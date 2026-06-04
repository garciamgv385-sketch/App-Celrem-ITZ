@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Proveedores</h1>
            <p class="text-muted mb-0">Alta, consulta y gestión de proveedores del taller.</p>
        </div>

        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevoProveedor">
            Nuevo proveedor
        </button>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form method="GET" action="{{ route('proveedores.index') }}" class="row g-2 mb-3">
                <div class="col-md-8">
                    <input type="text" name="buscar" class="form-control" value="{{ $busqueda }}" placeholder="Buscar por nombre, RFC, contacto, teléfono o correo">
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-primary">Buscar</button>
                    <a href="{{ route('proveedores.index') }}" class="btn btn-outline-secondary">Limpiar</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Proveedor</th>
                            <th>RFC</th>
                            <th>Contacto</th>
                            <th>Teléfono</th>
                            <th>Correo</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($proveedores as $proveedor)
                            <tr>
                                <td class="fw-semibold">{{ $proveedor->nombre }}</td>
                                <td>{{ $proveedor->rfc ?: 'Sin RFC' }}</td>
                                <td>{{ $proveedor->contacto ?: 'Sin contacto' }}</td>
                                <td>{{ $proveedor->telefono ?: 'Sin teléfono' }}</td>
                                <td>{{ $proveedor->correo ?: 'Sin correo' }}</td>
                                <td>
                                    <span class="badge {{ $proveedor->estado === 'activo' ? 'bg-success' : 'bg-secondary' }}">
                                        {{ ucfirst($proveedor->estado) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#proveedorDetalle{{ $proveedor->id }}">Ver</button>
                                    <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#proveedorEditar{{ $proveedor->id }}">Editar</button>
                                    <form action="{{ route('proveedores.destroy', $proveedor) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" @disabled($proveedor->estado === 'inactivo')>Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">No hay proveedores registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $proveedores->links() }}
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalNuevoProveedor" tabindex="-1" aria-labelledby="modalNuevoProveedorLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('proveedores.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="modal_id" value="modalNuevoProveedor">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalNuevoProveedorLabel">Nuevo proveedor</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        @include('proveedores.partials.form', ['proveedor' => null])
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar proveedor</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @foreach ($proveedores as $proveedor)
        <div class="modal fade" id="proveedorDetalle{{ $proveedor->id }}" tabindex="-1" aria-labelledby="proveedorDetalleLabel{{ $proveedor->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <div>
                            <h5 class="modal-title" id="proveedorDetalleLabel{{ $proveedor->id }}">{{ $proveedor->nombre }}</h5>
                            <span class="badge {{ $proveedor->estado === 'activo' ? 'bg-success' : 'bg-secondary' }}">{{ ucfirst($proveedor->estado) }}</span>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-4"><p class="text-muted mb-1">RFC</p><p class="fw-semibold mb-0">{{ $proveedor->rfc ?: 'Sin RFC' }}</p></div>
                            <div class="col-md-4"><p class="text-muted mb-1">Teléfono</p><p class="fw-semibold mb-0">{{ $proveedor->telefono ?: 'Sin teléfono' }}</p></div>
                            <div class="col-md-4"><p class="text-muted mb-1">Correo</p><p class="fw-semibold mb-0">{{ $proveedor->correo ?: 'Sin correo' }}</p></div>
                            <div class="col-md-6"><p class="text-muted mb-1">Contacto</p><p class="fw-semibold mb-0">{{ $proveedor->contacto ?: 'Sin contacto' }}</p></div>
                            <div class="col-md-6"><p class="text-muted mb-1">Dirección</p><p class="fw-semibold mb-0">{{ $proveedor->direccion ?: 'Sin dirección' }}</p></div>
                            <div class="col-md-12"><p class="text-muted mb-1">Observaciones</p><p class="mb-0">{{ $proveedor->observaciones ?: 'Sin observaciones' }}</p></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="proveedorEditar{{ $proveedor->id }}" tabindex="-1" aria-labelledby="proveedorEditarLabel{{ $proveedor->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <form action="{{ route('proveedores.update', $proveedor) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="modal_id" value="proveedorEditar{{ $proveedor->id }}">
                        <div class="modal-header">
                            <h5 class="modal-title" id="proveedorEditarLabel{{ $proveedor->id }}">Editar proveedor</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            @include('proveedores.partials.form', ['proveedor' => $proveedor])
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

    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const modalConErrores = document.getElementById(@json(old('modal_id', 'modalNuevoProveedor')));

                if (modalConErrores) {
                    const modal = new bootstrap.Modal(modalConErrores);
                    modal.show();
                }
            });
        </script>
    @endif
@endsection
