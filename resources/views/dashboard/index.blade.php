@extends('layouts.app')

@section('content')
    <h1 class="mb-4">Panel principal</h1>

    <div class="row g-4">
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Órdenes pendientes</h6>
                    <h2>0</h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Productos bajos</h6>
                    <h2>0</h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Mantenimientos próximos</h6>
                    <h2>0</h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Ventas del día</h6>
                    <h2>$0.00</h2>
                </div>
            </div>
        </div>
    </div>
@endsection
