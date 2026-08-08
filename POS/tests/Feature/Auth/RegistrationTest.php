<?php

namespace Tests\Feature\Auth;

use App\Providers\RouteServiceProvider;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // El listener AssignDefaultRole asigna el rol 'Cliente' a cada usuario registrado.
        $this->seed(RoleSeeder::class);
    }

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'telefono' => '1234567890',
            'direccion' => 'Calle 123',
            'ciudad' => 'Bogotá',
            'estado' => 'Cundinamarca',
            'codigo_postal' => '12345',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(RouteServiceProvider::HOME);
    }
}
