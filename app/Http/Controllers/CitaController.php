<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Cliente;
use App\Models\Vehiculo;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CitaController extends Controller
{
    public function index(Request $request)
    {
        $mes = $request->query('mes', now()->format('Y-m'));

        $fechaActual = Carbon::createFromFormat('Y-m-d', $mes . '-01');
        $inicioMes = $fechaActual->copy()->startOfMonth();
        $finMes = $fechaActual->copy()->endOfMonth();

        $inicioCalendario = $inicioMes->copy()->startOfWeek(Carbon::MONDAY);
        $finCalendario = $finMes->copy()->endOfWeek(Carbon::SUNDAY);

        $dias = [];
        $dia = $inicioCalendario->copy();

        while ($dia->lte($finCalendario)) {
            $dias[] = $dia->copy();
            $dia->addDay();
        }

        $citas = Cita::with(['cliente', 'vehiculo'])
            ->whereBetween('fecha', [
                $inicioCalendario->toDateString(),
                $finCalendario->toDateString()
            ])
            ->orderBy('fecha')
            ->orderBy('hora')
            ->get()
            ->groupBy(function ($cita) {
                return $cita->fecha->toDateString();
            });

        $clientes = Cliente::where('estado', 'activo')
            ->orderBy('nombre')
            ->get();

        $vehiculos = Vehiculo::with('cliente')
            ->latest()
            ->get();

        $mesAnterior = $fechaActual->copy()->subMonth()->format('Y-m');
        $mesSiguiente = $fechaActual->copy()->addMonth()->format('Y-m');

        return view('citas.index', compact(
            'fechaActual',
            'dias',
            'citas',
            'clientes',
            'vehiculos',
            'mesAnterior',
            'mesSiguiente'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente_id' => ['required', 'exists:clientes,id'],
            'vehiculo_id' => ['nullable', 'exists:vehiculos,id'],
            'servicio' => ['required', 'string', 'max:255'],
            'fecha' => ['required', 'date'],
            'hora' => ['required'],
            'estado' => ['required', 'in:pendiente,confirmada,cancelada,atendida'],
            'observaciones' => ['nullable', 'string'],
        ]);

        $citaExistente = Cita::where('fecha', $validated['fecha'])
            ->where('hora', $validated['hora'])
            ->whereIn('estado', ['pendiente', 'confirmada'])
            ->exists();

        if ($citaExistente) {
            return back()
                ->withErrors([
                    'hora' => 'Ya existe una cita agendada en esa fecha y hora.',
                ])
                ->withInput();
        }

        Cita::create($validated);

        return redirect()
            ->route('citas.index', ['mes' => Carbon::parse($validated['fecha'])->format('Y-m')])
            ->with('success', 'Cita agendada correctamente.');
    }
}