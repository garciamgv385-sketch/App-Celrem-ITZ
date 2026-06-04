@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h1 class="h3 mb-1">Panel principal</h1>
            <p class="text-muted mb-0">
                @if ($esCliente)
                    Consulta tus vehiculos y el seguimiento de tus citas.
                @else
                    Resumen operativo del taller con datos actualizados de los modulos activos.
                @endif
            </p>
        </div>
        <span class="badge bg-light text-dark border">{{ now()->translatedFormat('d F Y') }}</span>
    </div>

    @if ($esCliente)
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <p class="text-muted mb-1">Vehiculos activos</p>
                        <h2 class="mb-0">{{ $metricas['vehiculosActivos'] }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <p class="text-muted mb-1">Citas proximas</p>
                        <h2 class="mb-0">{{ $metricas['citasProximas'] }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <p class="text-muted mb-1">Pendientes</p>
                        <h2 class="mb-0">{{ $metricas['citasPendientes'] }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <p class="text-muted mb-1">Atendidas</p>
                        <h2 class="mb-0">{{ $metricas['citasAtendidas'] }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0">Tus proximas citas</h5>
                            <a href="{{ route('citas.index') }}" class="btn btn-sm btn-outline-primary">Ver agenda</a>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Hora</th>
                                        <th>Servicio</th>
                                        <th>Vehiculo</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($proximasCitas as $cita)
                                        <tr>
                                            <td>{{ $cita->fecha->format('d/m/Y') }}</td>
                                            <td>{{ substr($cita->hora, 0, 5) }}</td>
                                            <td>{{ $cita->servicio }}</td>
                                            <td>
                                                @if ($cita->vehiculo)
                                                    {{ $cita->vehiculo->marca }} {{ $cita->vehiculo->modelo }}
                                                    <div class="text-muted small">{{ $cita->vehiculo->placas ?: 'Sin placas' }}</div>
                                                @else
                                                    <span class="text-muted">Sin vehiculo</span>
                                                @endif
                                            </td>
                                            <td><span class="badge bg-secondary">{{ ucfirst($cita->estado) }}</span></td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">No tienes citas proximas.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0">Tus vehiculos</h5>
                            <a href="{{ route('vehiculos.index') }}" class="btn btn-sm btn-outline-primary">Ver vehiculos</a>
                        </div>

                        @forelse ($vehiculos as $vehiculo)
                            <div class="border-bottom pb-3 mb-3">
                                <div class="fw-semibold">{{ $vehiculo->marca }} {{ $vehiculo->modelo }}</div>
                                <div class="text-muted small">
                                    {{ $vehiculo->anio }} · {{ $vehiculo->placas ?: 'Sin placas' }} · {{ ucfirst($vehiculo->estado) }}
                                </div>
                                <div class="small">{{ number_format($vehiculo->kilometraje_actual) }} km</div>
                            </div>
                        @empty
                            <p class="text-muted mb-0">Aun no tienes vehiculos registrados.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <p class="text-muted mb-1">Clientes activos</p>
                        <h2 class="mb-0">{{ $metricas['clientesActivos'] }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <p class="text-muted mb-1">Vehiculos activos</p>
                        <h2 class="mb-0">{{ $metricas['vehiculosActivos'] }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <p class="text-muted mb-1">Citas de hoy</p>
                        <h2 class="mb-0">{{ $metricas['citasHoy'] }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <p class="text-muted mb-1">Productos bajos</p>
                        <h2 class="mb-0">{{ $metricas['productosBajos'] }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <p class="text-muted mb-1">Ventas del dia</p>
                        <h2 class="mb-0">${{ number_format($metricas['ventasHoy'], 2) }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <p class="text-muted mb-1">Ventas del mes</p>
                        <h2 class="mb-0">${{ number_format($metricas['ventasMes'], 2) }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <p class="text-muted mb-1">Compras del mes</p>
                        <h2 class="mb-0">${{ number_format($metricas['comprasMes'], 2) }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <p class="text-muted mb-1">Proveedores activos</p>
                        <h2 class="mb-0">{{ $metricas['proveedoresActivos'] }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-xl-7">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0">Proximas citas</h5>
                            <a href="{{ route('citas.index') }}" class="btn btn-sm btn-outline-primary">Ver agenda</a>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Cliente</th>
                                        <th>Vehiculo</th>
                                        <th>Servicio</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($proximasCitas as $cita)
                                        <tr>
                                            <td>
                                                {{ $cita->fecha->format('d/m/Y') }}
                                                <div class="text-muted small">{{ substr($cita->hora, 0, 5) }}</div>
                                            </td>
                                            <td>{{ $cita->cliente?->nombre ?? 'Sin cliente' }}</td>
                                            <td>
                                                @if ($cita->vehiculo)
                                                    {{ $cita->vehiculo->marca }} {{ $cita->vehiculo->modelo }}
                                                    <div class="text-muted small">{{ $cita->vehiculo->placas ?: 'Sin placas' }}</div>
                                                @else
                                                    <span class="text-muted">Sin vehiculo</span>
                                                @endif
                                            </td>
                                            <td>{{ $cita->servicio }}</td>
                                            <td><span class="badge bg-secondary">{{ ucfirst($cita->estado) }}</span></td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">No hay citas proximas.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Movimientos recientes</h5>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <h6 class="text-muted">Ventas</h6>
                                @forelse ($ventasRecientes as $venta)
                                    <div class="border-bottom pb-2 mb-2">
                                        <div class="d-flex justify-content-between">
                                            <span>{{ $venta->cliente?->nombre ?? 'Venta sin cliente' }}</span>
                                            <strong>${{ number_format($venta->subtotal, 2) }}</strong>
                                        </div>
                                        <div class="text-muted small">{{ $venta->fecha->format('d/m/Y') }} · {{ ucfirst($venta->estado) }}</div>
                                    </div>
                                @empty
                                    <p class="text-muted mb-0">Sin ventas registradas.</p>
                                @endforelse
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted">Compras</h6>
                                @forelse ($comprasRecientes as $compra)
                                    <div class="border-bottom pb-2 mb-2">
                                        <div class="d-flex justify-content-between">
                                            <span>{{ $compra->proveedor?->nombre ?? 'Compra sin proveedor' }}</span>
                                            <strong>${{ number_format($compra->subtotal, 2) }}</strong>
                                        </div>
                                        <div class="text-muted small">{{ $compra->fecha->format('d/m/Y') }} · {{ ucfirst($compra->estado) }}</div>
                                    </div>
                                @empty
                                    <p class="text-muted mb-0">Sin compras registradas.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-5">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0">Inventario critico</h5>
                            <a href="{{ route('inventario.index') }}" class="btn btn-sm btn-outline-primary">Ver inventario</a>
                        </div>

                        @forelse ($productosCriticos as $producto)
                            <div class="border-bottom pb-3 mb-3">
                                <div class="d-flex justify-content-between gap-3">
                                    <div>
                                        <div class="fw-semibold">{{ $producto->nombre }}</div>
                                        <div class="text-muted small">{{ $producto->sku }} · {{ $producto->marca ?: 'Sin marca' }}</div>
                                    </div>
                                    <span class="badge {{ $producto->claseEstadoInventario() }}">{{ $producto->estadoInventario() }}</span>
                                </div>
                                <div class="small mt-1">
                                    Existencia: {{ $producto->existencia }} {{ $producto->unidad }} · Minimo: {{ $producto->stock_minimo }}
                                </div>
                            </div>
                        @empty
                            <p class="text-muted mb-0">No hay productos en nivel critico.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
