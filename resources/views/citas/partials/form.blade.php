@php
    $fieldId = $cita?->id ?? 'nueva';
@endphp

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label" for="clienteCita{{ $fieldId }}">Cliente</label>
        <select id="clienteCita{{ $fieldId }}" name="cliente_id" class="form-select @error('cliente_id') is-invalid @enderror" data-cita-cliente required>
            <option value="">Selecciona un cliente</option>
            @foreach ($clientes as $cliente)
                <option value="{{ $cliente->id }}" @selected((int) old('cliente_id', $cita?->cliente_id) === $cliente->id)>
                    {{ $cliente->nombre }} - {{ $cliente->telefono }}
                </option>
            @endforeach
        </select>
        @error('cliente_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label" for="vehiculoCita{{ $fieldId }}">Vehículo</label>
        <select id="vehiculoCita{{ $fieldId }}" name="vehiculo_id" class="form-select @error('vehiculo_id') is-invalid @enderror" data-cita-vehiculo>
            <option value="">Sin vehículo asignado</option>
            @foreach ($vehiculos as $vehiculo)
                <option
                    value="{{ $vehiculo->id }}"
                    data-cliente-id="{{ $vehiculo->cliente_id }}"
                    @selected((int) old('vehiculo_id', $cita?->vehiculo_id) === $vehiculo->id)
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
        <label class="form-label" for="servicioCita{{ $fieldId }}">Servicio</label>
        <select id="servicioCita{{ $fieldId }}" name="servicio" class="form-select @error('servicio') is-invalid @enderror" data-cita-servicio required>
            <option value="">Selecciona un servicio</option>
            @foreach ($serviciosCita as $servicio => $duracion)
                <option
                    value="{{ $servicio }}"
                    data-duration="{{ $duracion }}"
                    @selected(old('servicio', $cita?->servicio) === $servicio)
                >
                    {{ $servicio }} - {{ $duracion }} min
                </option>
            @endforeach
        </select>
        <div class="form-text" data-cita-duracion>Selecciona un servicio para ver la duración estimada.</div>
        @error('servicio')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label" for="fechaCita{{ $fieldId }}">Fecha</label>
        <input
            type="date"
            id="fechaCita{{ $fieldId }}"
            name="fecha"
            class="form-control @error('fecha') is-invalid @enderror"
            value="{{ old('fecha', $cita?->fecha?->toDateString()) }}"
            min="{{ now()->toDateString() }}"
            data-cita-fecha
            required
        >
        @error('fecha')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label" for="horaCita{{ $fieldId }}">Hora</label>
        <select id="horaCita{{ $fieldId }}" name="hora" class="form-select @error('hora') is-invalid @enderror" data-cita-hora required>
            <option value="">Selecciona una hora</option>
            @foreach ($horariosCita as $horario)
                <option value="{{ $horario }}" @selected(old('hora', $cita ? substr($cita->hora, 0, 5) : null) === $horario)>
                    {{ $horario }}
                </option>
            @endforeach
        </select>
        @error('hora')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label" for="estadoCita{{ $fieldId }}">Estado</label>
        <select id="estadoCita{{ $fieldId }}" name="estado" class="form-select @error('estado') is-invalid @enderror" required>
            @foreach (['pendiente', 'confirmada', 'cancelada', 'atendida'] as $estado)
                <option value="{{ $estado }}" @selected(old('estado', $cita?->estado ?? 'pendiente') === $estado)>
                    {{ ucfirst($estado) }}
                </option>
            @endforeach
        </select>
        @error('estado')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-12">
        <label class="form-label" for="observacionesCita{{ $fieldId }}">Observaciones</label>
        <textarea
            id="observacionesCita{{ $fieldId }}"
            name="observaciones"
            class="form-control @error('observaciones') is-invalid @enderror"
            rows="3"
            placeholder="Detalles adicionales de la cita"
        >{{ old('observaciones', $cita?->observaciones) }}</textarea>
        @error('observaciones')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
