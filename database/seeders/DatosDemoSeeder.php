<?php

namespace Database\Seeders;

use App\Models\Cita;
use App\Models\Cliente;
use App\Models\Compra;
use App\Models\CompraDetalle;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\User;
use App\Models\Vehiculo;
use App\Models\Venta;
use App\Models\VentaDetalle;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatosDemoSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $faker = fake();
		User::updateOrCreate(
    ['email' => 'admin@admin.com'],
    [
        'name' => 'Administrador',
        'password' => Hash::make('admin'),
        'rol' => 'admin',
        'cliente_id' => null,
    ]
);
            /*
            Total aproximado/exacto de registros principales:
            users: 400
            clientes: 400
            vehiculos: 600
            citas: 800
            proveedores: 50
            productos: 250
            compras: 50
            compra_detalles: 250
            ventas: 50
            venta_detalles: 150

            Total: 3000 registros
            */

            $clientes = collect();
            $vehiculos = collect();
            $proveedores = collect();
            $productos = collect();

            /*
            |--------------------------------------------------------------------------
            | Usuarios principales
            |--------------------------------------------------------------------------
            */

            User::create([
                'name' => 'Administrador',
                'email' => 'admin@taller.com',
                'password' => Hash::make('password'),
                'rol' => 'admin',
            ]);

            User::create([
                'name' => 'Mecánico Principal',
                'email' => 'mecanico@taller.com',
                'password' => Hash::make('password'),
                'rol' => 'mecanico',
            ]);

            /*
            |--------------------------------------------------------------------------
            | Clientes y usuarios cliente
            |--------------------------------------------------------------------------
            */

            for ($i = 1; $i <= 400; $i++) {
                $cliente = Cliente::create([
                    'nombre' => "Cliente Demo {$i}",
                    'telefono' => '715' . str_pad((string) $i, 7, '0', STR_PAD_LEFT),
                    'correo' => "cliente{$i}@demo.com",
                    'direccion' => "Calle {$i}, Zitácuaro, Michoacán",
                    'estado' => $i % 20 === 0 ? 'inactivo' : 'activo',
                    'observaciones' => 'Cliente generado automáticamente por seeder.',
                ]);

                $clientes->push($cliente);

                // Solo se crean 398 usuarios cliente para mantener el total en 3000.
                if ($i <= 398) {
                    User::create([
                        'cliente_id' => $cliente->id,
                        'name' => $cliente->nombre,
                        'email' => $cliente->correo,
                        'password' => Hash::make('password'),
                        'rol' => 'cliente',
                    ]);
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Vehículos
            |--------------------------------------------------------------------------
            */

            $marcas = ['Nissan', 'Chevrolet', 'Volkswagen', 'Toyota', 'Honda', 'Ford', 'Mazda'];
            $modelos = ['Versa', 'Aveo', 'Jetta', 'Corolla', 'Civic', 'Focus', 'Mazda 3'];
            $tipos = ['moto', 'auto', 'servicio_pesado', 'camioneta', 'otro'];
            $combustibles = ['gasolina', 'diesel', 'hibrido', 'electrico', 'gas'];

            for ($i = 1; $i <= 600; $i++) {
                $vehiculo = Vehiculo::create([
                    'cliente_id' => $clientes->random()->id,
                    'tipo' => $faker->randomElement($tipos),
                    'marca' => $faker->randomElement($marcas),
                    'modelo' => $faker->randomElement($modelos),
                    'anio' => $faker->numberBetween(2005, 2026),
                    'placas' => 'CL' . str_pad((string) $i, 5, '0', STR_PAD_LEFT),
                    'kilometraje_actual' => $faker->numberBetween(15000, 250000),
                    'tipo_combustible' => $faker->randomElement($combustibles),
                    'estado' => $faker->randomElement(['activo', 'activo', 'activo', 'en_servicio', 'inactivo']),
                    'observaciones' => 'Vehículo generado automáticamente.',
                ]);

                $vehiculos->push($vehiculo);
            }

            /*
            |--------------------------------------------------------------------------
            | Proveedores
            |--------------------------------------------------------------------------
            */

            for ($i = 1; $i <= 50; $i++) {
                $proveedor = Proveedor::create([
                    'nombre' => "Proveedor Demo {$i}",
                    'rfc' => 'RFC' . str_pad((string) $i, 10, '0', STR_PAD_LEFT),
                    'telefono' => '443' . str_pad((string) $i, 7, '0', STR_PAD_LEFT),
                    'correo' => "proveedor{$i}@demo.com",
                    'direccion' => "Zona comercial {$i}, Michoacán",
                    'contacto' => "Contacto {$i}",
                    'estado' => $i % 15 === 0 ? 'inactivo' : 'activo',
                    'observaciones' => 'Proveedor generado automáticamente.',
                ]);

                $proveedores->push($proveedor);
            }

            /*
            |--------------------------------------------------------------------------
            | Productos de inventario
            |--------------------------------------------------------------------------
            */

            $categorias = ['Lubricantes', 'Filtros', 'Refacciones', 'Consumibles', 'Frenos', 'Bujías'];
            $productosBase = [
                'Aceite 5W-30',
                'Aceite 20W-50',
                'Filtro de aceite',
                'Filtro de aire',
                'Balatas delanteras',
                'Bujía estándar',
                'Anticongelante',
                'Líquido de frenos',
                'Aditivo gasolina',
                'Limpiador de inyectores',
            ];

            for ($i = 1; $i <= 250; $i++) {
                $precioCompra = $faker->randomFloat(2, 50, 1500);
                $precioVenta = round($precioCompra * 1.35, 2);

                $producto = Producto::create([
                    'nombre' => $faker->randomElement($productosBase) . " {$i}",
                    'categoria' => $faker->randomElement($categorias),
                    'marca' => $faker->randomElement(['Mobil', 'Castrol', 'Gonher', 'Fritec', 'Bosch', 'Prestone']),
                    'sku' => 'CEL-PROD-' . str_pad((string) $i, 5, '0', STR_PAD_LEFT),
                    'unidad' => $faker->randomElement(['pieza', 'litro', 'galón', 'kit']),
                    'existencia' => $faker->numberBetween(0, 80),
                    'stock_minimo' => $faker->numberBetween(3, 15),
                    'precio_compra' => $precioCompra,
                    'precio_venta' => $precioVenta,
                    'proveedor' => $proveedores->random()->nombre,
                    'ubicacion' => 'Estante ' . $faker->randomElement(['A', 'B', 'C', 'D']),
                    'estado' => $i % 30 === 0 ? 'inactivo' : 'activo',
                    'observaciones' => 'Producto generado automáticamente.',
                ]);

                $productos->push($producto);
            }

            /*
            |--------------------------------------------------------------------------
            | Citas
            |--------------------------------------------------------------------------
            */

            $servicios = [
                'Cambio de aceite',
                'Afinación',
                'Revisión de frenos',
                'Cambio de filtros',
                'Diagnóstico general',
                'Mantenimiento preventivo',
            ];

            $horas = [
                '08:00',
                '08:30',
                '09:00',
                '09:30',
                '10:00',
                '10:30',
                '11:00',
                '11:30',
                '12:00',
                '12:30',
                '13:00',
                '13:30',
                '14:00',
                '14:30',
                '15:00',
                '15:30',
                '16:00',
                '16:30',
            ];

            for ($i = 1; $i <= 800; $i++) {
                $cliente = $clientes->random();

                $vehiculo = Vehiculo::where('cliente_id', $cliente->id)
                    ->inRandomOrder()
                    ->first();

                $fecha = Carbon::now()->addDays($faker->numberBetween(1, 120));

                // Evita domingos.
                if ($fecha->isSunday()) {
                    $fecha->addDay();
                }

                Cita::create([
                    'cliente_id' => $cliente->id,
                    'vehiculo_id' => $vehiculo?->id,
                    'servicio' => $faker->randomElement($servicios),
                    'fecha' => $fecha->toDateString(),
                    'hora' => $faker->randomElement($horas),
                    'estado' => $faker->randomElement(['pendiente', 'confirmada', 'cancelada', 'atendida']),
                    'observaciones' => 'Cita generada automáticamente.',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Compras y detalles de compra
            |--------------------------------------------------------------------------
            */

            for ($i = 1; $i <= 50; $i++) {
                $compra = Compra::create([
                    'proveedor_id' => $proveedores->random()->id,
                    'fecha' => Carbon::now()->subDays($faker->numberBetween(1, 90))->toDateString(),
                    'subtotal' => 0,
                    'estado' => 'registrada',
                    'observaciones' => 'Compra generada automáticamente.',
                ]);

                $subtotalCompra = 0;
                $productosCompra = $productos->random(5);

                foreach ($productosCompra as $producto) {
                    $cantidad = $faker->numberBetween(1, 10);
                    $precioUnitario = (float) $producto->precio_compra;
                    $subtotalDetalle = $cantidad * $precioUnitario;

                    CompraDetalle::create([
                        'compra_id' => $compra->id,
                        'producto_id' => $producto->id,
                        'cantidad' => $cantidad,
                        'precio_unitario' => $precioUnitario,
                        'subtotal' => $subtotalDetalle,
                    ]);

                    Producto::whereKey($producto->id)->increment('existencia', $cantidad);

                    $subtotalCompra += $subtotalDetalle;
                }

                $compra->update([
                    'subtotal' => $subtotalCompra,
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Ventas y detalles de venta
            |--------------------------------------------------------------------------
            */

            for ($i = 1; $i <= 50; $i++) {
                $venta = Venta::create([
                    'cliente_id' => $clientes->random()->id,
                    'fecha' => Carbon::now()->subDays($faker->numberBetween(1, 60))->toDateString(),
                    'subtotal' => 0,
                    'estado' => 'registrada',
                    'observaciones' => 'Venta generada automáticamente.',
                ]);

                $subtotalVenta = 0;

                for ($j = 1; $j <= 3; $j++) {
                    $producto = Producto::where('estado', 'activo')
                        ->where('existencia', '>', 5)
                        ->inRandomOrder()
                        ->first();

                    if (!$producto) {
                        continue;
                    }

                    $cantidad = $faker->numberBetween(1, min(5, $producto->existencia));
                    $precioUnitario = (float) $producto->precio_venta;
                    $subtotalDetalle = $cantidad * $precioUnitario;

                    VentaDetalle::create([
                        'venta_id' => $venta->id,
                        'producto_id' => $producto->id,
                        'cantidad' => $cantidad,
                        'precio_unitario' => $precioUnitario,
                        'subtotal' => $subtotalDetalle,
                    ]);

                    Producto::whereKey($producto->id)->decrement('existencia', $cantidad);

                    $subtotalVenta += $subtotalDetalle;
                }

                $venta->update([
                    'subtotal' => $subtotalVenta,
                ]);
            }

            $this->command?->info('Seeder terminado: se generaron 3000 registros de prueba.');
        });
    }
}
