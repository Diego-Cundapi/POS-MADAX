<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\User; // Añadir esta línea
use App\Models\Pedido; // Añadir esta línea

use Spatie\Permission\Models\Role;

class Clientes extends Component
{
    // Eliminamos public $clientes para evitar conflictos y uso innecesario de memoria

    public function mount()
    {
        // Asegurar que el rol 'Cliente' exista
        Role::firstOrCreate(['name' => 'Cliente', 'guard_name' => 'web']);
    }
    
    public function render()
    {
        // Obtener clientes y sus pedidos ordenados
        $clientes = User::role('Cliente')->with(['pedidos' => function($query) {
            $query->orderBy('id', 'desc');
        }])->get();

        // Mapear a un array simple para la vista
        $datosClientes = $clientes->map(function($cliente) {
            $ultimoPedido = $cliente->pedidos->first();
            
            $fechaMostrar = ''; // Dejar vacío si no hay compra
            $ultimoPedidoId = null;

            if ($ultimoPedido) {
                $ultimoPedidoId = $ultimoPedido->id;
                if ($ultimoPedido->fechapedido) {
                    try {
                        $fechaMostrar = \Carbon\Carbon::parse($ultimoPedido->fechapedido)->format('d/m/Y H:i');
                    } catch (\Exception $e) {
                        $fechaMostrar = $ultimoPedido->fechapedido;
                    }
                } else {
                    $fechaMostrar = 'Fecha desconocida';
                }
            }

            return [
                'id' => $cliente->id,
                'name' => $cliente->name,
                'email' => $cliente->email,
                'telefono' => $cliente->telefono,
                'direccion' => $cliente->direccion,
                'ultima_compra' => $fechaMostrar,
                'ultimo_pedido_id' => $ultimoPedidoId
            ];
        });

        return view('livewire.clientes', [
            'clientes' => $datosClientes
        ]);
    }
}
