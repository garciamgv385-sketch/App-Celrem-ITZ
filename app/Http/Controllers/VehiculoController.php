<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Vehiculo;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VehiculoController extends Controller
{
    public function index(Request $request)
    {
        $busqueda = $request->string('buscar')->trim()->toString();

        $vehiculos = Vehiculo::query()
            ->with('cliente')
            ->when($busqueda !== '', function ($query) use ($busqueda) {
                $query->where(function ($query) use ($busqueda) {
                    $query->where('modelo', 'like', "%{$busqueda}%")
                        ->orWhere('anio', 'like', "%{$busqueda}%")
                        ->orWhere('placas', 'like', "%{$busqueda}%")
                        ->orWhereHas('cliente', function ($query) use ($busqueda) {
                            $query->where('nombre', 'like', "%{$busqueda}%");
                        });
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('vehiculos.index', compact('vehiculos', 'busqueda'));
    }

    public function create()
    {
        $clientes = Cliente::orderBy('nombre')->get();

        return view('vehiculos.create', compact('clientes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente_id' => ['required', 'exists:clientes,id'],
            'tipo' => ['required', Rule::in(['moto', 'auto', 'servicio_pesado', 'camioneta', 'otro'])],
            'marca' => ['required', 'string', 'max:100'],
            'modelo' => ['required', 'string', 'max:100'],
            'anio' => ['required', 'integer', 'min:1900', 'max:'.((int) date('Y') + 1)],
            'placas' => ['required', 'string', 'max:20', 'unique:vehiculos,placas'],
            'kilometraje_actual' => ['required', 'integer', 'min:0'],
            'tipo_combustible' => ['required', Rule::in(['gasolina', 'diesel', 'hibrido', 'electrico', 'gas'])],
            'estado' => ['required', Rule::in(['activo', 'inactivo', 'en_servicio'])],
            'observaciones' => ['nullable', 'string'],
        ]);

        Vehiculo::create($validated);

        return redirect()
            ->route('vehiculos.index')
            ->with('success', 'Vehículo registrado correctamente.');
    }
}
