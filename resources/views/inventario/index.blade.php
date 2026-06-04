@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Inventario</h1>
            <p class="text-muted mb-0">
                Control visual de refacciones, lubricantes y productos del taller.
            </p>
        </div>

        <button class="btn btn-primary" disabled>
            Nuevo producto
        </button>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="text-muted">Total de productos</h6>
                    <h3 class="mb-0">24</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="text-muted">Stock bajo</h6>
                    <h3 class="mb-0 text-warning">5</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="text-muted">Sin existencia</h6>
                    <h3 class="mb-0 text-danger">2</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="text-muted">Valor estimado</h6>
                    <h3 class="mb-0">$18,450</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <input type="text" class="form-control" placeholder="Buscar producto, marca o categoría" disabled>
                </div>

                <div class="col-md-3 mt-2 mt-md-0">
                    <select class="form-select" disabled>
                        <option>Todas las categorías</option>
                        <option>Aceites</option>
                        <option>Filtros</option>
                        <option>Refacciones</option>
                    </select>
                </div>
            </div>

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
                        <tr>
                            <td>Aceite 5W-30</td>
                            <td>Lubricantes</td>
                            <td>Mobil</td>
                            <td>12</td>
                            <td>5</td>
                            <td>$180.00</td>
                            <td>
                                <span class="badge bg-success">Disponible</span>
                            </td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-info" disabled>Ver</button>
                                <button class="btn btn-sm btn-outline-warning" disabled>Editar</button>
                            </td>
                        </tr>

                        <tr>
                            <td>Filtro de aceite</td>
                            <td>Filtros</td>
                            <td>Gonher</td>
                            <td>3</td>
                            <td>5</td>
                            <td>$95.00</td>
                            <td>
                                <span class="badge bg-warning text-dark">Stock bajo</span>
                            </td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-info" disabled>Ver</button>
                                <button class="btn btn-sm btn-outline-warning" disabled>Editar</button>
                            </td>
                        </tr>

                        <tr>
                            <td>Balatas delanteras</td>
                            <td>Refacciones</td>
                            <td>Fritec</td>
                            <td>0</td>
                            <td>2</td>
                            <td>$620.00</td>
                            <td>
                                <span class="badge bg-danger">Sin existencia</span>
                            </td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-info" disabled>Ver</button>
                                <button class="btn btn-sm btn-outline-warning" disabled>Editar</button>
                            </td>
                        </tr>

                        <tr>
                            <td>Anticongelante</td>
                            <td>Consumibles</td>
                            <td>Prestone</td>
                            <td>8</td>
                            <td>4</td>
                            <td>$150.00</td>
                            <td>
                                <span class="badge bg-success">Disponible</span>
                            </td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-info" disabled>Ver</button>
                                <button class="btn btn-sm btn-outline-warning" disabled>Editar</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <p class="text-muted small mb-0">
                Esta vista es un prototipo visual. Después se conectará con la base de datos para registrar productos reales.
            </p>
        </div>
    </div>
@endsection