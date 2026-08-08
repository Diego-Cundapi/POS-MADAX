<?php

namespace Tests\Unit;

use App\Models\Producto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductoCostoTest extends TestCase
{
    use RefreshDatabase;

    public function test_costo_can_be_null(): void
    {
        $producto = Producto::factory()->create(['costo' => null]);

        $this->assertNull($producto->costo);
        $this->assertNull($producto->fresh()->costo);
    }

    public function test_costo_is_persisted_correctly(): void
    {
        $producto = Producto::factory()->create(['costo' => 45.50]);

        $this->assertEquals(45.50, (float) $producto->fresh()->costo);
    }
}
