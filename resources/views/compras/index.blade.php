@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Compras</h1>
            <p class="text-muted mb-0">
                Registro visual de compras de refacciones, aceites y lubricantes.
            </p>
        </div>

        <button class="btn btn-primary" disabled>
            Nueva compra
        </button>
    </div>

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title mb-3">Registrar compra</h5>

                    <form action="#" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Proveedor</label>
                            <select class="form-select" disabled>
                                <option>Selecciona un proveedor</option>
                                <option>Refacciones del Centro</option>
                                <option>Lubricantes Express</option>
                                <option>Autopartes Michoacán</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Producto</label>
                            <select class="form-select" disabled>
                                <option>Selecciona un producto</option>
                                <option>Aceite 5W-30</option>
                                <option>Filtro de aceite</option>
                                <option>Balatas delanteras</option>
                            </select>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Cantidad</label>
                                <input type="number" class="form-control" value="1" disabled>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Precio compra</label>
                                <input type="text" class="form-control" value="$0.00" disabled>
                            </div>
                        </div>

                        <div class="mt-3">
                            <label class="form-label">Observaciones</label>
                            <textarea class="form-control" rows="3" placeholder="Notas de la compra" disabled></textarea>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="button" class="btn btn-outline-secondary me-2" disabled>
                                Cancelar
                            </button>

                            <button type="button" class="btn btn-primary" disabled>
                                Guardar compra
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title mb-3">Historial de compras</h5>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Fecha</th>
                                    <th>Proveedor</th>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>01/06/2026</td>
                                    <td>Lubricantes Express</td>
                                    <td>Aceite 5W-30</td>
                                    <td>10</td>
                                    <td>$1,200.00</td>
                                    <td>
                                        <span class="badge bg-success">Registrada</span>
                                    </td>
                                </tr>

                                <tr>
                                    <td>02/06/2026</td>
                                    <td>Autopartes Michoacán</td>
                                    <td>Filtro de aceite</td>
                                    <td>8</td>
                                    <td>$560.00</td>
                                    <td>
                                        <span class="badge bg-success">Registrada</span>
                                    </td>
                                </tr>

                                <tr>
                                    <td>03/06/2026</td>
                                    <td>Refacciones del Centro</td>
                                    <td>Balatas delanteras</td>
                                    <td>4</td>
                                    <td>$1,800.00</td>
                                    <td>
                                        <span class="badge bg-warning text-dark">Pendiente</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <p class="text-muted small mb-0">
                        Esta vista solo muestra cómo se verá el módulo. Más adelante, al guardar una compra, se aumentará la existencia del producto en inventario.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection