<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Cliente;
use App\Models\Compra;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Venta;
use App\Models\Vehiculo;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $esCliente = $user->esCliente();
        $clienteId = $user->cliente_id;

        if ($esCliente) {
            $proximasCitas = Cita::with('vehiculo')
                ->where('cliente_id', $clienteId)
                ->whereIn('estado', ['pendiente', 'confirmada'])
                ->whereDate('fecha', '>=', now()->toDateString())
                ->orderBy('fecha')
                ->orderBy('hora')
                ->limit(5)
                ->get();

            return view('dashboard.index', [
                'esCliente' => true,
                'metricas' => [
                    'vehiculosActivos' => Vehiculo::where('cliente_id', $clienteId)->where('estado', 'activo')->count(),
                    'citasProximas' => Cita::where('cliente_id', $clienteId)
                        ->whereIn('estado', ['pendiente', 'confirmada'])
                        ->whereDate('fecha', '>=', now()->toDateString())
                        ->count(),
                    'citasPendientes' => Cita::where('cliente_id', $clienteId)->where('estado', 'pendiente')->count(),
                    'citasAtendidas' => Cita::where('cliente_id', $clienteId)->where('estado', 'atendida')->count(),
                ],
                'proximasCitas' => $proximasCitas,
                'vehiculos' => Vehiculo::where('cliente_id', $clienteId)
                    ->latest()
                    ->limit(5)
                    ->get(),
                'citasRecientes' => Cita::with('vehiculo')
                    ->where('cliente_id', $clienteId)
                    ->orderByDesc('fecha')
                    ->orderByDesc('hora')
                    ->limit(5)
                    ->get(),
            ]);
        }

        $inicioMes = now()->startOfMonth()->toDateString();
        $finMes = now()->endOfMonth()->toDateString();
        $hoy = now()->toDateString();

        return view('dashboard.index', [
            'esCliente' => false,
            'metricas' => [
                'clientesActivos' => Cliente::where('estado', 'activo')->count(),
                'vehiculosActivos' => Vehiculo::where('estado', 'activo')->count(),
                'citasHoy' => Cita::whereDate('fecha', $hoy)->whereIn('estado', ['pendiente', 'confirmada'])->count(),
                'citasPendientes' => Cita::where('estado', 'pendiente')->count(),
                'productosBajos' => Producto::where('estado', 'activo')
                    ->whereColumn('existencia', '<=', 'stock_minimo')
                    ->count(),
                'proveedoresActivos' => Proveedor::where('estado', 'activo')->count(),
                'ventasHoy' => Venta::whereDate('fecha', $hoy)->sum('subtotal'),
                'ventasMes' => Venta::whereBetween('fecha', [$inicioMes, $finMes])->sum('subtotal'),
                'comprasMes' => Compra::whereBetween('fecha', [$inicioMes, $finMes])->sum('subtotal'),
            ],
            'proximasCitas' => Cita::with(['cliente', 'vehiculo'])
                ->whereIn('estado', ['pendiente', 'confirmada'])
                ->whereDate('fecha', '>=', $hoy)
                ->orderBy('fecha')
                ->orderBy('hora')
                ->limit(6)
                ->get(),
            'productosCriticos' => Producto::where('estado', 'activo')
                ->whereColumn('existencia', '<=', 'stock_minimo')
                ->orderBy('existencia')
                ->limit(6)
                ->get(),
            'ventasRecientes' => Venta::with('cliente')
                ->orderByDesc('fecha')
                ->latest('id')
                ->limit(5)
                ->get(),
            'comprasRecientes' => Compra::with('proveedor')
                ->orderByDesc('fecha')
                ->latest('id')
                ->limit(5)
                ->get(),
        ]);
    }
}
