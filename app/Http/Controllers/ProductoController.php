<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductoController extends Controller
{
    public function index(Request $request)
    {
        $busqueda = $request->string('buscar')->trim()->toString();
        $categoria = $request->string('categoria')->trim()->toString();

        $productosBase = Producto::query();

        $productos = (clone $productosBase)
            ->when($busqueda !== '', function ($query) use ($busqueda) {
                $query->where(function ($query) use ($busqueda) {
                    $query->where('nombre', 'like', "%{$busqueda}%")
                        ->orWhere('categoria', 'like', "%{$busqueda}%")
                        ->orWhere('marca', 'like', "%{$busqueda}%")
                        ->orWhere('sku', 'like', "%{$busqueda}%")
                        ->orWhere('proveedor', 'like', "%{$busqueda}%");
                });
            })
            ->when($categoria !== '', function ($query) use ($categoria) {
                $query->where('categoria', $categoria);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $categorias = Producto::query()
            ->select('categoria')
            ->distinct()
            ->orderBy('categoria')
            ->pluck('categoria');

        $resumen = [
            'total' => (clone $productosBase)->count(),
            'stock_bajo' => (clone $productosBase)
                ->where('estado', 'activo')
                ->whereColumn('existencia', '<=', 'stock_minimo')
                ->where('existencia', '>', 0)
                ->count(),
            'sin_existencia' => (clone $productosBase)
                ->where('estado', 'activo')
                ->where('existencia', 0)
                ->count(),
            'valor' => (clone $productosBase)
                ->where('estado', 'activo')
                ->get()
                ->sum(fn (Producto $producto) => $producto->valorInventario()),
        ];

        return view('inventario.index', compact('productos', 'categorias', 'resumen', 'busqueda', 'categoria'));
    }

    public function store(Request $request)
    {
        Producto::create($this->validatedData($request));

        return redirect()
            ->route('inventario.index')
            ->with('success', 'Producto registrado correctamente.');
    }

    public function update(Request $request, Producto $producto)
    {
        $producto->update($this->validatedData($request, $producto));

        return redirect()
            ->route('inventario.index')
            ->with('success', 'Producto actualizado correctamente.');
    }

    private function validatedData(Request $request, ?Producto $producto = null): array
    {
        $skuRule = Rule::unique('productos', 'sku');

        if ($producto) {
            $skuRule->ignore($producto);
        }

        return $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'categoria' => ['required', 'string', 'max:100'],
            'marca' => ['nullable', 'string', 'max:100'],
            'sku' => ['nullable', 'string', 'max:80', $skuRule],
            'unidad' => ['required', 'string', 'max:40'],
            'existencia' => ['required', 'integer', 'min:0'],
            'stock_minimo' => ['required', 'integer', 'min:0'],
            'precio_compra' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'precio_venta' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'proveedor' => ['nullable', 'string', 'max:255'],
            'ubicacion' => ['nullable', 'string', 'max:255'],
            'estado' => ['required', Rule::in(['activo', 'inactivo'])],
            'observaciones' => ['nullable', 'string'],
        ]);
    }
}
