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
        background: #e7f1ff;
        border-left: 4px solid #0d6efd;
        border-top: 0;
        border-right: 0;
        border-bottom: 0;
        color: #212529;
        cursor: pointer;
    }

    .appointment:hover,
    .appointment:focus {
        filter: brightness(0.97);
        outline: 2px solid rgba(13, 110, 253, 0.25);
        outline-offset: 2px;
    }

    .appointment-pendiente {
        background: #fff3cd;
        border-left-color: #ffc107;
    }

    .appointment-confirmada {
        background: #e7f1ff;
        border-left-color: #0d6efd;
    }

    .appointment-atendida {
        background: #d1e7dd;
        border-left-color: #198754;
    }

    .appointment-cancelada {
        background: #f8d7da;
        border-left-color: #dc3545;
    }

    .summary-card {
        height: 100%;
    }

    @media (max-width: 768px) {
        .calendar-grid {
            min-width: 900px;
        }

        .calendar-wrapper {
            overflow-x: auto;
        }

        .calendar-day {
            min-height: 150px;
        }
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

@if ($errors->any())
    <div class="alert alert-danger">
        {{ $errors->first() }}
    </div>
@endif

<div class="card shadow-sm border-0 mb-4">
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
                            <span class="fw-semibold">
                                {{ $dia->day }}
                            </span>

                            @if ($dia->isToday())
                                <span class="badge bg-primary">Hoy</span>
                            @endif

                            @if ($dia->isSunday())
                                <span class="badge bg-secondary">Inhábil</span>
                            @endif
                        </div>

                        @foreach ($citas->get($dia->toDateString(), []) as $cita)
                            <button
                                type="button"
                                class="appointment appointment-{{ $cita->estado }}"
                                data-bs-toggle="modal"
                                data-bs-target="#modalCita{{ $cita->id }}"
                            >
                                <div class="fw-semibold">
                                    @php
                                        $duracionCita = $serviciosCita[$cita->servicio] ?? 60;
                                        $inicioCita = \Carbon\Carbon::parse($cita->fecha->toDateString() . ' ' . $cita->hora);
                                        $finCita = $inicioCita->copy()->addMinutes($duracionCita);
                                    @endphp

                                    {{ $inicioCita->format('H:i') }} - {{ $finCita->format('H:i') }} · {{ $cita->servicio }}
                                </div>

                                <div>
                                    {{ $cita->cliente->nombre }}
                                </div>

                                <div class="text-muted">
                                    @if ($cita->vehiculo)
                                        {{ $cita->vehiculo->marca ?? '' }}
                                        {{ $cita->vehiculo->modelo ?? '' }}
                                        {{ $cita->vehiculo->placas ? '(' . $cita->vehiculo->placas . ')' : '' }}
                                    @else
                                        Sin vehículo
                                    @endif
                                </div>

                                <div class="mt-1">
                                    <span class="badge bg-secondary">
                                        {{ ucfirst($cita->estado) }}
                                    </span>
                                    <span class="badge bg-light text-dark">
                                        {{ $duracionCita }} min
                                    </span>
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
        <div class="card shadow-sm border-0 summary-card">
            <div class="card-body">
                <h5 class="card-title mb-3">Resumen</h5>

                <div class="row text-center">
                    <div class="col-md-3 border-end">
                        <h4 class="mb-0">{{ $citas->flatten(1)->count() }}</h4>
                        <small class="text-muted">Citas del mes</small>
                    </div>

                    <div class="col-md-3 border-end">
                        <h4 class="mb-0">{{ $citas->flatten(1)->where('estado', 'pendiente')->count() }}</h4>
                        <small class="text-muted">Pendientes</small>
                    </div>

                    <div class="col-md-3 border-end">
                        <h4 class="mb-0">{{ $citas->flatten(1)->where('estado', 'confirmada')->count() }}</h4>
                        <small class="text-muted">Confirmadas</small>
                    </div>

                    <div class="col-md-3">
                        <h4 class="mb-0">{{ $citas->flatten(1)->where('estado', 'atendida')->count() }}</h4>
                        <small class="text-muted">Atendidas</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card shadow-sm border-0 summary-card">
            <div class="card-body">
                <h5 class="card-title mb-3">Estados de las citas</h5>

                <div class="d-flex flex-wrap gap-2 mb-3">
                    <span class="badge bg-warning text-dark px-3 py-2">Pendiente</span>
                    <span class="badge bg-primary px-3 py-2">Confirmada</span>
                    <span class="badge bg-success px-3 py-2">Atendida</span>
                    <span class="badge bg-danger px-3 py-2">Cancelada</span>
                </div>

                <p class="text-muted mb-0">
                    Esta agenda permite organizar los servicios programados del taller.
                    El calendario ocupa el espacio principal y la información de apoyo se muestra debajo.
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
                            <select id="clienteCita" name="cliente_id" class="form-select @error('cliente_id') is-invalid @enderror" required>
                                <option value="">Selecciona un cliente</option>

                                @foreach ($clientes as $cliente)
                                    <option value="{{ $cliente->id }}" @selected(old('cliente_id') == $cliente->id)>
                                        {{ $cliente->nombre }} - {{ $cliente->telefono }}
                                    </option>
                                @endforeach
                            </select>

                            @error('cliente_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Vehículo</label>
                            <select id="vehiculoCita" name="vehiculo_id" class="form-select @error('vehiculo_id') is-invalid @enderror">
                                <option value="">Sin vehículo asignado</option>

                                @foreach ($vehiculos as $vehiculo)
                                    <option
                                        value="{{ $vehiculo->id }}"
                                        data-cliente-id="{{ $vehiculo->cliente_id }}"
                                        @selected(old('vehiculo_id') == $vehiculo->id)
                                    >
                                        {{ $vehiculo->marca ?? 'Vehículo' }}
                                        {{ $vehiculo->modelo ?? '' }}
                                        {{ $vehiculo->placas ? '- ' . $vehiculo->placas : '' }}
                                        {{ $vehiculo->cliente ? '(' . $vehiculo->cliente->nombre . ')' : '' }}
                                    </option>
                                @endforeach
                            </select>

                            @error('vehiculo_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Servicio</label>
                            <select id="servicioCita" name="servicio" class="form-select @error('servicio') is-invalid @enderror" required>
                                <option value="">Selecciona un servicio</option>
                                @foreach ($serviciosCita as $servicio => $duracion)
                                    <option
                                        value="{{ $servicio }}"
                                        data-duration="{{ $duracion }}"
                                        @selected(old('servicio') === $servicio)
                                    >
                                        {{ $servicio }} - {{ $duracion }} min
                                    </option>
                                @endforeach
                            </select>
                            <div id="duracionServicioCita" class="form-text">
                                Selecciona un servicio para ver la duración estimada.
                            </div>

                            @error('servicio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Fecha</label>
                            <input
                                type="date"
                                id="fechaCita"
                                name="fecha"
                                class="form-control @error('fecha') is-invalid @enderror"
                                value="{{ old('fecha') }}"
                                min="{{ now()->toDateString() }}"
                                required
                            >

                            @error('fecha')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Hora</label>
                            <select
                                id="horaCita"
                                name="hora"
                                class="form-select @error('hora') is-invalid @enderror"
                                required
                            >
                                <option value="">Selecciona una hora</option>
                                @foreach ($horariosCita as $horario)
                                    <option value="{{ $horario }}" @selected(old('hora') === $horario)>
                                        {{ $horario }}
                                    </option>
                                @endforeach
                            </select>

                            @error('hora')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Estado</label>
                            <select name="estado" class="form-select @error('estado') is-invalid @enderror" required>
                                <option value="pendiente" @selected(old('estado', 'pendiente') === 'pendiente')>Pendiente</option>
                                <option value="confirmada" @selected(old('estado') === 'confirmada')>Confirmada</option>
                                <option value="cancelada" @selected(old('estado') === 'cancelada')>Cancelada</option>
                                <option value="atendida" @selected(old('estado') === 'atendida')>Atendida</option>
                            </select>

                            @error('estado')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Observaciones</label>
                            <textarea
                                name="observaciones"
                                class="form-control @error('observaciones') is-invalid @enderror"
                                rows="3"
                                placeholder="Detalles adicionales de la cita"
                            >{{ old('observaciones') }}</textarea>

                            @error('observaciones')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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

@foreach ($citas->flatten(1) as $cita)
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
                            <p class="fw-semibold mb-0">
                                {{ $cita->fecha->translatedFormat('d F Y') }}
                            </p>
                            <p class="mb-0">
                                {{ $inicioCita->format('H:i') }} - {{ $finCita->format('H:i') }} ({{ $duracionCita }} min)
                            </p>
                        </div>

                        <div class="col-md-6">
                            <p class="text-muted mb-1">Cliente</p>
                            <p class="fw-semibold mb-0">{{ $cita->cliente->nombre }}</p>
                            <p class="mb-0">{{ $cita->cliente->telefono }}</p>
                            @if ($cita->cliente->correo)
                                <p class="mb-0">{{ $cita->cliente->correo }}</p>
                            @endif
                        </div>

                        <div class="col-md-6">
                            <p class="text-muted mb-1">Vehículo</p>
                            @if ($cita->vehiculo)
                                <p class="fw-semibold mb-0">
                                    {{ $cita->vehiculo->marca }} {{ $cita->vehiculo->modelo }}
                                </p>
                                <p class="mb-0">Placas: {{ $cita->vehiculo->placas ?: 'Sin placas' }}</p>
                                <p class="mb-0">Kilometraje: {{ number_format($cita->vehiculo->kilometraje_actual) }} km</p>
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
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
@endforeach

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const clienteCita = document.getElementById('clienteCita');
        const vehiculoCita = document.getElementById('vehiculoCita');
        const servicioCita = document.getElementById('servicioCita');
        const duracionServicioCita = document.getElementById('duracionServicioCita');
        const fechaCita = document.getElementById('fechaCita');
        const horaCita = document.getElementById('horaCita');

        const formatoFechaLocal = function (fecha) {
            const mes = String(fecha.getMonth() + 1).padStart(2, '0');
            const dia = String(fecha.getDate()).padStart(2, '0');

            return `${fecha.getFullYear()}-${mes}-${dia}`;
        };

        const actualizarDuracion = function () {
            const opcionSeleccionada = servicioCita.options[servicioCita.selectedIndex];
            const duracion = opcionSeleccionada ? opcionSeleccionada.dataset.duration : null;

            duracionServicioCita.textContent = duracion
                ? `Este servicio apartará ${duracion} minutos.`
                : 'Selecciona un servicio para ver la duración estimada.';
        };

        if (servicioCita && duracionServicioCita) {
            servicioCita.addEventListener('change', actualizarDuracion);
            actualizarDuracion();
        }

        if (clienteCita && vehiculoCita) {
            const opcionesVehiculos = Array.from(vehiculoCita.options)
                .slice(1)
                .map((option) => option.cloneNode(true));
            const vehiculoInicial = vehiculoCita.value;

            const filtrarVehiculos = function () {
                const clienteId = clienteCita.value;
                const valorActual = vehiculoCita.value || vehiculoInicial;

                vehiculoCita.innerHTML = '';
                vehiculoCita.append(new Option('Sin vehículo asignado', ''));
                vehiculoCita.disabled = !clienteId;

                opcionesVehiculos
                    .filter((option) => option.dataset.clienteId === clienteId)
                    .forEach((option) => {
                        const nuevaOpcion = option.cloneNode(true);
                        nuevaOpcion.selected = nuevaOpcion.value === valorActual;
                        vehiculoCita.append(nuevaOpcion);
                    });
            };

            clienteCita.addEventListener('change', filtrarVehiculos);
            filtrarVehiculos();
        }

        if (fechaCita && horaCita) {
            const actualizarDisponibilidad = function () {
                const ahora = new Date();
                const hoy = formatoFechaLocal(ahora);
                const horaActual = `${String(ahora.getHours()).padStart(2, '0')}:${String(ahora.getMinutes()).padStart(2, '0')}`;
                const fechaSeleccionada = fechaCita.value;
                const fechaComoDate = fechaSeleccionada ? new Date(`${fechaSeleccionada}T00:00:00`) : null;
                const esDomingo = fechaComoDate ? fechaComoDate.getDay() === 0 : false;
                const esPasado = fechaSeleccionada && fechaSeleccionada < hoy;

                fechaCita.setCustomValidity('');

                if (esDomingo) {
                    fechaCita.setCustomValidity('Los domingos son días inhábiles.');
                } else if (esPasado) {
                    fechaCita.setCustomValidity('No se pueden agendar citas en fechas pasadas.');
                }

                Array.from(horaCita.options).forEach((option) => {
                    if (!option.value) {
                        return;
                    }

                    option.disabled = esDomingo || esPasado || (fechaSeleccionada === hoy && option.value <= horaActual);
                });

                if (horaCita.selectedOptions.length && horaCita.selectedOptions[0].disabled) {
                    horaCita.value = '';
                }
            };

            fechaCita.addEventListener('change', actualizarDisponibilidad);
            actualizarDisponibilidad();
        }
    });
</script>

@if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modalNuevaCita = document.getElementById('modalNuevaCita');

            if (modalNuevaCita) {
                const modal = new bootstrap.Modal(modalNuevaCita);
                modal.show();
            }
        });
    </script>
@endif
@endsection
