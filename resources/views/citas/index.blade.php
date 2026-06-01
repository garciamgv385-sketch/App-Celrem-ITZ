@extends('layouts.app')

@section('content')
<style>
    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        border: 1px solid #dee2e6;
        border-radius: 12px;
        overflow: hidden;
        background: #fff;
    }

    .calendar-header {
        background: #f8f9fa;
        font-weight: 600;
        text-align: center;
        padding: 12px;
        border-bottom: 1px solid #dee2e6;
    }

    .calendar-day {
        min-height: 140px;
        padding: 10px;
        border-right: 1px solid #dee2e6;
        border-bottom: 1px solid #dee2e6;
    }

    .calendar-day:nth-child(7n) {
        border-right: none;
    }

    .day-muted {
        background: #f8f9fa;
        color: #adb5bd;
    }

    .appointment {
        font-size: 12px;
        border-radius: 8px;
        padding: 6px;
        margin-top: 6px;
        background: #e7f1ff;
        border-left: 4px solid #0d6efd;
    }

    .appointment-pending {
        background: #fff3cd;
        border-left-color: #ffc107;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Agenda de citas</h1>
        <p class="text-muted mb-0">
            Programa servicios, revisiones y mantenimientos del taller.
        </p>
    </div>

    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevaCita">
        Agendar cita
    </button>
</div>

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<div class="row g-4">
    <div class="col-lg-9">
        <div class="card shadow-sm border-0">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <a href="{{ route('citas.index', ['mes' => $mesAnterior]) }}" class="btn btn-outline-secondary">
                        Anterior
                    </a>

                    <h4 class="mb-0 text-capitalize">
                        {{ $fechaActual->translatedFormat('F Y') }}
                    </h4>

                    <a href="{{ route('citas.index', ['mes' => $mesSiguiente]) }}" class="btn btn-outline-secondary">
                        Siguiente
                    </a>
                </div>

                <div class="calendar-grid">
                    <div class="calendar-header">Lun</div>
                    <div class="calendar-header">Mar</div>
                    <div class="calendar-header">Mié</div>
                    <div class="calendar-header">Jue</div>
                    <div class="calendar-header">Vie</div>
                    <div class="calendar-header">Sáb</div>
                    <div class="calendar-header">Dom</div>

                    @foreach ($dias as $dia)
                        <div class="calendar-day {{ $dia->month !== $fechaActual->month ? 'day-muted' : '' }}">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-semibold">
                                    {{ $dia->day }}
                                </span>

                                @if ($dia->isToday())
                                    <span class="badge bg-primary">Hoy</span>
                                @endif
                            </div>

                            @foreach ($citas->get($dia->toDateString(), []) as $cita)
                                <div class="appointment {{ $cita['estado'] === 'Pendiente' ? 'appointment-pending' : '' }}">
                                    <div class="fw-semibold">
                                        {{ $cita['hora'] }} - {{ $cita['servicio'] }}
                                    </div>
                                    <div>{{ $cita['cliente'] }}</div>
                                    <div class="text-muted">{{ $cita['vehiculo'] }}</div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>

            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <h5 class="card-title">Resumen</h5>

                <div class="d-flex justify-content-between border-bottom py-2">
                    <span>Citas del mes</span>
                    <strong>{{ $citas->flatten(1)->count() }}</strong>
                </div>

                <div class="d-flex justify-content-between border-bottom py-2">
                    <span>Confirmadas</span>
                    <strong>{{ $citas->flatten(1)->where('estado', 'Confirmada')->count() }}</strong>
                </div>

                <div class="d-flex justify-content-between py-2">
                    <span>Pendientes</span>
                    <strong>{{ $citas->flatten(1)->where('estado', 'Pendiente')->count() }}</strong>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h5 class="card-title">Tipos de cita</h5>

                <span class="badge bg-primary mb-2">Confirmada</span>
                <span class="badge bg-warning text-dark mb-2">Pendiente</span>

                <p class="text-muted small mt-3 mb-0">
                    Esta vista servirá para controlar las citas del taller,
                    servicios programados y mantenimientos preventivos.
                </p>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalNuevaCita" tabindex="-1" aria-labelledby="modalNuevaCitaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('citas.store') }}" method="POST">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title" id="modalNuevaCitaLabel">Agendar nueva cita</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Cliente</label>
                            <input type="text" name="cliente" class="form-control" placeholder="Nombre del cliente" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Vehículo</label>
                            <input type="text" name="vehiculo" class="form-control" placeholder="Ej. Nissan Versa 2018" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Servicio</label>
                            <select name="servicio" class="form-select" required>
                                <option value="">Selecciona un servicio</option>
                                <option value="Cambio de aceite">Cambio de aceite</option>
                                <option value="Afinación">Afinación</option>
                                <option value="Revisión de frenos">Revisión de frenos</option>
                                <option value="Cambio de filtros">Cambio de filtros</option>
                                <option value="Diagnóstico general">Diagnóstico general</option>
                                <option value="Mantenimiento preventivo">Mantenimiento preventivo</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Fecha</label>
                            <input type="date" name="fecha" class="form-control" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Hora</label>
                            <input type="time" name="hora" class="form-control" required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Observaciones</label>
                            <textarea name="observaciones" class="form-control" rows="3" placeholder="Detalles adicionales de la cita"></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Cancelar
                    </button>

                    <button type="submit" class="btn btn-primary">
                        Guardar cita
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection