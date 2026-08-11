<?php

namespace Database\Seeders;

use App\Models\Categories;
use App\Models\Cotizacion;
use App\Models\DetalleCotizacion;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DemoDataSeeder extends Seeder
{
    /**
     * Puebla la base de datos con clientes, productos, ventas y cotizaciones
     * de ejemplo, para poder probar el dashboard con datos realistas.
     */
    public function run(): void
    {
        $admin = User::whereHas('roles', fn ($q) => $q->where('name', 'Admin'))->first();

        // 1. Categorias
        $categorias = collect([
            ['name' => 'Frenos', 'color' => '#EF4444'],
            ['name' => 'Suspensión', 'color' => '#3B82F6'],
            ['name' => 'Motor', 'color' => '#F59E0B'],
            ['name' => 'Eléctrico', 'color' => '#10B981'],
        ])->map(fn ($c) => Categories::firstOrCreate(['name' => $c['name']], $c));

        // 2. Productos (precio de venta y costo, para que Ganancias tenga datos reales)
        $catalogo = [
            ['nombre' => 'Balatas delanteras cerámicas', 'marca' => 'Brembo', 'precio' => 850, 'costo' => 520, 'categoria' => 'Frenos'],
            ['nombre' => 'Disco de freno ventilado', 'marca' => 'ATE', 'precio' => 1200, 'costo' => 780, 'categoria' => 'Frenos'],
            ['nombre' => 'Kit de balatas traseras', 'marca' => 'Bosch', 'precio' => 690, 'costo' => 410, 'categoria' => 'Frenos'],
            ['nombre' => 'Amortiguador delantero', 'marca' => 'Monroe', 'precio' => 1450, 'costo' => 950, 'categoria' => 'Suspensión'],
            ['nombre' => 'Amortiguador trasero', 'marca' => 'KYB', 'precio' => 1300, 'costo' => 860, 'categoria' => 'Suspensión'],
            ['nombre' => 'Rótula de suspensión', 'marca' => 'Moog', 'precio' => 480, 'costo' => 290, 'categoria' => 'Suspensión'],
            ['nombre' => 'Bomba de aceite', 'marca' => 'Melling', 'precio' => 950, 'costo' => 610, 'categoria' => 'Motor'],
            ['nombre' => 'Kit de distribución', 'marca' => 'Gates', 'precio' => 2100, 'costo' => 1350, 'categoria' => 'Motor'],
            ['nombre' => 'Filtro de aceite', 'marca' => 'Mann', 'precio' => 180, 'costo' => 95, 'categoria' => 'Motor'],
            ['nombre' => 'Bujía de iridio', 'marca' => 'NGK', 'precio' => 220, 'costo' => 120, 'categoria' => 'Motor'],
            ['nombre' => 'Batería 12V 60Ah', 'marca' => 'LTH', 'precio' => 2450, 'costo' => 1700, 'categoria' => 'Eléctrico'],
            ['nombre' => 'Alternador remanufacturado', 'marca' => 'Bosch', 'precio' => 1800, 'costo' => 1150, 'categoria' => 'Eléctrico'],
            // Producto sin costo asignado, para probar el caso "costo nulo" en Ganancias
            ['nombre' => 'Sensor de oxígeno', 'marca' => 'Denso', 'precio' => 650, 'costo' => null, 'categoria' => 'Eléctrico'],
        ];

        $productos = collect($catalogo)->map(function ($p) use ($categorias) {
            return Producto::firstOrCreate(
                ['clave' => 'DEMO-'.str($p['nombre'])->slug()->upper()],
                [
                    'nombre' => $p['nombre'],
                    'categories_id' => $categorias->firstWhere('name', $p['categoria'])->id,
                    'modelo' => 'Universal',
                    'marca' => $p['marca'],
                    'precio' => $p['precio'],
                    'costo' => $p['costo'],
                    'descripcion' => $p['nombre'].' - '.$p['marca'],
                    'disponible' => fake()->numberBetween(3, 40),
                ]
            );
        });

        // 3. Clientes
        $clientes = User::factory(8)->create()->each(function ($user) {
            $user->assignRole('Cliente');
        });

        // 4. Ventas (pedidos + detalles), repartidas en los ultimos 4 meses
        for ($i = 0; $i < 18; $i++) {
            $fecha = Carbon::now()->subDays(fake()->numberBetween(0, 120));
            $lineas = $productos->random(fake()->numberBetween(1, 3));

            $subtotal = 0;
            $detallesData = $lineas->map(function ($producto) use (&$subtotal) {
                $cantidad = fake()->numberBetween(1, 4);
                $importe = $producto->precio * $cantidad;
                $subtotal += $importe;

                return [
                    'producto_id' => $producto->id,
                    'precio' => $producto->precio,
                    'cantidad' => $cantidad,
                    'importe' => $importe,
                ];
            });

            $impuesto = round($subtotal * 0.16, 2);

            $pedido = Pedido::create([
                'user_id' => $clientes->random()->id,
                'vendedor_id' => $admin?->id,
                'subtotal' => $subtotal,
                'impuesto' => $impuesto,
                'descuento' => 0,
                'total' => $subtotal + $impuesto,
                'fechapedido' => $fecha,
                'estado' => fake()->randomElement(['Nuevo', 'Proceso', 'Entregado']),
            ]);

            foreach ($detallesData as $detalle) {
                $pedido->detalles()->create($detalle);
            }
        }

        // 5. Cotizaciones
        for ($i = 0; $i < 6; $i++) {
            $lineas = $productos->random(fake()->numberBetween(1, 3));

            $subtotal = 0;
            $detallesData = $lineas->map(function ($producto) use (&$subtotal) {
                $cantidad = fake()->numberBetween(1, 3);
                $importe = $producto->precio * $cantidad;
                $subtotal += $importe;

                return [
                    'producto_id' => $producto->id,
                    'precio' => $producto->precio,
                    'cantidad' => $cantidad,
                    'importe' => $importe,
                ];
            });

            $impuesto = round($subtotal * 0.16, 2);

            $cotizacion = Cotizacion::create([
                'user_id' => $clientes->random()->id,
                'cliente_nombre' => fake()->name(),
                'cliente_email' => fake()->safeEmail(),
                'subtotal' => $subtotal,
                'impuesto' => $impuesto,
                'total' => $subtotal + $impuesto,
                'estado' => fake()->randomElement(['pendiente', 'aprobada', 'rechazada']),
            ]);

            foreach ($detallesData as $detalle) {
                DetalleCotizacion::create([...$detalle, 'cotizacion_id' => $cotizacion->id]);
            }
        }
    }
}
