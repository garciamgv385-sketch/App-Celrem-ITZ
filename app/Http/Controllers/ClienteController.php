<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        abort_if($request->user()->esCliente(), 403);

        $busqueda = $request->string('buscar')->trim()->toString();

        $clientes = Cliente::query()
            ->when($request->user()->esMecanico(), fn ($query) => $query->where('estado', 'activo'))
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
        abort_unless(request()->user()->esAdmin(), 403);

        return view('clientes.create');
    }

    public function store(Request $request)
    {
        abort_unless($request->user()->esAdmin(), 403);

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
        return redirect()->route('clientes.index');
    }

    public function edit(Cliente $cliente)
    {
        return redirect()->route('clientes.index');
    }

    public function update(Request $request, Cliente $cliente)
    {
        abort_unless($request->user()->esAdmin(), 403);

        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'telefono' => ['required', 'string', 'max:30'],
            'correo' => ['nullable', 'email', 'max:255'],
            'estado' => ['required', 'in:activo,inactivo'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'observaciones' => ['nullable', 'string'],
        ]);

        $cliente->update($validated);

        return redirect()
            ->route('clientes.index')
            ->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy(Cliente $cliente)
    {
        abort_unless(request()->user()->esAdmin(), 403);

        $cliente->update(['estado' => 'inactivo']);

        return redirect()
            ->route('clientes.index')
            ->with('success', 'Cliente marcado como inactivo.');
    }
}
