@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Clientes</h1>
            <p class="text-muted mb-0">Gestión de clientes registrados en el taller.</p>
        </div>

        @if (auth()->user()->esAdmin())
            <a href="{{ route('clientes.create') }}" class="btn btn-primary">
                Nuevo cliente
            </a>
        @endif
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
                                    <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#clienteDetalle{{ $cliente->id }}">
                                        Ver
                                    </button>
                                    @if (auth()->user()->esAdmin())
                                        <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#clienteEditar{{ $cliente->id }}">
                                            Editar
                                        </button>
                                        <form action="{{ route('clientes.destroy', $cliente) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" @disabled($cliente->estado === 'inactivo')>
                                                Eliminar
                                            </button>
                                        </form>
                                    @endif
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
        <div class="modal fade" id="clienteDetalle{{ $cliente->id }}" tabindex="-1" aria-labelledby="clienteDetalleLabel{{ $cliente->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <div>
                            <h2 class="modal-title fs-5" id="clienteDetalleLabel{{ $cliente->id }}">{{ $cliente->nombre }}</h2>
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

        @if (auth()->user()->esAdmin())
        <div class="modal fade" id="clienteEditar{{ $cliente->id }}" tabindex="-1" aria-labelledby="clienteEditarLabel{{ $cliente->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <form action="{{ route('clientes.update', $cliente) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="modal-header">
                            <h2 class="modal-title fs-5" id="clienteEditarLabel{{ $cliente->id }}">Editar cliente</h2>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nombre completo</label>
                                    <input type="text" name="nombre" class="form-control" value="{{ $cliente->nombre }}" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Teléfono</label>
                                    <input type="text" name="telefono" class="form-control" value="{{ $cliente->telefono }}" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Correo electrónico</label>
                                    <input type="email" name="correo" class="form-control" value="{{ $cliente->correo }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Estado</label>
                                    <select name="estado" class="form-select" required>
                                        <option value="activo" @selected($cliente->estado === 'activo')>Activo</option>
                                        <option value="inactivo" @selected($cliente->estado === 'inactivo')>Inactivo</option>
                                    </select>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">Dirección</label>
                                    <input type="text" name="direccion" class="form-control" value="{{ $cliente->direccion }}">
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">Observaciones</label>
                                    <textarea name="observaciones" class="form-control" rows="4">{{ $cliente->observaciones }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Guardar cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif
    @endforeach
@endsection
