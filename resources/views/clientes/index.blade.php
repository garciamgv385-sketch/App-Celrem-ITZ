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
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('clientes.index') }}" method="GET" class="row mb-3">
                <div class="col-md-6">
                    <input
                        type="text"
                        name="buscar"
                        class="form-control"
                        value="{{ $busqueda }}"
                        placeholder="Buscar por nombre, teléfono o correo"
                    >
                </div>

                <div class="col-md-auto mt-2 mt-md-0">
                    <button type="submit" class="btn btn-outline-secondary">
                        Buscar
                    </button>
                </div>
            </form>

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
                        @forelse ($clientes as $cliente)
                            <tr>
                                <td>{{ $cliente->nombre }}</td>
                                <td>{{ $cliente->telefono }}</td>
                                <td>{{ $cliente->correo ?: 'Sin correo' }}</td>
                                <td>{{ $cliente->direccion ?: 'Sin dirección' }}</td>
                                <td>
                                    <span class="badge {{ $cliente->estado === 'activo' ? 'bg-success' : 'bg-secondary' }}">
                                        {{ ucfirst($cliente->estado) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-info"
                                        data-bs-toggle="modal"
                                        data-bs-target="#clienteModal{{ $cliente->id }}"
                                    >
                                        Ver
                                    </button>
                                    <button class="btn btn-sm btn-outline-warning" disabled>Editar</button>
                                    <button class="btn btn-sm btn-outline-danger" disabled>Eliminar</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    No hay clientes registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $clientes->links() }}
            </div>
        </div>
    </div>

    @foreach ($clientes as $cliente)
        <div class="modal fade" id="clienteModal{{ $cliente->id }}" tabindex="-1" aria-labelledby="clienteModalLabel{{ $cliente->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <div>
                            <h2 class="modal-title fs-5" id="clienteModalLabel{{ $cliente->id }}">{{ $cliente->nombre }}</h2>
                            <span class="badge {{ $cliente->estado === 'activo' ? 'bg-success' : 'bg-secondary' }}">
                                {{ ucfirst($cliente->estado) }}
                            </span>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <dl class="row mb-0">
                            <dt class="col-sm-4">Teléfono</dt>
                            <dd class="col-sm-8">{{ $cliente->telefono }}</dd>

                            <dt class="col-sm-4">Correo</dt>
                            <dd class="col-sm-8">{{ $cliente->correo ?: 'Sin correo' }}</dd>

                            <dt class="col-sm-4">Dirección</dt>
                            <dd class="col-sm-8">{{ $cliente->direccion ?: 'Sin dirección' }}</dd>

                            <dt class="col-sm-4">Observaciones</dt>
                            <dd class="col-sm-8">{{ $cliente->observaciones ?: 'Sin observaciones' }}</dd>

                            <dt class="col-sm-4">Registro</dt>
                            <dd class="col-sm-8">{{ $cliente->created_at->format('d/m/Y H:i') }}</dd>
                        </dl>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endsection
