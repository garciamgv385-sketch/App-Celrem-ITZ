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
            <form action="#" method="POST">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nombre completo</label>
                        <input type="text" name="nombre" class="form-control" placeholder="Ej. Juan Pérez">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="telefono" class="form-control" placeholder="Ej. 443 000 0000">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Correo electrónico</label>
                        <input type="email" name="correo" class="form-control" placeholder="cliente@example.com">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Estado</label>
                        <select name="estado" class="form-select">
                            <option value="activo">Activo</option>
                            <option value="inactivo">Inactivo</option>
                        </select>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Dirección</label>
                        <input type="text" name="direccion" class="form-control" placeholder="Dirección del cliente">
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Observaciones</label>
                        <textarea name="observaciones" class="form-control" rows="4" placeholder="Notas adicionales del cliente"></textarea>
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