<?php

namespace App\Http\Livewire;
use Livewire\Component;
use App\Models\Pedido;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class Tablero extends Component
{
    public $ventas = 0;
    public $ganancias = 0;
    public $crecimientoGanancias = 0;
    public $productosVendidos = 0;
    public $crecimientoProductos = 0;
    public $ventasUltimos6Meses = [];
    public $productosMasVendidos =[];
    public $pedidosRecientes = [];

    public function render()
    {
        // Calcular el total de los pedidos del mes actual (subtotal + impuesto)
        $this->ventas = Pedido::whereYear('fechapedido', Carbon::now()->year)
            ->whereMonth('fechapedido', Carbon::now()->month)
            ->sum(DB::raw('subtotal + impuesto'));

        // Calcular las ganancias netas del mes actual: (precio_venta - costo) * cantidad por cada linea de detalle
        // Si el producto no tiene costo definido, se trata como 0 (se cuenta el precio de venta completo)
        $this->ganancias = DB::table('detalles')
            ->join('pedidos', 'pedidos.id', '=', 'detalles.pedido_id')
            ->join('productos', 'productos.id', '=', 'detalles.producto_id')
            ->whereYear('pedidos.fechapedido', Carbon::now()->year)
            ->whereMonth('pedidos.fechapedido', Carbon::now()->month)
            ->sum(DB::raw('(detalles.precio - COALESCE(productos.costo, 0)) * detalles.cantidad'));

        // Calcular las ganancias netas del mes anterior
        $gananciasMesAnterior = DB::table('detalles')
            ->join('pedidos', 'pedidos.id', '=', 'detalles.pedido_id')
            ->join('productos', 'productos.id', '=', 'detalles.producto_id')
            ->whereYear('pedidos.fechapedido', Carbon::now()->year)
            ->whereMonth('pedidos.fechapedido', Carbon::now()->subMonth()->month)
            ->sum(DB::raw('(detalles.precio - COALESCE(productos.costo, 0)) * detalles.cantidad'));

        // Calcular el porcentaje de crecimiento o decremento de ganancias
        if ($gananciasMesAnterior > 0) {
            $this->crecimientoGanancias = (($this->ganancias - $gananciasMesAnterior) / $gananciasMesAnterior) * 100;
        } else {
            $this->crecimientoGanancias = $this->ganancias > 0 ? (($this->ganancias - $gananciasMesAnterior) / 1) * 100 : 0;
        }

        // Calcular la cantidad de productos vendidos en el mes actual
        $this->productosVendidos = Pedido::whereYear('fechapedido', Carbon::now()->year)
            ->whereMonth('fechapedido', Carbon::now()->month)
            ->join('detalles', 'pedidos.id', '=', 'detalles.pedido_id')
            ->sum('detalles.cantidad');

        // Calcular la cantidad de productos vendidos en el mes anterior
        $productosMesAnterior = Pedido::whereYear('fechapedido', Carbon::now()->year)
            ->whereMonth('fechapedido', Carbon::now()->subMonth()->month)
            ->join('detalles', 'pedidos.id', '=', 'detalles.pedido_id')
            ->sum('detalles.cantidad');

        // Calcular el porcentaje de crecimiento o decremento de productos vendidos
        if ($productosMesAnterior > 0) {
            $this->crecimientoProductos = (($this->productosVendidos - $productosMesAnterior) / $productosMesAnterior) * 100;
        } else {
            $this->crecimientoProductos = $this->productosVendidos > 0 ? (($this->productosVendidos - $productosMesAnterior) / 1) * 100 : 0;
        }

        // Calcular las ventas de los ultimos 6 meses
        for($i = 0; $i < 6; $i++){
            $mes = Carbon::now()->subMonths($i);
            $ventasMes = Pedido::whereYear('fechapedido', $mes->year)
                        ->whereMonth('fechapedido', $mes->month)
                        ->sum(DB::raw('subtotal + impuesto'));
            $this->ventasUltimos6Meses[$mes->format('F Y')] = $ventasMes;
        }

        // Obtener los primeros 6 productos mas vendidos
        $this->productosMasVendidos = DB::table('productos')
            ->join('detalles', 'productos.id', '=', 'detalles.producto_id')
            ->whereNull('productos.deleted_at')
            ->select('productos.nombre', 
            DB::raw('SUM(detalles.cantidad) as cantidad'), 
            DB::raw('ROUND(SUM(detalles.cantidad * detalles.precio * 1.16), 2) as total_generado')) // 1.16 es el impuesto
            ->groupBy('productos.id', 'productos.nombre')
            ->orderByDesc('cantidad')
            ->limit(6)
            ->get();

        return view('livewire.tablero', [
            'ventas' => $this->ventas,
            'ganancias' => $this->ganancias,
            'crecimientoGanancias' => $this->crecimientoGanancias,
            'productosVendidos' => $this->productosVendidos,
            'crecimientoProductos' => $this->crecimientoProductos,
            'ventasUltimos6Meses' => $this->crecimientoProductos,
            'productosMasVendidos' => $this->productosMasVendidos
        ]);
    }
}

