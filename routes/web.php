<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\CitaController;
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
