@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Nuevo vehículo</h1>
            <p class="text-muted mb-0">Asigna un vehículo a un cliente registrado.</p>
        </div>

        <a href="{{ route('vehiculos.index') }}" class="btn btn-outline-secondary">
            Volver
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            @if ($clientes->isEmpty())
                <div class="alert alert-warning">
                    Primero registra un cliente para poder asignarle un vehículo.
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('vehiculos.store') }}" method="POST">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Cliente</label>
                        <select name="cliente_id" class="form-select @error('cliente_id') is-invalid @enderror" required>
                            <option value="">Selecciona un cliente</option>
                            @foreach ($clientes as $cliente)
                                <option value="{{ $cliente->id }}" @selected((int) old('cliente_id') === $cliente->id)>
                                    {{ $cliente->nombre }}
                                </option>
                            @endforeach
                        </select>

                        @error('cliente_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Tipo de vehículo</label>
                        <select name="tipo" class="form-select @error('tipo') is-invalid @enderror" required>
                            <option value="">Selecciona una opción</option>
                            <option value="moto" @selected(old('tipo') === 'moto')>Moto</option>
                            <option value="auto" @selected(old('tipo', 'auto') === 'auto')>Auto</option>
                            <option value="servicio_pesado" @selected(old('tipo') === 'servicio_pesado')>Servicio pesado</option>
                            <option value="camioneta" @selected(old('tipo') === 'camioneta')>Camioneta</option>
                            <option value="otro" @selected(old('tipo') === 'otro')>Otro</option>
                        </select>

                        @error('tipo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Marca</label>
                        <input type="text" name="marca" class="form-control @error('marca') is-invalid @enderror" value="{{ old('marca') }}" placeholder="Ej. Nissan" required>

                        @error('marca')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Modelo</label>
                        <input type="text" name="modelo" class="form-control @error('modelo') is-invalid @enderror" value="{{ old('modelo') }}" placeholder="Ej. Versa" required>

                        @error('modelo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Año</label>
                        <input type="number" name="anio" class="form-control @error('anio') is-invalid @enderror" value="{{ old('anio') }}" min="1900" max="{{ date('Y') + 1 }}" placeholder="{{ date('Y') }}" required>

                        @error('anio')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Placas</label>
                        <input type="text" name="placas" class="form-control @error('placas') is-invalid @enderror" value="{{ old('placas') }}" placeholder="ABC-123" required>

                        @error('placas')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Kilometraje actual</label>
                        <input type="number" name="kilometraje_actual" class="form-control @error('kilometraje_actual') is-invalid @enderror" value="{{ old('kilometraje_actual', 0) }}" min="0" required>

                        @error('kilometraje_actual')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Tipo de combustible</label>
                        <select name="tipo_combustible" class="form-select @error('tipo_combustible') is-invalid @enderror" required>
                            <option value="">Selecciona una opción</option>
                            <option value="gasolina" @selected(old('tipo_combustible') === 'gasolina')>Gasolina</option>
                            <option value="diesel" @selected(old('tipo_combustible') === 'diesel')>Diésel</option>
                            <option value="hibrido" @selected(old('tipo_combustible') === 'hibrido')>Híbrido</option>
                            <option value="electrico" @selected(old('tipo_combustible') === 'electrico')>Eléctrico</option>
                            <option value="gas" @selected(old('tipo_combustible') === 'gas')>Gas</option>
                        </select>

                        @error('tipo_combustible')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Estado</label>
                        <select name="estado" class="form-select @error('estado') is-invalid @enderror" required>
                            <option value="activo" @selected(old('estado', 'activo') === 'activo')>Activo</option>
                            <option value="inactivo" @selected(old('estado') === 'inactivo')>Inactivo</option>
                            <option value="en_servicio" @selected(old('estado') === 'en_servicio')>En servicio</option>
                        </select>

                        @error('estado')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-8">
                        <label class="form-label">Observaciones</label>
                        <textarea name="observaciones" class="form-control @error('observaciones') is-invalid @enderror" rows="3" placeholder="Notas adicionales del vehículo">{{ old('observaciones') }}</textarea>

                        @error('observaciones')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('vehiculos.index') }}" class="btn btn-outline-secondary me-2">
                        Cancelar
                    </a>

                    <button type="submit" class="btn btn-primary" @disabled($clientes->isEmpty())>
                        Guardar vehículo
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
