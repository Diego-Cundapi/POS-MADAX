<?php

namespace App\Http\Livewire;
use Livewire\Component;
use App\Models\Pedido;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class Tablero extends Component
{
    public $ventas = 0;
    public $nuevosClientes = 0;
    public $crecimientoClientes = 0;
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

        // Calcular el número de nuevos clientes del mes actual
        $this->nuevosClientes = User::whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->count();

        // Calcular el número de clientes del mes anterior
        $clientesMesAnterior = User::whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->count();

        // Calcular el porcentaje de crecimiento o decremento de clientes
        if ($clientesMesAnterior > 0) {
            $this->crecimientoClientes = (($this->nuevosClientes - $clientesMesAnterior) / $clientesMesAnterior) * 100;
        } else {
            $this->crecimientoClientes = $this->nuevosClientes > 0 ? (($this->nuevosClientes - $clientesMesAnterior) / 1) * 100 : 0;
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
            'nuevosClientes' => $this->nuevosClientes,
            'crecimientoClientes' => $this->crecimientoClientes,
            'productosVendidos' => $this->productosVendidos,
            'crecimientoProductos' => $this->crecimientoProductos,
            'ventasUltimos6Meses' => $this->crecimientoProductos,
            'productosMasVendidos' => $this->productosMasVendidos
        ]);
    }
}

