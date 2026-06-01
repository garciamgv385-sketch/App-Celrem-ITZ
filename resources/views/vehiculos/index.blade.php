@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Vehículos</h1>
            <p class="text-muted mb-0">Vehículos registrados y asignados a clientes.</p>
        </div>

        <a href="{{ route('vehiculos.create') }}" class="btn btn-primary">
            Nuevo vehículo
        </a>
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
                            <th>Observaciones</th>
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
                                <td>{{ $vehiculo->observaciones ?: 'Sin observaciones' }}</td>
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
@endsection
