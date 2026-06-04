@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Compras</h1>
            <p class="text-muted mb-0">
                Registra compras con múltiples productos y actualiza inventario automáticamente.
            </p>
        </div>

        <a href="{{ route('proveedores.index') }}" class="btn btn-outline-primary">
            Proveedores
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
                    <h5 class="card-title mb-3">Agregar producto al carrito</h5>

                    <form action="{{ route('compras.carrito.agregar') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Producto</label>
                            <select id="productoCompra" name="producto_id" class="form-select @error('producto_id') is-invalid @enderror" required>
                                <option value="">Selecciona un producto</option>
                                @foreach ($productos as $producto)
                                    <option
                                        value="{{ $producto->id }}"
                                        data-precio="{{ $producto->precio_compra }}"
                                        data-existencia="{{ $producto->existencia }}"
                                        data-unidad="{{ $producto->unidad }}"
                                    >
                                        {{ $producto->nombre }} {{ $producto->sku ? '- ' . $producto->sku : '' }}
                                    </option>
                                @endforeach
                            </select>
                            <div id="productoCompraInfo" class="form-text">Selecciona un producto para ver existencia actual.</div>
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
                                <label class="form-label">Precio compra</label>
                                <input id="precioCompra" type="number" name="precio_unitario" class="form-control @error('precio_unitario') is-invalid @enderror" value="{{ old('precio_unitario', 0) }}" min="0" step="0.01" required>
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
                    <h5 class="card-title mb-3">Datos de la compra</h5>

                    <form action="{{ route('compras.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Proveedor</label>
                            <select name="proveedor_id" class="form-select @error('proveedor_id') is-invalid @enderror" required>
                                <option value="">Selecciona un proveedor</option>
                                @foreach ($proveedores as $proveedor)
                                    <option value="{{ $proveedor->id }}" @selected(old('proveedor_id') == $proveedor->id)>
                                        {{ $proveedor->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('proveedor_id')
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
                            <button type="submit" class="btn btn-success" @disabled(empty($carrito) || $proveedores->isEmpty())>
                                Guardar compra
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
                        <form action="{{ route('compras.carrito.vaciar') }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-secondary" @disabled(empty($carrito))>
                                Vaciar
                            </button>
                        </form>
                    </div>

                    @if (empty($carrito))
                        <p class="text-muted mb-0">Agrega productos para formar una compra.</p>
                    @else
                        <form action="{{ route('compras.carrito.actualizar') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Producto</th>
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
                                                    <div class="text-muted small">{{ $item['sku'] ?: 'Sin SKU' }}</div>
                                                </td>
                                                <td>
                                                    <input type="number" name="items[{{ $item['producto_id'] }}][cantidad]" class="form-control form-control-sm" value="{{ $item['cantidad'] }}" min="1" required>
                                                </td>
                                                <td>
                                                    <input type="number" name="items[{{ $item['producto_id'] }}][precio_unitario]" class="form-control form-control-sm" value="{{ $item['precio_unitario'] }}" min="0" step="0.01" required>
                                                </td>
                                                <td>${{ number_format($item['subtotal'], 2) }}</td>
                                                <td class="text-end">
                                                    <button type="submit" form="eliminarCarrito{{ $item['producto_id'] }}" class="btn btn-sm btn-outline-danger">
                                                        Quitar
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="3" class="text-end">Total</th>
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
                            <form id="eliminarCarrito{{ $item['producto_id'] }}" action="{{ route('compras.carrito.eliminar', $item['producto_id']) }}" method="POST" class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title mb-3">Historial de compras</h5>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Fecha</th>
                                    <th>Proveedor</th>
                                    <th>Productos</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($compras as $compra)
                                    <tr>
                                        <td>{{ $compra->fecha->format('d/m/Y') }}</td>
                                        <td>{{ $compra->proveedor->nombre }}</td>
                                        <td>
                                            @foreach ($compra->detalles as $detalle)
                                                <div class="small">
                                                    {{ $detalle->cantidad }} x {{ $detalle->producto->nombre }}
                                                </div>
                                            @endforeach
                                        </td>
                                        <td>${{ number_format((float) $compra->subtotal, 2) }}</td>
                                        <td><span class="badge bg-success">{{ ucfirst($compra->estado) }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">No hay compras registradas.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $compras->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const productoCompra = document.getElementById('productoCompra');
            const precioCompra = document.getElementById('precioCompra');
            const productoCompraInfo = document.getElementById('productoCompraInfo');

            if (!productoCompra || !precioCompra || !productoCompraInfo) {
                return;
            }

            const actualizarProducto = function () {
                const opcion = productoCompra.options[productoCompra.selectedIndex];

                if (!opcion || !opcion.value) {
                    productoCompraInfo.textContent = 'Selecciona un producto para ver existencia actual.';
                    return;
                }

                precioCompra.value = opcion.dataset.precio || 0;
                productoCompraInfo.textContent = `Existencia actual: ${opcion.dataset.existencia} ${opcion.dataset.unidad}.`;
            };

            productoCompra.addEventListener('change', actualizarProducto);
            actualizarProducto();
        });
    </script>
@endsection
