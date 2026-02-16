<?php

namespace App\Http\Livewire\Admin\Ventas;

use Livewire\Component;
use App\Models\Producto;
use App\Models\User;
use App\Models\Pedido;
use App\Models\Detalle;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;

class CrearVenta extends Component
{
    // --- BÚSQUEDA ---
    public $buscarClave = '';
    public $buscarNombre = '';
    public $resultadosClave = [];
    public $resultadosNombre = [];

    // --- CLIENTE ---
    public $cliente_id = "";
    public $buscarCliente = '';
    public $resultadosClientes = [];

    // --- DATOS ---
    public $items = [];
    public $ventaGuardadaId = null;
    public $fecha;

    // --- TOTALES ---
    // --- TOTALES ---
    public $subtotal = 0;
    public $iva = 0;
    public $descuento = 0;
    public $descuentoPorcentaje = 0;
    public $total = 0;

    public function mount()
    {
        $this->fecha = Carbon::now()->format('d/m/Y');
    }

    // --- BÚSQUEDA PRODUCTOS ---
    public function updatedBuscarClave()
    {
        $this->resultadosNombre = [];
        if (strlen($this->buscarClave) > 0) {
            $this->resultadosClave = Producto::where(function($query) {
                    $query->where('clave', 'like', trim($this->buscarClave) . '%')
                          ->orWhere('clave_proveedor', 'like', trim($this->buscarClave) . '%');
                })
                ->take(20)->get();
        } else {
            $this->resultadosClave = [];
        }
    }

    public function updatedBuscarNombre()
    {
        $this->resultadosClave = []; 
        if (strlen($this->buscarNombre) > 0) {
            $this->resultadosNombre = Producto::where('nombre', 'like', '%' . trim($this->buscarNombre) . '%')
                ->take(20)
                ->get();
        } else {
            $this->resultadosNombre = [];
        }
    }

    // --- BÚSQUEDA CLIENTES ---
    public function updatedBuscarCliente()
    {
        if (strlen($this->buscarCliente) == 0) {
            $this->resultadosClientes = [];
            $this->cliente_id = "";
            return;
        }

        $this->resultadosClientes = User::where('name', 'like', '%' . $this->buscarCliente . '%')
            ->orWhere('email', 'like', '%' . $this->buscarCliente . '%')
            ->take(5)
            ->get();
    }

    public function seleccionarCliente($id)
    {
        $cliente = User::find($id);
        if ($cliente) {
            $this->cliente_id = $cliente->id;
            $this->buscarCliente = $cliente->name;
            $this->resultadosClientes = [];
        }
    }

    public function resetClienteId()
    {
        $this->cliente_id = "";
        $this->buscarCliente = "";
        $this->resultadosClientes = [];
    }

    // --- OP VENTA ---
    public function seleccionarProducto($id)
    {
        $producto = Producto::find($id);

        if ($producto) {
            $encontrado = false;
            foreach ($this->items as $key => $item) {
                if ($item['id'] == $id) {
                    $this->items[$key]['cantidad']++;
                    $this->items[$key]['importe'] = $this->items[$key]['cantidad'] * $this->items[$key]['precio'];
                    $encontrado = true;
                    break;
                }
            }

            if (!$encontrado) {
                $this->items[] = [
                    'id' => $producto->id,
                    'clave' => $producto->clave ?? 'S/C',
                    'descripcion' => $producto->nombre ?? $producto->descripcion,
                    'cantidad' => 1,
                    // Convertimos precio a float para cálculos
                    'precio' => floatval($producto->precio),
                    'importe' => floatval($producto->precio)
                ];
            }
        }
        $this->limpiarBuscadores();
        $this->calcularTotales();
    }

    public function enterClave()
    {
        if (count($this->resultadosClave) > 0) {
            $this->seleccionarProducto($this->resultadosClave[0]->id);
        } else {
            $prod = Producto::where('clave', trim($this->buscarClave))->first();
            if ($prod) $this->seleccionarProducto($prod->id);
        }
    }

    public function enterNombre()
    {
        if (count($this->resultadosNombre) > 0) {
            $this->seleccionarProducto($this->resultadosNombre[0]->id);
        } else {
            $termino = trim($this->buscarNombre);
            $prod = Producto::where('nombre', 'like', "%{$termino}%")
                ->first();
            if ($prod) $this->seleccionarProducto($prod->id);
        }
    }

    public function limpiarBuscadores()
    {
        $this->buscarClave = '';
        $this->buscarNombre = '';
        $this->resultadosClave = [];
        $this->resultadosNombre = [];
    }

    public function actualizarCantidad($index, $cantidad)
    {
        if ($cantidad > 0) {
            $this->items[$index]['cantidad'] = $cantidad;
            $this->items[$index]['importe'] = $cantidad * $this->items[$index]['precio'];
            $this->calcularTotales();
        }
    }

    public function eliminarItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->calcularTotales();
    }

    public function updatedDescuentoPorcentaje()
    {
        $this->calcularTotales();
    }

    public function calcularTotales()
    {
        $this->subtotal = 0;
        foreach ($this->items as $item) {
            $this->subtotal += $item['importe'];
        }
        
        // El precio ya incluye IVA, por lo que no se agrega extra.
        $this->iva = 0; 
        
        // El subtotal es prácticamente el Total Bruto en este caso
        $totalBruto = $this->subtotal;
        
        // Calculamos el monto del descuento basado en el porcentaje
        $pct = floatval($this->descuentoPorcentaje);
        if($pct < 0) $pct = 0;
        if($pct > 100) $pct = 100;
        
        $this->descuento = $totalBruto * ($pct / 100);
        $this->total = $totalBruto - $this->descuento;
        
        if($this->total < 0) $this->total = 0;
    }

    public function guardarVenta()
    {
        $this->validate([
            'items' => 'required|array|min:1'
        ]);

        try {
            DB::transaction(function () {
                // 1. Determinar Usuario (Cliente real o General)
                $clienteFinalId = $this->cliente_id;
                
                if (!$clienteFinalId) {
                    // Buscar o crear Cliente General
                    $generalEmail = 'general@ventas.com';
                    $clienteGeneral = User::withTrashed()->where('email', $generalEmail)->first();
                    
                    if (!$clienteGeneral) {
                        $clienteGeneral = User::create([
                            'name' => 'Cliente General',
                            'email' => $generalEmail,
                            'password' => bcrypt('password'), // Dummy password
                            // Agrega otros campos requeridos por tu DB si los hay
                        ]);
                        // Asignar rol Cliente si es necesario
                        $clienteGeneral->assignRole('Cliente');
                    }
                    $clienteFinalId = $clienteGeneral->id;
                }

                // 2. Crear Pedido (Venta)
                // IMPORTANTE: Ajustar campos según la tabla 'pedidos'
                // Estructura vista en migración: id, subtotal, impuesto, total, fechapedido, estado, user_id
                $pedido = Pedido::create([
                    'user_id' => $clienteFinalId,
                    'vendedor_id' => auth()->id(),
                    'subtotal' => $this->subtotal,
                    'impuesto' => $this->iva,
                    'descuento' => $this->descuento ?? 0,
                    'total' => $this->total,
                    'estado' => 'Entregado',
                    'fechapedido' => Carbon::now()
                ]);

                $this->ventaGuardadaId = $pedido->id;

                // 3. Crear Detalles y DESCONTAR STOCK
                foreach ($this->items as $item) {
                    Detalle::create([
                        'pedido_id' => $pedido->id,
                        'producto_id' => $item['id'],
                        'cantidad' => $item['cantidad'],
                        'precio' => $item['precio'],
                        'importe' => $item['importe']
                    ]);

                    // Descontar inventario
                    $producto = Producto::find($item['id']);
                    if ($producto) {
                        $producto->disponible = $producto->disponible - $item['cantidad'];
                        if($producto->disponible < 0) $producto->disponible = 0; // Evitar negativos
                        $producto->save();
                    }
                }
            });
        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('swal:modal', [
                'type' => 'error',
                'title' => 'Error al guardar',
                'text' => 'Ocurrió un error al procesar la venta: ' . $e->getMessage()
            ]);
            return;
        }

        // Lanzar evento para el modal
        $this->dispatchBrowserEvent('abrir-modal-exito', ['id' => $this->ventaGuardadaId]);
        
        // Reset para nueva venta
        $this->limpiarTodo();
    }

    public function limpiarTodo() 
    {
        $this->items = [];
        $this->subtotal = 0;
        $this->iva = 0;
        $this->descuento = 0;
        $this->descuentoPorcentaje = 0;
        $this->total = 0;
        $this->cliente_id = "";
        $this->buscarCliente = "";
    }

    public function render()
    {
        return view('livewire.admin.ventas.crear-venta')
            ->extends('adminlte::page')
            ->section('content');
    }
}
