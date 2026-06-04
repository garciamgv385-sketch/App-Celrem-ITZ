<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VentaController extends Controller
{
    private const CARRITO_KEY = 'venta_carrito';

    public function index(Request $request)
    {
        $busqueda = $request->string('buscar_producto')->trim()->toString();
        $productos = Producto::query()
            ->where('estado', 'activo')
            ->where('existencia', '>', 0)
            ->when($busqueda !== '', function ($query) use ($busqueda) {
                $query->where(function ($query) use ($busqueda) {
                    $query->where('nombre', 'like', "%{$busqueda}%")
                        ->orWhere('sku', 'like', "%{$busqueda}%")
                        ->orWhere('categoria', 'like', "%{$busqueda}%")
                        ->orWhere('marca', 'like', "%{$busqueda}%");
                });
            })
            ->orderBy('nombre')
            ->limit(25)
            ->get();

        $clientes = Cliente::where('estado', 'activo')
            ->orderBy('nombre')
            ->get();
        $carrito = $this->carrito();
        $ventas = Venta::with(['cliente', 'detalles.producto'])
            ->latest('fecha')
            ->latest()
            ->paginate(10);
        $totalCarrito = collect($carrito)->sum('subtotal');

        return view('ventas.index', compact('productos', 'clientes', 'carrito', 'ventas', 'totalCarrito', 'busqueda'));
    }

    public function agregarProducto(Request $request)
    {
        $validated = $request->validate([
            'producto_id' => ['required', 'exists:productos,id'],
            'cantidad' => ['required', 'integer', 'min:1'],
            'precio_unitario' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
        ]);

        $producto = Producto::where('estado', 'activo')->findOrFail($validated['producto_id']);

        if ($producto->existencia < $validated['cantidad']) {
            return redirect()
                ->route('ventas.index')
                ->withErrors(['cantidad' => 'No hay existencia suficiente para agregar esa cantidad al carrito.'])
                ->withInput();
        }

        $carrito = $this->carrito();
        $productoId = (string) $producto->id;
        $cantidad = $validated['cantidad'];

        if (isset($carrito[$productoId])) {
            $cantidad += $carrito[$productoId]['cantidad'];
        }

        if ($producto->existencia < $cantidad) {
            return redirect()
                ->route('ventas.index')
                ->withErrors(['cantidad' => 'La cantidad total en el carrito supera la existencia disponible.'])
                ->withInput();
        }

        $carrito[$productoId] = [
            'producto_id' => $producto->id,
            'nombre' => $producto->nombre,
            'sku' => $producto->sku,
            'categoria' => $producto->categoria,
            'marca' => $producto->marca,
            'unidad' => $producto->unidad,
            'existencia' => $producto->existencia,
            'cantidad' => $cantidad,
            'precio_unitario' => (float) $validated['precio_unitario'],
            'subtotal' => $cantidad * (float) $validated['precio_unitario'],
        ];

        session([self::CARRITO_KEY => $carrito]);

        return redirect()
            ->route('ventas.index')
            ->with('success', 'Producto agregado al carrito de venta.');
    }

    public function actualizarCarrito(Request $request)
    {
        $validated = $request->validate([
            'items' => ['required', 'array'],
            'items.*.cantidad' => ['required', 'integer', 'min:1'],
            'items.*.precio_unitario' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
        ]);

        $carrito = $this->carrito();

        foreach ($validated['items'] as $productoId => $item) {
            if (!isset($carrito[$productoId])) {
                continue;
            }

            $producto = Producto::findOrFail($productoId);

            if ($producto->existencia < (int) $item['cantidad']) {
                return redirect()
                    ->route('ventas.index')
                    ->withErrors(['items' => "No hay existencia suficiente para {$producto->nombre}."])
                    ->withInput();
            }

            $carrito[$productoId]['existencia'] = $producto->existencia;
            $carrito[$productoId]['cantidad'] = (int) $item['cantidad'];
            $carrito[$productoId]['precio_unitario'] = (float) $item['precio_unitario'];
            $carrito[$productoId]['subtotal'] = (int) $item['cantidad'] * (float) $item['precio_unitario'];
        }

        session([self::CARRITO_KEY => $carrito]);

        return redirect()
            ->route('ventas.index')
            ->with('success', 'Carrito de venta actualizado.');
    }

    public function eliminarProducto(Producto $producto)
    {
        $carrito = $this->carrito();
        unset($carrito[(string) $producto->id]);
        session([self::CARRITO_KEY => $carrito]);

        return redirect()
            ->route('ventas.index')
            ->with('success', 'Producto retirado del carrito.');
    }

    public function vaciarCarrito()
    {
        session()->forget(self::CARRITO_KEY);

        return redirect()
            ->route('ventas.index')
            ->with('success', 'Carrito de venta vaciado.');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente_id' => ['nullable', 'exists:clientes,id'],
            'fecha' => ['required', 'date'],
            'observaciones' => ['nullable', 'string'],
        ]);

        $carrito = $this->carrito();

        if (empty($carrito)) {
            return redirect()
                ->route('ventas.index')
                ->withErrors(['carrito' => 'Agrega al menos un producto al carrito antes de guardar la venta.'])
                ->withInput();
        }

        DB::transaction(function () use ($validated, $carrito) {
            foreach ($carrito as $item) {
                $producto = Producto::lockForUpdate()->findOrFail($item['producto_id']);

                if ($producto->existencia < $item['cantidad']) {
                    throw new \RuntimeException("No hay existencia suficiente para {$producto->nombre}.");
                }
            }

            $venta = Venta::create([
                'cliente_id' => $validated['cliente_id'] ?? null,
                'fecha' => $validated['fecha'],
                'subtotal' => collect($carrito)->sum('subtotal'),
                'estado' => 'registrada',
                'observaciones' => $validated['observaciones'] ?? null,
            ]);

            foreach ($carrito as $item) {
                $venta->detalles()->create([
                    'producto_id' => $item['producto_id'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio_unitario'],
                    'subtotal' => $item['subtotal'],
                ]);

                Producto::whereKey($item['producto_id'])->decrement('existencia', $item['cantidad']);
            }
        });

        session()->forget(self::CARRITO_KEY);

        return redirect()
            ->route('ventas.index')
            ->with('success', 'Venta registrada correctamente. El inventario fue actualizado.');
    }

    private function carrito(): array
    {
        return session(self::CARRITO_KEY, []);
    }
}
