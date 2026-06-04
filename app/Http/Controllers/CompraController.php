<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\Producto;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompraController extends Controller
{
    private const CARRITO_KEY = 'compra_carrito';

    public function index()
    {
        $proveedores = Proveedor::where('estado', 'activo')
            ->orderBy('nombre')
            ->get();
        $productos = Producto::where('estado', 'activo')
            ->orderBy('nombre')
            ->get();
        $carrito = $this->carrito();
        $compras = Compra::with(['proveedor', 'detalles.producto'])
            ->latest('fecha')
            ->latest()
            ->paginate(10);
        $totalCarrito = collect($carrito)->sum('subtotal');

        return view('compras.index', compact('proveedores', 'productos', 'carrito', 'compras', 'totalCarrito'));
    }

    public function agregarProducto(Request $request)
    {
        $validated = $request->validate([
            'producto_id' => ['required', 'exists:productos,id'],
            'cantidad' => ['required', 'integer', 'min:1'],
            'precio_unitario' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
        ]);

        $producto = Producto::findOrFail($validated['producto_id']);
        $carrito = $this->carrito();
        $productoId = (string) $producto->id;
        $cantidad = $validated['cantidad'];

        if (isset($carrito[$productoId])) {
            $cantidad += $carrito[$productoId]['cantidad'];
        }

        $carrito[$productoId] = [
            'producto_id' => $producto->id,
            'nombre' => $producto->nombre,
            'sku' => $producto->sku,
            'unidad' => $producto->unidad,
            'cantidad' => $cantidad,
            'precio_unitario' => (float) $validated['precio_unitario'],
            'subtotal' => $cantidad * (float) $validated['precio_unitario'],
        ];

        session([self::CARRITO_KEY => $carrito]);

        return redirect()
            ->route('compras.index')
            ->with('success', 'Producto agregado al carrito.');
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

            $carrito[$productoId]['cantidad'] = (int) $item['cantidad'];
            $carrito[$productoId]['precio_unitario'] = (float) $item['precio_unitario'];
            $carrito[$productoId]['subtotal'] = (int) $item['cantidad'] * (float) $item['precio_unitario'];
        }

        session([self::CARRITO_KEY => $carrito]);

        return redirect()
            ->route('compras.index')
            ->with('success', 'Carrito actualizado.');
    }

    public function eliminarProducto(Producto $producto)
    {
        $carrito = $this->carrito();
        unset($carrito[(string) $producto->id]);
        session([self::CARRITO_KEY => $carrito]);

        return redirect()
            ->route('compras.index')
            ->with('success', 'Producto eliminado del carrito.');
    }

    public function vaciarCarrito()
    {
        session()->forget(self::CARRITO_KEY);

        return redirect()
            ->route('compras.index')
            ->with('success', 'Carrito vaciado.');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'proveedor_id' => ['required', 'exists:proveedores,id'],
            'fecha' => ['required', 'date'],
            'observaciones' => ['nullable', 'string'],
        ]);

        $carrito = $this->carrito();

        if (empty($carrito)) {
            return redirect()
                ->route('compras.index')
                ->withErrors(['carrito' => 'Agrega al menos un producto al carrito antes de guardar la compra.'])
                ->withInput();
        }

        DB::transaction(function () use ($validated, $carrito) {
            $compra = Compra::create([
                'proveedor_id' => $validated['proveedor_id'],
                'fecha' => $validated['fecha'],
                'subtotal' => collect($carrito)->sum('subtotal'),
                'estado' => 'registrada',
                'observaciones' => $validated['observaciones'] ?? null,
            ]);

            foreach ($carrito as $item) {
                $compra->detalles()->create([
                    'producto_id' => $item['producto_id'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio_unitario'],
                    'subtotal' => $item['subtotal'],
                ]);

                Producto::whereKey($item['producto_id'])->increment('existencia', $item['cantidad']);
                Producto::whereKey($item['producto_id'])->update(['precio_compra' => $item['precio_unitario']]);
            }
        });

        session()->forget(self::CARRITO_KEY);

        return redirect()
            ->route('compras.index')
            ->with('success', 'Compra registrada correctamente. El inventario fue actualizado.');
    }

    private function carrito(): array
    {
        return session(self::CARRITO_KEY, []);
    }
}
