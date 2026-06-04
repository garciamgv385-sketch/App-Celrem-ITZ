@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Ventas</h1>
            <p class="text-muted mb-0">
                Registra ventas con carrito y búsqueda inteligente de productos.
            </p>
        </div>

        <a href="{{ route('inventario.index') }}" class="btn btn-outline-primary">
            Inventario
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-3">Buscar producto</h5>

                    <form action="{{ route('ventas.index') }}" method="GET" class="mb-3">
                        <label class="form-label">Búsqueda inteligente</label>
                        <div class="input-group">
                            <input
                                type="text"
                                name="buscar_producto"
                                class="form-control"
                                value="{{ $busqueda }}"
                                placeholder="Nombre, SKU, categoría o marca"
                            >
                            <button type="submit" class="btn btn-outline-primary">Buscar</button>
                        </div>
                    </form>

                    <form action="{{ route('ventas.carrito.agregar') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Producto</label>
                            <select id="productoVenta" name="producto_id" class="form-select @error('producto_id') is-invalid @enderror" required>
                                <option value="">Selecciona un producto</option>
                                @foreach ($productos as $producto)
                                    <option
                                        value="{{ $producto->id }}"
                                        data-precio="{{ $producto->precio_venta }}"
                                        data-existencia="{{ $producto->existencia }}"
                                        data-unidad="{{ $producto->unidad }}"
                                    >
                                        {{ $producto->nombre }}
                                        {{ $producto->sku ? '- ' . $producto->sku : '' }}
                                        {{ $producto->marca ? '(' . $producto->marca . ')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            <div id="productoVentaInfo" class="form-text">
                                Busca y selecciona un producto para ver su existencia.
                            </div>
                            @error('producto_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Cantidad</label>
                                <input type="number" name="cantidad" class="form-control @error('cantidad') is-invalid @enderror" value="{{ old('cantidad', 1) }}" min="1" required>
                                @error('cantidad')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Precio venta</label>
                                <input id="precioVenta" type="number" name="precio_unitario" class="form-control @error('precio_unitario') is-invalid @enderror" value="{{ old('precio_unitario', 0) }}" min="0" step="0.01" required>
                                @error('precio_unitario')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary" @disabled($productos->isEmpty())>
                                Agregar al carrito
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title mb-3">Datos de la venta</h5>

                    <form action="{{ route('ventas.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Cliente</label>
                            <select name="cliente_id" class="form-select @error('cliente_id') is-invalid @enderror">
                                <option value="">Venta al público</option>
                                @foreach ($clientes as $cliente)
                                    <option value="{{ $cliente->id }}" @selected(old('cliente_id') == $cliente->id)>
                                        {{ $cliente->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('cliente_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Fecha</label>
                            <input type="date" name="fecha" class="form-control @error('fecha') is-invalid @enderror" value="{{ old('fecha', now()->toDateString()) }}" required>
                            @error('fecha')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Observaciones</label>
                            <textarea name="observaciones" class="form-control @error('observaciones') is-invalid @enderror" rows="3">{{ old('observaciones') }}</textarea>
                            @error('observaciones')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <strong>Total: ${{ number_format($totalCarrito, 2) }}</strong>
                            <button type="submit" class="btn btn-success" @disabled(empty($carrito))>
                                Guardar venta
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">Mi carrito</h5>
                        <form action="{{ route('ventas.carrito.vaciar') }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-secondary" @disabled(empty($carrito))>
                                Vaciar
                            </button>
                        </form>
                    </div>

                    @if (empty($carrito))
                        <p class="text-muted mb-0">Agrega productos para formar una venta.</p>
                    @else
                        <form action="{{ route('ventas.carrito.actualizar') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Producto</th>
                                            <th>Existencia</th>
                                            <th style="width: 120px;">Cantidad</th>
                                            <th style="width: 150px;">Precio</th>
                                            <th>Subtotal</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($carrito as $item)
                                            <tr>
                                                <td>
                                                    <div class="fw-semibold">{{ $item['nombre'] }}</div>
                                                    <div class="text-muted small">
                                                        {{ $item['sku'] ?: 'Sin SKU' }} · {{ $item['categoria'] }} {{ $item['marca'] ? '· ' . $item['marca'] : '' }}
                                                    </div>
                                                </td>
                                                <td>{{ $item['existencia'] }} {{ $item['unidad'] }}</td>
                                                <td>
                                                    <input type="number" name="items[{{ $item['producto_id'] }}][cantidad]" class="form-control form-control-sm" value="{{ $item['cantidad'] }}" min="1" max="{{ $item['existencia'] }}" required>
                                                </td>
                                                <td>
                                                    <input type="number" name="items[{{ $item['producto_id'] }}][precio_unitario]" class="form-control form-control-sm" value="{{ $item['precio_unitario'] }}" min="0" step="0.01" required>
                                                </td>
                                                <td>${{ number_format($item['subtotal'], 2) }}</td>
                                                <td class="text-end">
                                                    <button type="submit" form="eliminarVentaCarrito{{ $item['producto_id'] }}" class="btn btn-sm btn-outline-danger">
                                                        Quitar
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="4" class="text-end">Total</th>
                                            <th>${{ number_format($totalCarrito, 2) }}</th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-outline-primary">Actualizar carrito</button>
                            </div>
                        </form>

                        @foreach ($carrito as $item)
                            <form id="eliminarVentaCarrito{{ $item['producto_id'] }}" action="{{ route('ventas.carrito.eliminar', $item['producto_id']) }}" method="POST" class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title mb-3">Historial de ventas</h5>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Fecha</th>
                                    <th>Cliente</th>
                                    <th>Productos</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($ventas as $venta)
                                    <tr>
                                        <td>{{ $venta->fecha->format('d/m/Y') }}</td>
                                        <td>{{ $venta->cliente?->nombre ?? 'Venta al público' }}</td>
                                        <td>
                                            @foreach ($venta->detalles as $detalle)
                                                <div class="small">
                                                    {{ $detalle->cantidad }} x {{ $detalle->producto->nombre }}
                                                </div>
                                            @endforeach
                                        </td>
                                        <td>${{ number_format((float) $venta->subtotal, 2) }}</td>
                                        <td><span class="badge bg-success">{{ ucfirst($venta->estado) }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">No hay ventas registradas.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $ventas->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const productoVenta = document.getElementById('productoVenta');
            const precioVenta = document.getElementById('precioVenta');
            const productoVentaInfo = document.getElementById('productoVentaInfo');

            if (!productoVenta || !precioVenta || !productoVentaInfo) {
                return;
            }

            const actualizarProducto = function () {
                const opcion = productoVenta.options[productoVenta.selectedIndex];

                if (!opcion || !opcion.value) {
                    productoVentaInfo.textContent = 'Busca y selecciona un producto para ver su existencia.';
                    return;
                }

                precioVenta.value = opcion.dataset.precio || 0;
                productoVentaInfo.textContent = `Existencia disponible: ${opcion.dataset.existencia} ${opcion.dataset.unidad}.`;
            };

            productoVenta.addEventListener('change', actualizarProducto);
            actualizarProducto();
        });
    </script>
@endsection
