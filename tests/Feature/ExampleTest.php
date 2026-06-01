<?php

namespace Tests\Feature;

use App\Models\User;
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
}
