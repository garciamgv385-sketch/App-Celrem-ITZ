<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $busqueda = $request->string('buscar')->trim()->toString();

        $clientes = Cliente::query()
            ->when($busqueda !== '', function ($query) use ($busqueda) {
                $query->where(function ($query) use ($busqueda) {
                    $query->where('nombre', 'like', "%{$busqueda}%")
                        ->orWhere('telefono', 'like', "%{$busqueda}%")
                        ->orWhere('correo', 'like', "%{$busqueda}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('clientes.index', compact('clientes', 'busqueda'));
    }

    public function create()
    {
        return view('clientes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'telefono' => ['required', 'string', 'max:30'],
            'correo' => ['nullable', 'email', 'max:255'],
            'estado' => ['required', 'in:activo,inactivo'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'observaciones' => ['nullable', 'string'],
        ]);

        Cliente::create($validated);

        return redirect()
            ->route('clientes.index')
            ->with('success', 'Cliente registrado correctamente.');
    }

    public function show(Cliente $cliente)
    {
        return view('clientes.show', compact('cliente'));
    }

    public function edit(string $id)
    {
        return view('clientes.edit');
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
}
