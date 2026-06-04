@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Inventario</h1>
            <p class="text-muted mb-0">
                Control de refacciones, lubricantes y productos del taller.
            </p>
        </div>

        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevoProducto">
            Nuevo producto
        </button>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="text-muted">Total de productos</h6>
                    <h3 class="mb-0">{{ $resumen['total'] }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="text-muted">Stock bajo</h6>
                    <h3 class="mb-0 text-warning">{{ $resumen['stock_bajo'] }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="text-muted">Sin existencia</h6>
                    <h3 class="mb-0 text-danger">{{ $resumen['sin_existencia'] }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="text-muted">Valor estimado</h6>
                    <h3 class="mb-0">${{ number_format($resumen['valor'], 2) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form method="GET" action="{{ route('inventario.index') }}" class="row mb-3">
                <div class="col-md-6">
                    <input
                        type="text"
                        name="buscar"
                        class="form-control"
                        value="{{ $busqueda }}"
                        placeholder="Buscar producto, marca, SKU o proveedor"
                    >
                </div>

                <div class="col-md-3 mt-2 mt-md-0">
                    <select name="categoria" class="form-select">
                        <option value="">Todas las categorías</option>
                        @foreach ($categorias as $categoriaDisponible)
                            <option value="{{ $categoriaDisponible }}" @selected($categoria === $categoriaDisponible)>
                                {{ $categoriaDisponible }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 mt-2 mt-md-0 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-primary">
                        Filtrar
                    </button>
                    <a href="{{ route('inventario.index') }}" class="btn btn-outline-secondary">
                        Limpiar
                    </a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Producto</th>
                            <th>Categoría</th>
                            <th>Marca</th>
                            <th>Existencia</th>
                            <th>Stock mínimo</th>
                            <th>Precio venta</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($productos as $producto)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $producto->nombre }}</div>
                                    <div class="text-muted small">{{ $producto->sku ?: 'Sin SKU' }}</div>
                                </td>
                                <td>{{ $producto->categoria }}</td>
                                <td>{{ $producto->marca ?: 'Sin marca' }}</td>
                                <td>{{ $producto->existencia }} {{ $producto->unidad }}</td>
                                <td>{{ $producto->stock_minimo }} {{ $producto->unidad }}</td>
                                <td>${{ number_format((float) $producto->precio_venta, 2) }}</td>
                                <td>
                                    <span class="badge {{ $producto->claseEstadoInventario() }}">
                                        {{ $producto->estadoInventario() }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#productoDetalle{{ $producto->id }}">
                                        Ver
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#productoEditar{{ $producto->id }}">
                                        Editar
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    No hay productos registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $productos->links() }}
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalNuevoProducto" tabindex="-1" aria-labelledby="modalNuevoProductoLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('inventario.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="modal_id" value="modalNuevoProducto">

                    <div class="modal-header">
                        <h5 class="modal-title" id="modalNuevoProductoLabel">Nuevo producto</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>

                    <div class="modal-body">
                        @include('inventario.partials.form', ['producto' => null])
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar producto</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @foreach ($productos as $producto)
        <div class="modal fade" id="productoDetalle{{ $producto->id }}" tabindex="-1" aria-labelledby="productoDetalleLabel{{ $producto->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <div>
                            <h5 class="modal-title" id="productoDetalleLabel{{ $producto->id }}">{{ $producto->nombre }}</h5>
                            <span class="badge {{ $producto->claseEstadoInventario() }}">{{ $producto->estadoInventario() }}</span>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <p class="text-muted mb-1">Categoría</p>
                                <p class="fw-semibold mb-0">{{ $producto->categoria }}</p>
                            </div>
                            <div class="col-md-4">
                                <p class="text-muted mb-1">Marca</p>
                                <p class="fw-semibold mb-0">{{ $producto->marca ?: 'Sin marca' }}</p>
                            </div>
                            <div class="col-md-4">
                                <p class="text-muted mb-1">SKU</p>
                                <p class="fw-semibold mb-0">{{ $producto->sku ?: 'Sin SKU' }}</p>
                            </div>
                            <div class="col-md-4">
                                <p class="text-muted mb-1">Existencia</p>
                                <p class="fw-semibold mb-0">{{ $producto->existencia }} {{ $producto->unidad }}</p>
                            </div>
                            <div class="col-md-4">
                                <p class="text-muted mb-1">Stock mínimo</p>
                                <p class="fw-semibold mb-0">{{ $producto->stock_minimo }} {{ $producto->unidad }}</p>
                            </div>
                            <div class="col-md-4">
                                <p class="text-muted mb-1">Valor estimado</p>
                                <p class="fw-semibold mb-0">${{ number_format($producto->valorInventario(), 2) }}</p>
                            </div>
                            <div class="col-md-4">
                                <p class="text-muted mb-1">Precio compra</p>
                                <p class="fw-semibold mb-0">${{ number_format((float) $producto->precio_compra, 2) }}</p>
                            </div>
                            <div class="col-md-4">
                                <p class="text-muted mb-1">Precio venta</p>
                                <p class="fw-semibold mb-0">${{ number_format((float) $producto->precio_venta, 2) }}</p>
                            </div>
                            <div class="col-md-4">
                                <p class="text-muted mb-1">Ubicación</p>
                                <p class="fw-semibold mb-0">{{ $producto->ubicacion ?: 'Sin ubicación' }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="text-muted mb-1">Proveedor</p>
                                <p class="fw-semibold mb-0">{{ $producto->proveedor ?: 'Sin proveedor' }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="text-muted mb-1">Observaciones</p>
                                <p class="mb-0">{{ $producto->observaciones ?: 'Sin observaciones' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="productoEditar{{ $producto->id }}" tabindex="-1" aria-labelledby="productoEditarLabel{{ $producto->id }}" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content">
                    <form action="{{ route('inventario.update', $producto) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="modal_id" value="productoEditar{{ $producto->id }}">

                        <div class="modal-header">
                            <h5 class="modal-title" id="productoEditarLabel{{ $producto->id }}">Editar producto</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>

                        <div class="modal-body">
                            @include('inventario.partials.form', ['producto' => $producto])
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
                const modalConErrores = document.getElementById(@json(old('modal_id', 'modalNuevoProducto')));

                if (modalConErrores) {
                    const modal = new bootstrap.Modal(modalConErrores);
                    modal.show();
                }
            });
        </script>
    @endif
@endsection
