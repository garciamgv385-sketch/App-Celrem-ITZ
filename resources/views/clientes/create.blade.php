@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Nuevo cliente</h1>
            <p class="text-muted mb-0">Registra la información básica del cliente.</p>
        </div>

        <a href="{{ route('clientes.index') }}" class="btn btn-outline-secondary">
            Volver
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('clientes.store') }}" method="POST">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nombre completo</label>
                        <input
                            type="text"
                            name="nombre"
                            class="form-control @error('nombre') is-invalid @enderror"
                            value="{{ old('nombre') }}"
                            placeholder="Ej. Juan Pérez"
                            required
                        >

                        @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Teléfono</label>
                        <input
                            type="text"
                            name="telefono"
                            class="form-control @error('telefono') is-invalid @enderror"
                            value="{{ old('telefono') }}"
                            placeholder="Ej. 443 000 0000"
                            required
                        >

                        @error('telefono')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Correo electrónico</label>
                        <input
                            type="email"
                            name="correo"
                            class="form-control @error('correo') is-invalid @enderror"
                            value="{{ old('correo') }}"
                            placeholder="cliente@example.com"
                        >

                        @error('correo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Estado</label>
                        <select name="estado" class="form-select @error('estado') is-invalid @enderror" required>
                            <option value="activo" @selected(old('estado', 'activo') === 'activo')>Activo</option>
                            <option value="inactivo" @selected(old('estado') === 'inactivo')>Inactivo</option>
                        </select>

                        @error('estado')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Dirección</label>
                        <input
                            type="text"
                            name="direccion"
                            class="form-control @error('direccion') is-invalid @enderror"
                            value="{{ old('direccion') }}"
                            placeholder="Dirección del cliente"
                        >

                        @error('direccion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Observaciones</label>
                        <textarea
                            name="observaciones"
                            class="form-control @error('observaciones') is-invalid @enderror"
                            rows="4"
                            placeholder="Notas adicionales del cliente"
                        >{{ old('observaciones') }}</textarea>

                        @error('observaciones')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('clientes.index') }}" class="btn btn-outline-secondary me-2">
                        Cancelar
                    </a>

                    <button type="submit" class="btn btn-primary">
                        Guardar cliente
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
