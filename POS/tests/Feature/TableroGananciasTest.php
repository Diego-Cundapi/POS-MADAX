<?php

namespace Tests\Feature;

use App\Http\Livewire\Tablero;
use App\Models\DetalleCotizacion;
use App\Models\Cotizacion;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class TableroGananciasTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::create(2026, 8, 8, 12, 0, 0));

        DB::table('app_settings')->insert([
            'key' => 'app_activated',
            'value' => 'true',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->seed(RoleSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('Admin');
        $this->actingAs($admin);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    protected function crearFixturesDeVentas(): void
    {
        // Producto A: costo definido
        $productoA = Producto::factory()->create(['precio' => 100.00, 'costo' => 60.00]);
        // Producto B: sin costo (producto legado) -> se trata como costo 0
        $productoB = Producto::factory()->create(['precio' => 50.00, 'costo' => null]);

        // Pedido de este mes: 2 lineas de detalle
        // Ganancia esperada: (100-60)*3 + (50-0)*2 = 120 + 100 = 220
        $pedidoEsteMes = Pedido::create([
            'subtotal' => 400.00,
            'impuesto' => 0,
            'descuento' => 0,
            'total' => 400.00,
            'fechapedido' => Carbon::now(),
            'estado' => 'Entregado',
            'user_id' => User::factory()->create()->id,
        ]);
        $pedidoEsteMes->detalles()->create(['producto_id' => $productoA->id, 'precio' => 100.00, 'cantidad' => 3, 'importe' => 300.00]);
        $pedidoEsteMes->detalles()->create(['producto_id' => $productoB->id, 'precio' => 50.00, 'cantidad' => 2, 'importe' => 100.00]);

        // Pedido del mes anterior: ganancia esperada (100-60)*1 = 40
        $pedidoMesAnterior = Pedido::create([
            'subtotal' => 100.00,
            'impuesto' => 0,
            'descuento' => 0,
            'total' => 100.00,
            'fechapedido' => Carbon::now()->subMonth(),
            'estado' => 'Entregado',
            'user_id' => User::factory()->create()->id,
        ]);
        $pedidoMesAnterior->detalles()->create(['producto_id' => $productoA->id, 'precio' => 100.00, 'cantidad' => 1, 'importe' => 100.00]);

        // Pedido de hace 3 meses: no debe afectar ni el mes actual ni el anterior
        $pedidoViejo = Pedido::create([
            'subtotal' => 1000.00,
            'impuesto' => 0,
            'descuento' => 0,
            'total' => 1000.00,
            'fechapedido' => Carbon::now()->subMonths(3),
            'estado' => 'Entregado',
            'user_id' => User::factory()->create()->id,
        ]);
        $pedidoViejo->detalles()->create(['producto_id' => $productoA->id, 'precio' => 100.00, 'cantidad' => 10, 'importe' => 1000.00]);
    }

    public function test_ganancias_se_calcula_para_el_mes_actual_tratando_costo_nulo_como_cero(): void
    {
        $this->crearFixturesDeVentas();

        Livewire::test(Tablero::class)
            ->assertSet('ganancias', 220.00);
    }

    public function test_crecimiento_de_ganancias_respecto_al_mes_anterior(): void
    {
        $this->crearFixturesDeVentas();

        // (220 - 40) / 40 * 100 = 450
        Livewire::test(Tablero::class)
            ->assertSet('crecimientoGanancias', 450.0);
    }

    public function test_ganancias_excluye_cotizaciones(): void
    {
        $this->crearFixturesDeVentas();

        $producto = Producto::factory()->create(['precio' => 100.00, 'costo' => 60.00]);
        $cotizacion = Cotizacion::create([
            'subtotal' => 5000.00,
            'impuesto' => 0,
            'total' => 5000.00,
            'cliente_nombre' => 'Cliente de prueba',
            'estado' => 'pendiente',
            'user_id' => User::factory()->create()->id,
        ]);
        DetalleCotizacion::create([
            'cotizacion_id' => $cotizacion->id,
            'producto_id' => $producto->id,
            'cantidad' => 50,
            'precio' => 100.00,
            'importe' => 5000.00,
        ]);

        // La ganancia debe seguir siendo 220 (las cotizaciones no son ventas reales)
        Livewire::test(Tablero::class)
            ->assertSet('ganancias', 220.00);
    }

    public function test_tarjeta_ganancias_se_muestra_en_lugar_de_nuevos_clientes(): void
    {
        $this->crearFixturesDeVentas();

        $response = $this->get('/dashboard/tablero');

        $response->assertStatus(200);
        $response->assertSee('Ganancias');
        $response->assertSee('de crecimiento de Ganancias');
        $response->assertDontSee('Nuevos Clientes');
    }
}
