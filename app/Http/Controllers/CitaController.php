<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Cliente;
use App\Models\Vehiculo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
        $serviciosCita = $this->serviciosCita();
        $horariosCita = $this->horariosCita();

        return view('citas.index', compact(
            'fechaActual',
            'dias',
            'citas',
            'clientes',
            'vehiculos',
            'mesAnterior',
            'mesSiguiente',
            'serviciosCita',
            'horariosCita'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente_id' => ['required', 'exists:clientes,id'],
            'vehiculo_id' => [
                'nullable',
                Rule::exists('vehiculos', 'id')->where(function ($query) use ($request) {
                    $query->where('cliente_id', $request->input('cliente_id'));
                }),
            ],
            'servicio' => ['required', Rule::in(array_keys($this->serviciosCita()))],
            'fecha' => ['required', 'date'],
            'hora' => ['required', Rule::in($this->horariosCita())],
            'estado' => ['required', 'in:pendiente,confirmada,cancelada,atendida'],
            'observaciones' => ['nullable', 'string'],
        ]);

        if ($this->esDomingo($validated['fecha'])) {
            return back()
                ->withErrors([
                    'fecha' => 'Los domingos son días inhábiles. Selecciona otro día.',
                ])
                ->withInput();
        }

        if ($this->fechaHoraYaPaso($validated['fecha'], $validated['hora'])) {
            return back()
                ->withErrors([
                    'hora' => 'No se pueden agendar citas antes del día y hora actual.',
                ])
                ->withInput();
        }

        if ($this->horarioOcupado($validated['fecha'], $validated['hora'], $validated['servicio'])) {
            return back()
                ->withErrors([
                    'hora' => 'El horario seleccionado se cruza con una cita existente. Elige otro espacio disponible.',
                ])
                ->withInput();
        }

        Cita::create($validated);

        return redirect()
            ->route('citas.index', ['mes' => Carbon::parse($validated['fecha'])->format('Y-m')])
            ->with('success', 'Cita agendada correctamente.');
    }

    private function serviciosCita(): array
    {
        return [
            'Cambio de aceite' => 30,
            'Afinación' => 90,
            'Revisión de frenos' => 60,
            'Cambio de filtros' => 30,
            'Diagnóstico general' => 60,
            'Mantenimiento preventivo' => 120,
        ];
    }

    private function horariosCita(): array
    {
        $horarios = [];
        $hora = Carbon::createFromTime(8, 0);
        $fin = Carbon::createFromTime(18, 0);

        while ($hora->lt($fin)) {
            $horarios[] = $hora->format('H:i');
            $hora->addMinutes(30);
        }

        return $horarios;
    }

    private function esDomingo(string $fecha): bool
    {
        return Carbon::parse($fecha)->isSunday();
    }

    private function fechaHoraYaPaso(string $fecha, string $hora): bool
    {
        return Carbon::parse("{$fecha} {$hora}")->lte(now());
    }

    private function horarioOcupado(string $fecha, string $hora, string $servicio): bool
    {
        $servicios = $this->serviciosCita();
        $inicioNuevaCita = Carbon::parse("{$fecha} {$hora}");
        $finNuevaCita = $inicioNuevaCita->copy()->addMinutes($servicios[$servicio]);
        $cierre = Carbon::parse("{$fecha} 18:00");

        if ($finNuevaCita->gt($cierre)) {
            return true;
        }

        return Cita::where('fecha', $fecha)
            ->whereIn('estado', ['pendiente', 'confirmada'])
            ->get()
            ->contains(function (Cita $cita) use ($fecha, $inicioNuevaCita, $finNuevaCita, $servicios) {
                $inicioExistente = Carbon::parse("{$fecha} {$cita->hora}");
                $duracionExistente = $servicios[$cita->servicio] ?? 60;
                $finExistente = $inicioExistente->copy()->addMinutes($duracionExistente);

                return $inicioNuevaCita->lt($finExistente) && $finNuevaCita->gt($inicioExistente);
            });
    }
}
