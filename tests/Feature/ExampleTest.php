<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Cliente;
use App\Models\Cita;
use App\Models\Compra;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Venta;
use App\Models\Vehiculo;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use DatabaseMigrations;

    protected function migrateFreshUsing(): array
    {
        return [
            '--drop-views' => false,
            '--drop-types' => false,
            '--seed' => false,
            '--schema-path' => database_path('schema/no-schema-dump-for-tests.sql'),
        ];
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('login'));
    }

    public function test_auth_pages_can_be_rendered(): void
    {
        $this->get(route('login'))->assertOk();
        $this->get(route('register'))->assertOk();
    }

    public function test_user_can_register_and_is_authenticated(): void
    {
        $email = 'registro-test-'.uniqid().'@example.com';

        $response = $this->post(route('register.post'), [
            'name' => 'Usuario de prueba',
            'email' => $email,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => $email,
        ]);
    }

    public function test_authenticated_user_can_create_and_view_clients(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('clientes.store'), [
            'nombre' => 'Cliente de prueba',
            'telefono' => '443 000 0000',
            'correo' => 'cliente@example.com',
            'direccion' => 'Zitacuaro, Michoacan',
            'estado' => 'activo',
            'observaciones' => 'Cliente generado desde prueba.',
        ]);

        $response->assertRedirect(route('clientes.index'));
        $this->assertDatabaseHas('clientes', [
            'nombre' => 'Cliente de prueba',
            'correo' => 'cliente@example.com',
        ]);

        $this->actingAs($user)
            ->get(route('clientes.index'))
            ->assertOk()
            ->assertSee('Cliente de prueba')
            ->assertSee('Cliente generado desde prueba.');
    }

    public function test_authenticated_user_can_create_and_filter_vehicles(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('clientes.store'), [
            'nombre' => 'Cliente con vehiculo',
            'telefono' => '443 111 2222',
            'correo' => 'vehiculo@example.com',
            'direccion' => 'Zitacuaro, Michoacan',
            'estado' => 'activo',
            'observaciones' => null,
        ]);

        $clienteId = \App\Models\Cliente::where('correo', 'vehiculo@example.com')->value('id');

        $response = $this->actingAs($user)->post(route('vehiculos.store'), [
            'cliente_id' => $clienteId,
            'tipo' => 'auto',
            'marca' => 'Nissan',
            'modelo' => 'Versa',
            'anio' => 2022,
            'placas' => 'ABC-123',
            'kilometraje_actual' => 45000,
            'tipo_combustible' => 'gasolina',
            'estado' => 'activo',
            'observaciones' => 'Vehiculo registrado desde prueba.',
        ]);

        $response->assertRedirect(route('vehiculos.index'));
        $this->assertDatabaseHas('vehiculos', [
            'cliente_id' => $clienteId,
            'tipo' => 'auto',
            'modelo' => 'Versa',
            'anio' => 2022,
            'placas' => 'ABC-123',
        ]);

        $this->actingAs($user)
            ->get(route('vehiculos.index', ['buscar' => 'Cliente con vehiculo']))
            ->assertOk()
            ->assertSee('Cliente con vehiculo')
            ->assertSee('Versa')
            ->assertSee('ABC-123');
    }

    public function test_authenticated_user_can_update_and_deactivate_clients(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('clientes.store'), [
            'nombre' => 'Cliente editable',
            'telefono' => '443 222 3333',
            'correo' => 'editable@example.com',
            'direccion' => 'Zitacuaro',
            'estado' => 'activo',
            'observaciones' => 'Antes de editar.',
        ]);

        $cliente = Cliente::where('correo', 'editable@example.com')->firstOrFail();

        $this->actingAs($user)->put(route('clientes.update', $cliente), [
            'nombre' => 'Cliente actualizado',
            'telefono' => '443 999 8888',
            'correo' => 'actualizado@example.com',
            'direccion' => 'Morelia',
            'estado' => 'activo',
            'observaciones' => 'Despues de editar.',
        ])->assertRedirect(route('clientes.index'));

        $this->assertDatabaseHas('clientes', [
            'id' => $cliente->id,
            'nombre' => 'Cliente actualizado',
            'correo' => 'actualizado@example.com',
        ]);

        $this->actingAs($user)
            ->delete(route('clientes.destroy', $cliente))
            ->assertRedirect(route('clientes.index'));

        $this->assertDatabaseHas('clientes', [
            'id' => $cliente->id,
            'estado' => 'inactivo',
        ]);
    }

    public function test_authenticated_user_can_update_and_deactivate_vehicles(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('clientes.store'), [
            'nombre' => 'Cliente vehiculo editable',
            'telefono' => '443 555 6666',
            'correo' => 'vehiculo-editable@example.com',
            'direccion' => 'Zitacuaro',
            'estado' => 'activo',
            'observaciones' => null,
        ]);

        $cliente = Cliente::where('correo', 'vehiculo-editable@example.com')->firstOrFail();

        $this->actingAs($user)->post(route('vehiculos.store'), [
            'cliente_id' => $cliente->id,
            'tipo' => 'auto',
            'marca' => 'Nissan',
            'modelo' => 'Versa',
            'anio' => 2022,
            'placas' => 'EDIT-1',
            'kilometraje_actual' => 45000,
            'tipo_combustible' => 'gasolina',
            'estado' => 'activo',
            'observaciones' => 'Antes de editar.',
        ]);

        $vehiculo = Vehiculo::where('placas', 'EDIT-1')->firstOrFail();

        $this->actingAs($user)->put(route('vehiculos.update', $vehiculo), [
            'cliente_id' => $cliente->id,
            'tipo' => 'camioneta',
            'marca' => 'Toyota',
            'modelo' => 'Hilux',
            'anio' => 2024,
            'placas' => 'EDIT-2',
            'kilometraje_actual' => 12000,
            'tipo_combustible' => 'diesel',
            'estado' => 'en_servicio',
            'observaciones' => 'Despues de editar.',
        ])->assertRedirect(route('vehiculos.index'));

        $this->assertDatabaseHas('vehiculos', [
            'id' => $vehiculo->id,
            'tipo' => 'camioneta',
            'marca' => 'Toyota',
            'modelo' => 'Hilux',
            'placas' => 'EDIT-2',
        ]);

        $this->actingAs($user)
            ->delete(route('vehiculos.destroy', $vehiculo))
            ->assertRedirect(route('vehiculos.index'));

        $this->assertDatabaseHas('vehiculos', [
            'id' => $vehiculo->id,
            'estado' => 'inactivo',
        ]);
    }

    public function test_appointments_block_their_full_service_duration(): void
    {
        Carbon::setTestNow('2026-06-04 09:00:00');

        $user = User::factory()->create();
        $cliente = Cliente::create([
            'nombre' => 'Cliente con cita',
            'telefono' => '443 777 8888',
            'correo' => 'cita@example.com',
            'direccion' => 'Zitacuaro',
            'estado' => 'activo',
            'observaciones' => null,
        ]);

        $this->actingAs($user)->post(route('citas.store'), [
            'cliente_id' => $cliente->id,
            'vehiculo_id' => null,
            'servicio' => 'Mantenimiento preventivo',
            'fecha' => '2026-06-15',
            'hora' => '09:00',
            'estado' => 'pendiente',
            'observaciones' => null,
        ])->assertRedirect(route('citas.index', ['mes' => '2026-06']));

        $this->actingAs($user)->post(route('citas.store'), [
            'cliente_id' => $cliente->id,
            'vehiculo_id' => null,
            'servicio' => 'Cambio de aceite',
            'fecha' => '2026-06-15',
            'hora' => '10:30',
            'estado' => 'pendiente',
            'observaciones' => null,
        ])->assertSessionHasErrors('hora');

        $this->actingAs($user)->post(route('citas.store'), [
            'cliente_id' => $cliente->id,
            'vehiculo_id' => null,
            'servicio' => 'Cambio de aceite',
            'fecha' => '2026-06-15',
            'hora' => '11:00',
            'estado' => 'pendiente',
            'observaciones' => null,
        ])->assertRedirect(route('citas.index', ['mes' => '2026-06']));

        $this->assertSame(2, Cita::where('fecha', '2026-06-15')->count());
    }

    public function test_appointments_only_accept_30_minute_time_slots(): void
    {
        Carbon::setTestNow('2026-06-04 09:00:00');

        $user = User::factory()->create();
        $cliente = Cliente::create([
            'nombre' => 'Cliente hora invalida',
            'telefono' => '443 123 4545',
            'correo' => 'hora-invalida@example.com',
            'direccion' => 'Zitacuaro',
            'estado' => 'activo',
            'observaciones' => null,
        ]);

        $this->actingAs($user)->post(route('citas.store'), [
            'cliente_id' => $cliente->id,
            'vehiculo_id' => null,
            'servicio' => 'Cambio de aceite',
            'fecha' => '2026-06-16',
            'hora' => '09:15',
            'estado' => 'pendiente',
            'observaciones' => null,
        ])->assertSessionHasErrors('hora');

        $this->assertDatabaseMissing('citas', [
            'fecha' => '2026-06-16',
            'hora' => '09:15',
        ]);
    }

    public function test_appointments_cannot_be_scheduled_in_the_past_or_on_sundays(): void
    {
        Carbon::setTestNow('2026-06-04 09:00:00');

        $user = User::factory()->create();
        $cliente = Cliente::create([
            'nombre' => 'Cliente fecha invalida',
            'telefono' => '443 222 1111',
            'correo' => 'fecha-invalida@example.com',
            'direccion' => 'Zitacuaro',
            'estado' => 'activo',
            'observaciones' => null,
        ]);

        $this->actingAs($user)->post(route('citas.store'), [
            'cliente_id' => $cliente->id,
            'vehiculo_id' => null,
            'servicio' => 'Cambio de aceite',
            'fecha' => '2026-06-04',
            'hora' => '08:30',
            'estado' => 'pendiente',
            'observaciones' => null,
        ])->assertSessionHasErrors('hora');

        $this->actingAs($user)->post(route('citas.store'), [
            'cliente_id' => $cliente->id,
            'vehiculo_id' => null,
            'servicio' => 'Cambio de aceite',
            'fecha' => '2026-06-07',
            'hora' => '10:00',
            'estado' => 'pendiente',
            'observaciones' => null,
        ])->assertSessionHasErrors('fecha');

        $this->assertSame(0, Cita::count());
    }

    public function test_appointment_vehicle_must_belong_to_selected_client(): void
    {
        Carbon::setTestNow('2026-06-04 09:00:00');

        $user = User::factory()->create();
        $cliente = Cliente::create([
            'nombre' => 'Cliente correcto',
            'telefono' => '443 333 4444',
            'correo' => 'cliente-correcto@example.com',
            'direccion' => 'Zitacuaro',
            'estado' => 'activo',
            'observaciones' => null,
        ]);
        $otroCliente = Cliente::create([
            'nombre' => 'Cliente distinto',
            'telefono' => '443 555 6666',
            'correo' => 'cliente-distinto@example.com',
            'direccion' => 'Morelia',
            'estado' => 'activo',
            'observaciones' => null,
        ]);
        $vehiculoDeOtroCliente = Vehiculo::create([
            'cliente_id' => $otroCliente->id,
            'tipo' => 'auto',
            'marca' => 'Nissan',
            'modelo' => 'Versa',
            'anio' => 2022,
            'placas' => 'OTRO-1',
            'kilometraje_actual' => 15000,
            'tipo_combustible' => 'gasolina',
            'estado' => 'activo',
            'observaciones' => null,
        ]);

        $this->actingAs($user)->post(route('citas.store'), [
            'cliente_id' => $cliente->id,
            'vehiculo_id' => $vehiculoDeOtroCliente->id,
            'servicio' => 'Cambio de aceite',
            'fecha' => '2026-06-08',
            'hora' => '10:00',
            'estado' => 'pendiente',
            'observaciones' => null,
        ])->assertSessionHasErrors('vehiculo_id');

        $this->assertDatabaseMissing('citas', [
            'vehiculo_id' => $vehiculoDeOtroCliente->id,
        ]);
    }

    public function test_authenticated_user_can_create_and_filter_inventory_products(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('inventario.store'), [
            'nombre' => 'Aceite 5W-30',
            'categoria' => 'Lubricantes',
            'marca' => 'Mobil',
            'sku' => 'ACE-5W30',
            'unidad' => 'litro',
            'existencia' => 12,
            'stock_minimo' => 5,
            'precio_compra' => 120,
            'precio_venta' => 180,
            'proveedor' => 'Proveedor de prueba',
            'ubicacion' => 'Estante A1',
            'estado' => 'activo',
            'observaciones' => 'Producto registrado desde prueba.',
        ])->assertRedirect(route('inventario.index'));

        $this->assertDatabaseHas('productos', [
            'nombre' => 'Aceite 5W-30',
            'sku' => 'ACE-5W30',
            'existencia' => 12,
        ]);

        $this->actingAs($user)
            ->get(route('inventario.index', ['buscar' => 'Mobil']))
            ->assertOk()
            ->assertSee('Aceite 5W-30')
            ->assertSee('Mobil')
            ->assertSee('Disponible');
    }

    public function test_authenticated_user_can_update_inventory_products(): void
    {
        $user = User::factory()->create();
        $producto = Producto::create([
            'nombre' => 'Filtro de aceite',
            'categoria' => 'Filtros',
            'marca' => 'Gonher',
            'sku' => 'FIL-001',
            'unidad' => 'pieza',
            'existencia' => 3,
            'stock_minimo' => 5,
            'precio_compra' => 60,
            'precio_venta' => 95,
            'proveedor' => 'Proveedor inicial',
            'ubicacion' => 'Estante B2',
            'estado' => 'activo',
            'observaciones' => null,
        ]);

        $this->actingAs($user)->put(route('inventario.update', $producto), [
            'nombre' => 'Filtro de aceite premium',
            'categoria' => 'Filtros',
            'marca' => 'Gonher',
            'sku' => 'FIL-001',
            'unidad' => 'pieza',
            'existencia' => 10,
            'stock_minimo' => 4,
            'precio_compra' => 70,
            'precio_venta' => 120,
            'proveedor' => 'Proveedor actualizado',
            'ubicacion' => 'Estante C1',
            'estado' => 'activo',
            'observaciones' => 'Actualizado desde prueba.',
        ])->assertRedirect(route('inventario.index'));

        $this->assertDatabaseHas('productos', [
            'id' => $producto->id,
            'nombre' => 'Filtro de aceite premium',
            'existencia' => 10,
            'precio_venta' => 120,
        ]);
    }

    public function test_authenticated_user_can_manage_suppliers(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('proveedores.store'), [
            'nombre' => 'Refacciones del Centro',
            'rfc' => 'RCE260604AA1',
            'telefono' => '443 101 2020',
            'correo' => 'compras@refacciones.test',
            'direccion' => 'Centro',
            'contacto' => 'Laura Perez',
            'estado' => 'activo',
            'observaciones' => 'Proveedor generado desde prueba.',
        ])->assertRedirect(route('proveedores.index'));

        $proveedor = Proveedor::where('rfc', 'RCE260604AA1')->firstOrFail();

        $this->actingAs($user)
            ->get(route('proveedores.index', ['buscar' => 'Laura']))
            ->assertOk()
            ->assertSee('Refacciones del Centro')
            ->assertSee('Laura Perez');

        $this->actingAs($user)->put(route('proveedores.update', $proveedor), [
            'nombre' => 'Refacciones del Centro Actualizado',
            'rfc' => 'RCE260604AA1',
            'telefono' => '443 303 4040',
            'correo' => 'ventas@refacciones.test',
            'direccion' => 'Morelia',
            'contacto' => 'Laura Perez',
            'estado' => 'activo',
            'observaciones' => 'Actualizado desde prueba.',
        ])->assertRedirect(route('proveedores.index'));

        $this->assertDatabaseHas('proveedores', [
            'id' => $proveedor->id,
            'nombre' => 'Refacciones del Centro Actualizado',
            'telefono' => '443 303 4040',
        ]);

        $this->actingAs($user)
            ->delete(route('proveedores.destroy', $proveedor))
            ->assertRedirect(route('proveedores.index'));

        $this->assertDatabaseHas('proveedores', [
            'id' => $proveedor->id,
            'estado' => 'inactivo',
        ]);
    }

    public function test_purchase_cart_registers_multiple_products_and_updates_inventory(): void
    {
        $user = User::factory()->create();
        $proveedor = Proveedor::create([
            'nombre' => 'Lubricantes Express',
            'rfc' => 'LEX260604AA1',
            'telefono' => '443 909 8080',
            'correo' => 'contacto@lubricantes.test',
            'direccion' => 'Zitacuaro',
            'contacto' => 'Mario Lopez',
            'estado' => 'activo',
            'observaciones' => null,
        ]);
        $aceite = Producto::create([
            'nombre' => 'Aceite 5W-30',
            'categoria' => 'Lubricantes',
            'marca' => 'Mobil',
            'sku' => 'ACE-COMPRA',
            'unidad' => 'litro',
            'existencia' => 2,
            'stock_minimo' => 5,
            'precio_compra' => 100,
            'precio_venta' => 180,
            'proveedor' => 'Lubricantes Express',
            'ubicacion' => 'A1',
            'estado' => 'activo',
            'observaciones' => null,
        ]);
        $filtro = Producto::create([
            'nombre' => 'Filtro de aceite',
            'categoria' => 'Filtros',
            'marca' => 'Gonher',
            'sku' => 'FIL-COMPRA',
            'unidad' => 'pieza',
            'existencia' => 1,
            'stock_minimo' => 3,
            'precio_compra' => 60,
            'precio_venta' => 95,
            'proveedor' => 'Lubricantes Express',
            'ubicacion' => 'B1',
            'estado' => 'activo',
            'observaciones' => null,
        ]);

        $this->actingAs($user)->post(route('compras.carrito.agregar'), [
            'producto_id' => $aceite->id,
            'cantidad' => 4,
            'precio_unitario' => 110,
        ])->assertRedirect(route('compras.index'));

        $this->actingAs($user)->post(route('compras.carrito.agregar'), [
            'producto_id' => $filtro->id,
            'cantidad' => 6,
            'precio_unitario' => 65,
        ])->assertRedirect(route('compras.index'));

        $this->actingAs($user)->post(route('compras.store'), [
            'proveedor_id' => $proveedor->id,
            'fecha' => '2026-06-10',
            'observaciones' => 'Compra generada desde prueba.',
        ])->assertRedirect(route('compras.index'));

        $this->assertDatabaseHas('compras', [
            'proveedor_id' => $proveedor->id,
            'subtotal' => 830,
        ]);
        $this->assertDatabaseHas('compra_detalles', [
            'producto_id' => $aceite->id,
            'cantidad' => 4,
            'precio_unitario' => 110,
            'subtotal' => 440,
        ]);
        $this->assertDatabaseHas('compra_detalles', [
            'producto_id' => $filtro->id,
            'cantidad' => 6,
            'precio_unitario' => 65,
            'subtotal' => 390,
        ]);
        $this->assertDatabaseHas('productos', [
            'id' => $aceite->id,
            'existencia' => 6,
            'precio_compra' => 110,
        ]);
        $this->assertDatabaseHas('productos', [
            'id' => $filtro->id,
            'existencia' => 7,
            'precio_compra' => 65,
        ]);
        $this->assertSame(1, Compra::count());
    }

    public function test_purchase_product_search_matches_name_sku_category_or_brand(): void
    {
        $user = User::factory()->create();
        Producto::create([
            'nombre' => 'Anticongelante rojo',
            'categoria' => 'Consumibles',
            'marca' => 'Prestone',
            'sku' => 'SKU-ANTI',
            'unidad' => 'litro',
            'existencia' => 0,
            'stock_minimo' => 2,
            'precio_compra' => 75,
            'precio_venta' => 140,
            'proveedor' => null,
            'ubicacion' => null,
            'estado' => 'activo',
            'observaciones' => null,
        ]);

        $this->actingAs($user)
            ->get(route('compras.index', ['buscar_producto' => 'Prestone']))
            ->assertOk()
            ->assertSee('Anticongelante rojo');

        $this->actingAs($user)
            ->get(route('compras.index', ['buscar_producto' => 'SKU-ANTI']))
            ->assertOk()
            ->assertSee('Anticongelante rojo');

        $this->actingAs($user)
            ->get(route('compras.index', ['buscar_producto' => 'Consumibles']))
            ->assertOk()
            ->assertSee('Anticongelante rojo');
    }

    public function test_sale_cart_registers_multiple_products_and_decrements_inventory(): void
    {
        $user = User::factory()->create();
        $cliente = Cliente::create([
            'nombre' => 'Cliente venta',
            'telefono' => '443 777 0000',
            'correo' => 'venta@example.com',
            'direccion' => 'Zitacuaro',
            'estado' => 'activo',
            'observaciones' => null,
        ]);
        $aceite = Producto::create([
            'nombre' => 'Aceite 5W-30',
            'categoria' => 'Lubricantes',
            'marca' => 'Mobil',
            'sku' => 'VEN-ACE',
            'unidad' => 'litro',
            'existencia' => 10,
            'stock_minimo' => 3,
            'precio_compra' => 100,
            'precio_venta' => 180,
            'proveedor' => 'Lubricantes Express',
            'ubicacion' => 'A1',
            'estado' => 'activo',
            'observaciones' => null,
        ]);
        $filtro = Producto::create([
            'nombre' => 'Filtro de aceite',
            'categoria' => 'Filtros',
            'marca' => 'Gonher',
            'sku' => 'VEN-FIL',
            'unidad' => 'pieza',
            'existencia' => 8,
            'stock_minimo' => 2,
            'precio_compra' => 60,
            'precio_venta' => 95,
            'proveedor' => 'Refacciones',
            'ubicacion' => 'B1',
            'estado' => 'activo',
            'observaciones' => null,
        ]);

        $this->actingAs($user)->post(route('ventas.carrito.agregar'), [
            'producto_id' => $aceite->id,
            'cantidad' => 2,
            'precio_unitario' => 180,
        ])->assertRedirect(route('ventas.index'));

        $this->actingAs($user)->post(route('ventas.carrito.agregar'), [
            'producto_id' => $filtro->id,
            'cantidad' => 3,
            'precio_unitario' => 95,
        ])->assertRedirect(route('ventas.index'));

        $this->actingAs($user)->post(route('ventas.store'), [
            'cliente_id' => $cliente->id,
            'fecha' => '2026-06-12',
            'observaciones' => 'Venta generada desde prueba.',
        ])->assertRedirect(route('ventas.index'));

        $this->assertDatabaseHas('ventas', [
            'cliente_id' => $cliente->id,
            'subtotal' => 645,
        ]);
        $this->assertDatabaseHas('venta_detalles', [
            'producto_id' => $aceite->id,
            'cantidad' => 2,
            'precio_unitario' => 180,
            'subtotal' => 360,
        ]);
        $this->assertDatabaseHas('venta_detalles', [
            'producto_id' => $filtro->id,
            'cantidad' => 3,
            'precio_unitario' => 95,
            'subtotal' => 285,
        ]);
        $this->assertDatabaseHas('productos', [
            'id' => $aceite->id,
            'existencia' => 8,
        ]);
        $this->assertDatabaseHas('productos', [
            'id' => $filtro->id,
            'existencia' => 5,
        ]);
        $this->assertSame(1, Venta::count());
    }

    public function test_sales_product_search_matches_name_sku_category_or_brand(): void
    {
        $user = User::factory()->create();
        Producto::create([
            'nombre' => 'Bujia platino',
            'categoria' => 'Encendido',
            'marca' => 'NGK',
            'sku' => 'SKU-BUJIA',
            'unidad' => 'pieza',
            'existencia' => 5,
            'stock_minimo' => 1,
            'precio_compra' => 40,
            'precio_venta' => 80,
            'proveedor' => null,
            'ubicacion' => null,
            'estado' => 'activo',
            'observaciones' => null,
        ]);

        $this->actingAs($user)
            ->get(route('ventas.index', ['buscar_producto' => 'NGK']))
            ->assertOk()
            ->assertSee('Bujia platino');

        $this->actingAs($user)
            ->get(route('ventas.index', ['buscar_producto' => 'SKU-BUJIA']))
            ->assertOk()
            ->assertSee('Bujia platino');

        $this->actingAs($user)
            ->get(route('ventas.index', ['buscar_producto' => 'Encendido']))
            ->assertOk()
            ->assertSee('Bujia platino');
    }
}
