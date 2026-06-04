<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProveedorController extends Controller
{
    public function index(Request $request)
    {
        $busqueda = $request->string('buscar')->trim()->toString();

        $proveedores = Proveedor::query()
            ->when($busqueda !== '', function ($query) use ($busqueda) {
                $query->where(function ($query) use ($busqueda) {
                    $query->where('nombre', 'like', "%{$busqueda}%")
                        ->orWhere('rfc', 'like', "%{$busqueda}%")
                        ->orWhere('telefono', 'like', "%{$busqueda}%")
                        ->orWhere('correo', 'like', "%{$busqueda}%")
                        ->orWhere('contacto', 'like', "%{$busqueda}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('proveedores.index', compact('proveedores', 'busqueda'));
    }

    public function store(Request $request)
    {
        Proveedor::create($this->validatedData($request));

        return redirect()
            ->route('proveedores.index')
            ->with('success', 'Proveedor registrado correctamente.');
    }

    public function update(Request $request, Proveedor $proveedor)
    {
        $proveedor->update($this->validatedData($request, $proveedor));

        return redirect()
            ->route('proveedores.index')
            ->with('success', 'Proveedor actualizado correctamente.');
    }

    public function destroy(Proveedor $proveedor)
    {
        $proveedor->update(['estado' => 'inactivo']);

        return redirect()
            ->route('proveedores.index')
            ->with('success', 'Proveedor marcado como inactivo.');
    }

    private function validatedData(Request $request, ?Proveedor $proveedor = null): array
    {
        $rfcRule = Rule::unique('proveedores', 'rfc');

        if ($proveedor) {
            $rfcRule->ignore($proveedor);
        }

        return $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'rfc' => ['nullable', 'string', 'max:20', $rfcRule],
            'telefono' => ['nullable', 'string', 'max:30'],
            'correo' => ['nullable', 'email', 'max:255'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'contacto' => ['nullable', 'string', 'max:255'],
            'estado' => ['required', Rule::in(['activo', 'inactivo'])],
            'observaciones' => ['nullable', 'string'],
        ]);
    }
}
