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
        $busqueda = $request->string('buscar')->trim()->toString();
        $estadoFiltro = $request->string('estado')->trim()->toString();
        $fechaFiltro = $request->string('fecha')->trim()->toString();

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
            ->when($request->user()->esCliente(), fn ($query) => $query->where('cliente_id', $request->user()->cliente_id))
            ->whereBetween('fecha', [
                $inicioCalendario->toDateString(),
                $finCalendario->toDateString(),
            ])
            ->orderBy('fecha')
            ->orderBy('hora')
            ->get()
            ->groupBy(fn (Cita $cita) => $cita->fecha->toDateString());

        $citasListado = Cita::query()
            ->with(['cliente', 'vehiculo'])
            ->when($request->user()->esCliente(), fn ($query) => $query->where('cliente_id', $request->user()->cliente_id))
            ->when($busqueda !== '', function ($query) use ($busqueda) {
                $query->where(function ($query) use ($busqueda) {
                    $query->where('servicio', 'like', "%{$busqueda}%")
                        ->orWhere('observaciones', 'like', "%{$busqueda}%")
                        ->orWhereHas('cliente', function ($query) use ($busqueda) {
                            $query->where('nombre', 'like', "%{$busqueda}%")
                                ->orWhere('telefono', 'like', "%{$busqueda}%");
                        })
                        ->orWhereHas('vehiculo', function ($query) use ($busqueda) {
                            $query->where('marca', 'like', "%{$busqueda}%")
                                ->orWhere('modelo', 'like', "%{$busqueda}%")
                                ->orWhere('placas', 'like', "%{$busqueda}%");
                        });
                });
            })
            ->when($estadoFiltro !== '', fn ($query) => $query->where('estado', $estadoFiltro))
            ->when($fechaFiltro !== '', fn ($query) => $query->whereDate('fecha', $fechaFiltro))
            ->orderByDesc('fecha')
            ->orderBy('hora')
            ->paginate(10)
            ->withQueryString();

        $clientes = $request->user()->esCliente()
            ? Cliente::whereKey($request->user()->cliente_id)->get()
            : Cliente::where('estado', 'activo')->orderBy('nombre')->get();
        $vehiculos = Vehiculo::with('cliente')
            ->when($request->user()->esCliente(), fn ($query) => $query->where('cliente_id', $request->user()->cliente_id))
            ->latest()
            ->get();
        $mesAnterior = $fechaActual->copy()->subMonth()->format('Y-m');
        $mesSiguiente = $fechaActual->copy()->addMonth()->format('Y-m');
        $serviciosCita = $this->serviciosCita();
        $horariosCita = $this->horariosCita();
        $bloquesOcupados = $this->bloquesOcupados($request);

        return view('citas.index', compact(
            'fechaActual',
            'dias',
            'citas',
            'citasListado',
            'clientes',
            'vehiculos',
            'mesAnterior',
            'mesSiguiente',
            'serviciosCita',
            'horariosCita',
            'bloquesOcupados',
            'busqueda',
            'estadoFiltro',
            'fechaFiltro'
        ));
    }

    public function store(Request $request)
    {
        if ($request->user()->esCliente()) {
            $request->merge(['cliente_id' => $request->user()->cliente_id]);
        }

        $validated = $this->validatedData($request);

        if ($error = $this->validarDisponibilidad($validated)) {
            return back()->withErrors($error)->withInput();
        }

        Cita::create($validated);

        return redirect()
            ->route('citas.index', ['mes' => Carbon::parse($validated['fecha'])->format('Y-m')])
            ->with('success', 'Cita agendada correctamente.');
    }

    public function update(Request $request, Cita $cita)
    {
        abort_if($request->user()->esCliente() && $cita->cliente_id !== $request->user()->cliente_id, 403);

        if ($request->user()->esCliente()) {
            $request->merge(['cliente_id' => $request->user()->cliente_id]);
        }

        $validated = $this->validatedData($request);

        if ($error = $this->validarDisponibilidad($validated, $cita)) {
            return back()->withErrors($error)->withInput();
        }

        $cita->update($validated);

        return redirect()
            ->route('citas.index', ['mes' => Carbon::parse($validated['fecha'])->format('Y-m')])
            ->with('success', 'Cita actualizada correctamente.');
    }

    public function updateEstado(Request $request, Cita $cita)
    {
        abort_if($request->user()->esCliente() && $cita->cliente_id !== $request->user()->cliente_id, 403);

        $validated = $request->validate([
            'estado' => ['required', Rule::in(['pendiente', 'confirmada', 'cancelada', 'atendida'])],
        ]);

        $cita->update($validated);

        return redirect()
            ->route('citas.index')
            ->with('success', 'Estado de la cita actualizado correctamente.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
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
            'estado' => ['required', Rule::in(['pendiente', 'confirmada', 'cancelada', 'atendida'])],
            'observaciones' => ['nullable', 'string'],
        ]);
    }

    private function validarDisponibilidad(array $validated, ?Cita $cita = null): ?array
    {
        if ($this->esDomingo($validated['fecha'])) {
            return ['fecha' => 'Los domingos son días inhábiles. Selecciona otro día.'];
        }

        if ($this->fechaHoraYaPaso($validated['fecha'], $validated['hora'])) {
            return ['hora' => 'No se pueden agendar citas antes del día y hora actual.'];
        }

        if ($this->horarioOcupado($validated['fecha'], $validated['hora'], $validated['servicio'], $cita)) {
            return ['hora' => 'El horario seleccionado se cruza con una cita existente. Elige otro espacio disponible.'];
        }

        return null;
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

    private function bloquesOcupados(Request $request): array
    {
        $servicios = $this->serviciosCita();

        return Cita::query()
            ->whereIn('estado', ['pendiente', 'confirmada'])
            ->whereDate('fecha', '>=', now()->toDateString())
            ->whereDate('fecha', '<=', now()->copy()->addYear()->toDateString())
            ->orderBy('fecha')
            ->orderBy('hora')
            ->get(['id', 'cliente_id', 'fecha', 'hora', 'servicio'])
            ->map(function (Cita $cita) use ($request, $servicios) {
                $inicio = Carbon::parse($cita->fecha->toDateString() . ' ' . $cita->hora);
                $duracion = $servicios[$cita->servicio] ?? 60;
                $puedeIdentificarCita = ! $request->user()->esCliente()
                    || $cita->cliente_id === $request->user()->cliente_id;

                return [
                    'cita_id' => $puedeIdentificarCita ? $cita->id : null,
                    'fecha' => $cita->fecha->toDateString(),
                    'inicio' => $inicio->format('H:i'),
                    'fin' => $inicio->copy()->addMinutes($duracion)->format('H:i'),
                ];
            })
            ->values()
            ->all();
    }

    private function esDomingo(string $fecha): bool
    {
        return Carbon::parse($fecha)->isSunday();
    }

    private function fechaHoraYaPaso(string $fecha, string $hora): bool
    {
        return Carbon::parse("{$fecha} {$hora}")->lte(now());
    }

    private function horarioOcupado(string $fecha, string $hora, string $servicio, ?Cita $citaIgnorada = null): bool
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
            ->when($citaIgnorada, fn ($query) => $query->whereKeyNot($citaIgnorada->id))
            ->get()
            ->contains(function (Cita $cita) use ($fecha, $inicioNuevaCita, $finNuevaCita, $servicios) {
                $inicioExistente = Carbon::parse("{$fecha} {$cita->hora}");
                $duracionExistente = $servicios[$cita->servicio] ?? 60;
                $finExistente = $inicioExistente->copy()->addMinutes($duracionExistente);

                return $inicioNuevaCita->lt($finExistente) && $finNuevaCita->gt($inicioExistente);
            });
    }
}
