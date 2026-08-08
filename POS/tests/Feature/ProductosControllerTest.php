<?php

namespace Tests\Feature;

use App\Models\Categories;
use App\Models\Producto;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ProductosControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // La app requiere estar "activada" (ver CheckAppActivation) para acceder a cualquier ruta del dashboard.
        DB::table('app_settings')->insert([
            'key' => 'app_activated',
            'value' => 'true',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->seed(RoleSeeder::class);
    }

    protected function actingAsAdmin(): User
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');
        $this->actingAs($admin);

        return $admin;
    }

    public function test_store_persists_costo(): void
    {
        $this->actingAsAdmin();
        $categoria = Categories::create(['name' => 'General', 'color' => '#000000']);

        $response = $this->post(route('productos.store'), [
            'nombre' => 'Producto de prueba',
            'categories_id' => $categoria->id,
            'modelo' => '2024',
            'marca' => 'MarcaX',
            'precio' => 100.00,
            'costo' => 25.00,
            'clave' => 'SKU-COSTO-1',
            'disponible' => 10,
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('productos', [
            'clave' => 'SKU-COSTO-1',
            'costo' => 25.00,
        ]);
    }

    public function test_store_allows_costo_to_be_omitted(): void
    {
        $this->actingAsAdmin();
        $categoria = Categories::create(['name' => 'General', 'color' => '#000000']);

        $response = $this->post(route('productos.store'), [
            'nombre' => 'Producto sin costo',
            'categories_id' => $categoria->id,
            'modelo' => '2024',
            'marca' => 'MarcaX',
            'precio' => 80.00,
            'clave' => 'SKU-SIN-COSTO',
            'disponible' => 5,
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('productos', [
            'clave' => 'SKU-SIN-COSTO',
            'costo' => null,
        ]);
    }

    public function test_update_persists_costo(): void
    {
        $this->actingAsAdmin();
        $categoria = Categories::create(['name' => 'General', 'color' => '#000000']);

        $producto = Producto::factory()->create(['costo' => null, 'categories_id' => $categoria->id]);

        $response = $this->put(route('productos.update', $producto->id), [
            'nombre' => $producto->nombre,
            'categories_id' => $categoria->id,
            'modelo' => $producto->modelo,
            'marca' => $producto->marca,
            'precio' => $producto->precio,
            'costo' => 60.00,
            'clave' => $producto->clave,
            'disponible' => $producto->disponible,
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertEquals(60.00, (float) $producto->fresh()->costo);
    }
}
