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
        padding: 14px;
        border-bottom: 1px solid #dee2e6;
    }

    .calendar-day {
        min-height: 175px;
        padding: 12px;
        border-right: 1px solid #dee2e6;
        border-bottom: 1px solid #dee2e6;
        background: #fff;
    }

    .calendar-day:nth-child(7n) {
        border-right: none;
    }

    .day-muted {
        background: #f8f9fa;
        color: #adb5bd;
    }

    .day-disabled {
        background: #f8f9fa;
        color: #6c757d;
    }

    .appointment {
        width: 100%;
        font-size: 13px;
        text-align: left;
        border-radius: 8px;
        padding: 8px;
        margin-top: 8px;
        border-left: 4px solid #0d6efd;
        border-top: 0;
        border-right: 0;
        border-bottom: 0;
        color: #212529;
        cursor: pointer;
    }

    .appointment-pendiente { background: #fff3cd; border-left-color: #ffc107; }
    .appointment-confirmada { background: #e7f1ff; border-left-color: #0d6efd; }
    .appointment-atendida { background: #d1e7dd; border-left-color: #198754; }
    .appointment-cancelada { background: #f8d7da; border-left-color: #dc3545; }

    @media (max-width: 768px) {
        .calendar-grid { min-width: 900px; }
        .calendar-wrapper { overflow-x: auto; }
        .calendar-day { min-height: 150px; }
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Agenda de citas</h1>
        <p class="text-muted mb-0">Programa y gestiona citas del taller.</p>
    </div>

    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevaCita">
        Agendar cita
    </button>
</div>

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if ($errors->any())
    <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="{{ route('citas.index', ['mes' => $mesAnterior]) }}" class="btn btn-outline-secondary">Anterior</a>
            <h4 class="mb-0 text-capitalize">{{ $fechaActual->translatedFormat('F Y') }}</h4>
            <a href="{{ route('citas.index', ['mes' => $mesSiguiente]) }}" class="btn btn-outline-secondary">Siguiente</a>
        </div>

        <div class="calendar-wrapper">
            <div class="calendar-grid">
                <div class="calendar-header">Lun</div>
                <div class="calendar-header">Mar</div>
                <div class="calendar-header">Mié</div>
                <div class="calendar-header">Jue</div>
                <div class="calendar-header">Vie</div>
                <div class="calendar-header">Sáb</div>
                <div class="calendar-header">Dom</div>

                @foreach ($dias as $dia)
                    <div class="calendar-day {{ $dia->month !== $fechaActual->month ? 'day-muted' : '' }} {{ $dia->isSunday() ? 'day-disabled' : '' }}">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-semibold">{{ $dia->day }}</span>
                            <div class="d-flex gap-1">
                                @if ($dia->isToday())
                                    <span class="badge bg-primary">Hoy</span>
                                @endif
                                @if ($dia->isSunday())
                                    <span class="badge bg-secondary">Inhábil</span>
                                @endif
                            </div>
                        </div>

                        @foreach ($citas->get($dia->toDateString(), []) as $cita)
                            @php
                                $duracionCita = $serviciosCita[$cita->servicio] ?? 60;
                                $inicioCita = \Carbon\Carbon::parse($cita->fecha->toDateString() . ' ' . $cita->hora);
                                $finCita = $inicioCita->copy()->addMinutes($duracionCita);
                            @endphp
                            <button type="button" class="appointment appointment-{{ $cita->estado }}" data-bs-toggle="modal" data-bs-target="#modalCita{{ $cita->id }}">
                                <div class="fw-semibold">{{ $inicioCita->format('H:i') }} - {{ $finCita->format('H:i') }} · {{ $cita->servicio }}</div>
                                <div>{{ $cita->cliente->nombre }}</div>
                                <div class="text-muted">
                                    @if ($cita->vehiculo)
                                        {{ $cita->vehiculo->marca }} {{ $cita->vehiculo->modelo }} {{ $cita->vehiculo->placas ? '(' . $cita->vehiculo->placas . ')' : '' }}
                                    @else
                                        Sin vehículo
                                    @endif
                                </div>
                                <div class="mt-1">
                                    <span class="badge bg-secondary">{{ ucfirst($cita->estado) }}</span>
                                    <span class="badge bg-light text-dark">{{ $duracionCita }} min</span>
                                </div>
                            </button>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <h5 class="card-title mb-3">Resumen del mes</h5>
                <div class="row text-center">
                    <div class="col-md-3 border-end"><h4 class="mb-0">{{ $citas->flatten(1)->count() }}</h4><small class="text-muted">Total</small></div>
                    <div class="col-md-3 border-end"><h4 class="mb-0">{{ $citas->flatten(1)->where('estado', 'pendiente')->count() }}</h4><small class="text-muted">Pendientes</small></div>
                    <div class="col-md-3 border-end"><h4 class="mb-0">{{ $citas->flatten(1)->where('estado', 'confirmada')->count() }}</h4><small class="text-muted">Confirmadas</small></div>
                    <div class="col-md-3"><h4 class="mb-0">{{ $citas->flatten(1)->where('estado', 'atendida')->count() }}</h4><small class="text-muted">Atendidas</small></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <h5 class="card-title mb-3">Estados</h5>
                <div class="d-flex flex-wrap gap-2">
                    <span class="badge bg-warning text-dark px-3 py-2">Pendiente</span>
                    <span class="badge bg-primary px-3 py-2">Confirmada</span>
                    <span class="badge bg-success px-3 py-2">Atendida</span>
                    <span class="badge bg-danger px-3 py-2">Cancelada</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h5 class="card-title mb-0">Gestión de citas</h5>
                <p class="text-muted mb-0 small">Filtra, cambia estado o modifica los datos de cada cita.</p>
            </div>
        </div>

        <form method="GET" action="{{ route('citas.index') }}" class="row g-2 mb-3">
            <input type="hidden" name="mes" value="{{ $fechaActual->format('Y-m') }}">
            <div class="col-md-4">
                <input type="text" name="buscar" class="form-control" value="{{ $busqueda }}" placeholder="Buscar cliente, servicio, placas o vehículo">
            </div>
            <div class="col-md-3">
                <select name="estado" class="form-select">
                    <option value="">Todos los estados</option>
                    @foreach (['pendiente', 'confirmada', 'cancelada', 'atendida'] as $estado)
                        <option value="{{ $estado }}" @selected($estadoFiltro === $estado)>{{ ucfirst($estado) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <input type="date" name="fecha" class="form-control" value="{{ $fechaFiltro }}">
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-outline-primary">Filtrar</button>
                <a href="{{ route('citas.index', ['mes' => $fechaActual->format('Y-m')]) }}" class="btn btn-outline-secondary">Limpiar</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Cliente</th>
                        <th>Vehículo</th>
                        <th>Servicio</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($citasListado as $cita)
                        <tr>
                            <td>{{ $cita->fecha->format('d/m/Y') }}</td>
                            <td>{{ substr($cita->hora, 0, 5) }}</td>
                            <td>
                                <div class="fw-semibold">{{ $cita->cliente->nombre }}</div>
                                <div class="text-muted small">{{ $cita->cliente->telefono }}</div>
                            </td>
                            <td>
                                @if ($cita->vehiculo)
                                    {{ $cita->vehiculo->marca }} {{ $cita->vehiculo->modelo }}
                                    <div class="text-muted small">{{ $cita->vehiculo->placas ?: 'Sin placas' }}</div>
                                @else
                                    <span class="text-muted">Sin vehículo</span>
                                @endif
                            </td>
                            <td>{{ $cita->servicio }}</td>
                            <td><span class="badge bg-secondary">{{ ucfirst($cita->estado) }}</span></td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#modalCita{{ $cita->id }}">Ver</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalEstadoCita{{ $cita->id }}">Estado</button>
                                <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#modalEditarCita{{ $cita->id }}">Editar</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No hay citas con los filtros seleccionados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $citasListado->links() }}
        </div>
    </div>
</div>

@php
    $citasModales = $citas->flatten(1)->merge($citasListado->getCollection())->unique('id');
@endphp

<div class="modal fade" id="modalNuevaCita" tabindex="-1" aria-labelledby="modalNuevaCitaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('citas.store') }}" method="POST" data-cita-form>
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="modalNuevaCitaLabel">Agendar nueva cita</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    @include('citas.partials.form', ['cita' => null])
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar cita</button>
                </div>
            </form>
        </div>
    </div>
</div>

@foreach ($citasModales as $cita)
    @php
        $duracionCita = $serviciosCita[$cita->servicio] ?? 60;
        $inicioCita = \Carbon\Carbon::parse($cita->fecha->toDateString() . ' ' . $cita->hora);
        $finCita = $inicioCita->copy()->addMinutes($duracionCita);
    @endphp

    <div class="modal fade" id="modalCita{{ $cita->id }}" tabindex="-1" aria-labelledby="modalCitaLabel{{ $cita->id }}" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="modalCitaLabel{{ $cita->id }}">{{ $cita->servicio }}</h5>
                        <span class="badge bg-secondary">{{ ucfirst($cita->estado) }}</span>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <p class="text-muted mb-1">Fecha y hora</p>
                            <p class="fw-semibold mb-0">{{ $cita->fecha->translatedFormat('d F Y') }}</p>
                            <p class="mb-0">{{ $inicioCita->format('H:i') }} - {{ $finCita->format('H:i') }} ({{ $duracionCita }} min)</p>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted mb-1">Cliente</p>
                            <p class="fw-semibold mb-0">{{ $cita->cliente->nombre }}</p>
                            <p class="mb-0">{{ $cita->cliente->telefono }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted mb-1">Vehículo</p>
                            @if ($cita->vehiculo)
                                <p class="fw-semibold mb-0">{{ $cita->vehiculo->marca }} {{ $cita->vehiculo->modelo }}</p>
                                <p class="mb-0">Placas: {{ $cita->vehiculo->placas ?: 'Sin placas' }}</p>
                            @else
                                <p class="fw-semibold mb-0">Sin vehículo asignado</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted mb-1">Observaciones</p>
                            <p class="mb-0">{{ $cita->observaciones ?: 'Sin observaciones' }}</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEstadoCita{{ $cita->id }}" tabindex="-1" aria-labelledby="modalEstadoCitaLabel{{ $cita->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('citas.estado', $cita) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalEstadoCitaLabel{{ $cita->id }}">Cambiar estado</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-3">{{ $cita->cliente->nombre }} · {{ $cita->servicio }}</p>
                        <label class="form-label">Estado</label>
                        <select name="estado" class="form-select" required>
                            @foreach (['pendiente', 'confirmada', 'cancelada', 'atendida'] as $estado)
                                <option value="{{ $estado }}" @selected($cita->estado === $estado)>{{ ucfirst($estado) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar estado</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEditarCita{{ $cita->id }}" tabindex="-1" aria-labelledby="modalEditarCitaLabel{{ $cita->id }}" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('citas.update', $cita) }}" method="POST" data-cita-form>
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalEditarCitaLabel{{ $cita->id }}">Editar cita</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        @include('citas.partials.form', ['cita' => $cita])
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const formatoFechaLocal = function (fecha) {
            const mes = String(fecha.getMonth() + 1).padStart(2, '0');
            const dia = String(fecha.getDate()).padStart(2, '0');
            return `${fecha.getFullYear()}-${mes}-${dia}`;
        };

        document.querySelectorAll('[data-cita-form]').forEach(function (form) {
            const cliente = form.querySelector('[data-cita-cliente]');
            const vehiculo = form.querySelector('[data-cita-vehiculo]');
            const servicio = form.querySelector('[data-cita-servicio]');
            const duracion = form.querySelector('[data-cita-duracion]');
            const fecha = form.querySelector('[data-cita-fecha]');
            const hora = form.querySelector('[data-cita-hora]');

            if (servicio && duracion) {
                const actualizarDuracion = function () {
                    const opcion = servicio.options[servicio.selectedIndex];
                    const minutos = opcion ? opcion.dataset.duration : null;
                    duracion.textContent = minutos ? `Este servicio apartará ${minutos} minutos.` : 'Selecciona un servicio para ver la duración estimada.';
                };
                servicio.addEventListener('change', actualizarDuracion);
                actualizarDuracion();
            }

            if (cliente && vehiculo) {
                const opcionesVehiculos = Array.from(vehiculo.options).slice(1).map((option) => option.cloneNode(true));
                const vehiculoInicial = vehiculo.value;

                const filtrarVehiculos = function () {
                    const clienteId = cliente.value;
                    const valorActual = vehiculo.value || vehiculoInicial;
                    vehiculo.innerHTML = '';
                    vehiculo.append(new Option('Sin vehículo asignado', ''));
                    vehiculo.disabled = !clienteId;

                    opcionesVehiculos
                        .filter((option) => option.dataset.clienteId === clienteId)
                        .forEach((option) => {
                            const nuevaOpcion = option.cloneNode(true);
                            nuevaOpcion.selected = nuevaOpcion.value === valorActual;
                            vehiculo.append(nuevaOpcion);
                        });
                };

                cliente.addEventListener('change', filtrarVehiculos);
                filtrarVehiculos();
            }

            if (fecha && hora) {
                const actualizarDisponibilidad = function () {
                    const ahora = new Date();
                    const hoy = formatoFechaLocal(ahora);
                    const horaActual = `${String(ahora.getHours()).padStart(2, '0')}:${String(ahora.getMinutes()).padStart(2, '0')}`;
                    const fechaSeleccionada = fecha.value;
                    const fechaComoDate = fechaSeleccionada ? new Date(`${fechaSeleccionada}T00:00:00`) : null;
                    const esDomingo = fechaComoDate ? fechaComoDate.getDay() === 0 : false;
                    const esPasado = fechaSeleccionada && fechaSeleccionada < hoy;

                    fecha.setCustomValidity('');
                    if (esDomingo) {
                        fecha.setCustomValidity('Los domingos son días inhábiles.');
                    } else if (esPasado) {
                        fecha.setCustomValidity('No se pueden agendar citas en fechas pasadas.');
                    }

                    Array.from(hora.options).forEach((option) => {
                        if (!option.value) {
                            return;
                        }
                        option.disabled = esDomingo || esPasado || (fechaSeleccionada === hoy && option.value <= horaActual);
                    });

                    if (hora.selectedOptions.length && hora.selectedOptions[0].disabled) {
                        hora.value = '';
                    }
                };

                fecha.addEventListener('change', actualizarDisponibilidad);
                actualizarDisponibilidad();
            }
        });
    });
</script>

@if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modalNuevaCita = document.getElementById('modalNuevaCita');
            if (modalNuevaCita) {
                new bootstrap.Modal(modalNuevaCita).show();
            }
        });
    </script>
@endif
@endsection
