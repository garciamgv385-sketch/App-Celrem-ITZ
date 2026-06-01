<?php

namespace App\Http\Controllers;

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

        $citas = collect([
            [
                'fecha' => $inicioMes->copy()->addDays(2)->toDateString(),
                'hora' => '09:00',
                'cliente' => 'Juan Pérez',
                'vehiculo' => 'Nissan Versa',
                'servicio' => 'Cambio de aceite',
                'estado' => 'Confirmada',
            ],
            [
                'fecha' => $inicioMes->copy()->addDays(5)->toDateString(),
                'hora' => '12:30',
                'cliente' => 'María González',
                'vehiculo' => 'Chevrolet Aveo',
                'servicio' => 'Revisión de frenos',
                'estado' => 'Pendiente',
            ],
            [
                'fecha' => $inicioMes->copy()->addDays(10)->toDateString(),
                'hora' => '16:00',
                'cliente' => 'Carlos Ramírez',
                'vehiculo' => 'Volkswagen Jetta',
                'servicio' => 'Afinación',
                'estado' => 'Confirmada',
            ],
        ])->groupBy('fecha');

        $mesAnterior = $fechaActual->copy()->subMonth()->format('Y-m');
        $mesSiguiente = $fechaActual->copy()->addMonth()->format('Y-m');

        return view('citas.index', compact(
            'fechaActual',
            'dias',
            'citas',
            'mesAnterior',
            'mesSiguiente'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cliente' => ['required', 'string', 'max:100'],
            'vehiculo' => ['required', 'string', 'max:100'],
            'servicio' => ['required', 'string', 'max:100'],
            'fecha' => ['required', 'date'],
            'hora' => ['required'],
            'observaciones' => ['nullable', 'string'],
        ]);

        return redirect()
            ->route('citas.index')
            ->with('success', 'Cita agendada correctamente. Después se conectará con la base de datos.');
    }
}