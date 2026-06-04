@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Vehículos</h1>
            <p class="text-muted mb-0">Vehículos registrados y asignados a clientes.</p>
        </div>

        @unless (auth()->user()->esMecanico())
            <a href="{{ route('vehiculos.create') }}" class="btn btn-primary">
                Nuevo vehículo
            </a>
        @endunless
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('vehiculos.index') }}" method="GET" class="row g-2 mb-3">
                <div class="col-md-7">
                    <input
                        type="text"
                        name="buscar"
                        class="form-control"
                        value="{{ $busqueda }}"
                        placeholder="Buscar por cliente, modelo, año o placas"
                    >
                </div>

                <div class="col-md-auto">
                    <button type="submit" class="btn btn-outline-secondary">
                        Buscar
                    </button>
                </div>

                <div class="col-md-auto">
                    <a href="{{ route('vehiculos.index') }}" class="btn btn-outline-secondary">
                        Limpiar
                    </a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Cliente</th>
                            <th>Tipo</th>
                            <th>Marca</th>
                            <th>Modelo</th>
                            <th>Año</th>
                            <th>Placas</th>
                            <th>Kilometraje</th>
                            <th>Combustible</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($vehiculos as $vehiculo)
                            <tr>
                                <td>{{ $vehiculo->cliente?->nombre ?? 'Sin cliente' }}</td>
                                <td>{{ str_replace('_', ' ', ucfirst($vehiculo->tipo)) }}</td>
                                <td>{{ $vehiculo->marca }}</td>
                                <td>{{ $vehiculo->modelo }}</td>
                                <td>{{ $vehiculo->anio }}</td>
                                <td>{{ $vehiculo->placas }}</td>
                                <td>{{ number_format($vehiculo->kilometraje_actual) }} km</td>
                                <td>{{ ucfirst($vehiculo->tipo_combustible) }}</td>
                                <td>
                                    <span class="badge {{ $vehiculo->estado === 'activo' ? 'bg-success' : ($vehiculo->estado === 'en_servicio' ? 'bg-warning text-dark' : 'bg-secondary') }}">
                                        {{ str_replace('_', ' ', ucfirst($vehiculo->estado)) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    @unless (auth()->user()->esMecanico())
                                        <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#vehiculoEditar{{ $vehiculo->id }}">
                                            Editar
                                        </button>
                                        <form action="{{ route('vehiculos.destroy', $vehiculo) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" @disabled($vehiculo->estado === 'inactivo')>
                                                Eliminar
                                            </button>
                                        </form>
                                    @endunless
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted py-4">
                                    No hay vehículos registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $vehiculos->links() }}
            </div>
        </div>
    </div>

    @foreach ($vehiculos as $vehiculo)
        @unless (auth()->user()->esMecanico())
        <div class="modal fade" id="vehiculoEditar{{ $vehiculo->id }}" tabindex="-1" aria-labelledby="vehiculoEditarLabel{{ $vehiculo->id }}" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content">
                    <form action="{{ route('vehiculos.update', $vehiculo) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="modal-header">
                            <h2 class="modal-title fs-5" id="vehiculoEditarLabel{{ $vehiculo->id }}">Editar vehículo</h2>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Cliente</label>
                                    <select name="cliente_id" class="form-select" required>
                                        @foreach ($clientes as $cliente)
                                            <option value="{{ $cliente->id }}" @selected($vehiculo->cliente_id === $cliente->id)>
                                                {{ $cliente->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Tipo de vehículo</label>
                                    <select name="tipo" class="form-select" required>
                                        <option value="moto" @selected($vehiculo->tipo === 'moto')>Moto</option>
                                        <option value="auto" @selected($vehiculo->tipo === 'auto')>Auto</option>
                                        <option value="servicio_pesado" @selected($vehiculo->tipo === 'servicio_pesado')>Servicio pesado</option>
                                        <option value="camioneta" @selected($vehiculo->tipo === 'camioneta')>Camioneta</option>
                                        <option value="otro" @selected($vehiculo->tipo === 'otro')>Otro</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Marca</label>
                                    <input type="text" name="marca" class="form-control" value="{{ $vehiculo->marca }}" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Modelo</label>
                                    <input type="text" name="modelo" class="form-control" value="{{ $vehiculo->modelo }}" required>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Año</label>
                                    <input type="number" name="anio" class="form-control" value="{{ $vehiculo->anio }}" min="1980" max="2025" required>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Placas</label>
                                    <input type="text" name="placas" class="form-control" value="{{ $vehiculo->placas }}" required>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Kilometraje actual</label>
                                    <input type="number" name="kilometraje_actual" class="form-control" value="{{ $vehiculo->kilometraje_actual }}" min="0" required>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Tipo de combustible</label>
                                    <select name="tipo_combustible" class="form-select" required>
                                        <option value="gasolina" @selected($vehiculo->tipo_combustible === 'gasolina')>Gasolina</option>
                                        <option value="diesel" @selected($vehiculo->tipo_combustible === 'diesel')>Diésel</option>
                                        <option value="hibrido" @selected($vehiculo->tipo_combustible === 'hibrido')>Híbrido</option>
                                        <option value="electrico" @selected($vehiculo->tipo_combustible === 'electrico')>Eléctrico</option>
                                        <option value="gas" @selected($vehiculo->tipo_combustible === 'gas')>Gas</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Estado</label>
                                    <select name="estado" class="form-select" required>
                                        <option value="activo" @selected($vehiculo->estado === 'activo')>Activo</option>
                                        <option value="inactivo" @selected($vehiculo->estado === 'inactivo')>Inactivo</option>
                                        <option value="en_servicio" @selected($vehiculo->estado === 'en_servicio')>En servicio</option>
                                    </select>
                                </div>

                                <div class="col-md-8">
                                    <label class="form-label">Observaciones</label>
                                    <textarea name="observaciones" class="form-control" rows="3">{{ $vehiculo->observaciones }}</textarea>
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
        @endunless
    @endforeach
@endsection
