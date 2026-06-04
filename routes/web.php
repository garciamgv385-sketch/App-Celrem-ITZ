<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\VehiculoController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth')->name('dashboard');

Route::resource('clientes', ClienteController::class)->middleware('auth');
Route::get('/citas', [CitaController::class, 'index'])->middleware('auth')->name('citas.index');
Route::post('/citas', [CitaController::class, 'store'])->middleware('auth')->name('citas.store');
Route::resource('vehiculos', VehiculoController::class)->only(['index', 'create', 'store', 'update', 'destroy'])->middleware('auth');

Route::resource('inventario', ProductoController::class)
    ->only(['index', 'store', 'update'])
    ->parameters(['inventario' => 'producto'])
    ->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/compras', [CompraController::class, 'index'])->name('compras.index');
    Route::post('/compras/carrito', [CompraController::class, 'agregarProducto'])->name('compras.carrito.agregar');
    Route::put('/compras/carrito', [CompraController::class, 'actualizarCarrito'])->name('compras.carrito.actualizar');
    Route::delete('/compras/carrito', [CompraController::class, 'vaciarCarrito'])->name('compras.carrito.vaciar');
    Route::delete('/compras/carrito/{producto}', [CompraController::class, 'eliminarProducto'])->name('compras.carrito.eliminar');
    Route::post('/compras', [CompraController::class, 'store'])->name('compras.store');

    Route::resource('proveedores', ProveedorController::class)
        ->only(['index', 'store', 'update', 'destroy'])
        ->parameters(['proveedores' => 'proveedor']);
});
