@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Clientes</h1>
            <p class="text-muted mb-0">Gestión de clientes registrados en el taller.</p>
        </div>

        <a href="{{ route('clientes.create') }}" class="btn btn-primary">
            Nuevo cliente
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <input type="text" class="form-control" placeholder="Buscar por nombre, teléfono o correo">
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Nombre</th>
                            <th>Teléfono</th>
                            <th>Correo</th>
                            <th>Dirección</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Cliente de prueba</td>
                            <td>443 000 0000</td>
                            <td>cliente@example.com</td>
                            <td>Morelia, Michoacán</td>
                            <td>
                                <span class="badge bg-success">Activo</span>
                            </td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-info">Ver</button>
                                <button class="btn btn-sm btn-outline-warning">Editar</button>
                                <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                            </td>
                        </tr>

                        <tr>
                            <td>María González</td>
                            <td>443 111 2222</td>
                            <td>maria@example.com</td>
                            <td>Zitácuaro, Michoacán</td>
                            <td>
                                <span class="badge bg-success">Activo</span>
                            </td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-info">Ver</button>
                                <button class="btn btn-sm btn-outline-warning">Editar</button>
                                <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection