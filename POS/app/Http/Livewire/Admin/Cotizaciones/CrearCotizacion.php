<?php

namespace App\Http\Livewire\Admin\Cotizaciones;

use Livewire\Component;
use App\Models\Producto;
use App\Models\User;
use App\Models\Cotizacion;
use App\Models\DetalleCotizacion;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CrearCotizacion extends Component
{
    // --- VARIABLES DE EDICIÓN ---
    public $modoEdicion = false;
    public $cotizacionEdicionId = null;

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
    public $cotizacionGuardadaId = null;
    public $fecha;

    // --- TOTALES ---
    public $subtotal = 0;
    public $iva = 0;
    public $total = 0;

    // --- MOUNT (AQUÍ ESTABA EL ERROR) ---
    public function mount($cotizacionId = null)
    {
        $this->fecha = Carbon::now()->format('d/m/Y');

        // 1. TRUCO DE SEGURIDAD:
        // Si Livewire no pasó el argumento, lo forzamos leyendo la ruta URL.
        if (!$cotizacionId) {
            $cotizacionId = request()->route('cotizacionId');
        }

        // 2. Si detectamos un ID, activamos el modo edición
        if ($cotizacionId) {
            $cotizacion = Cotizacion::find($cotizacionId);

            if ($cotizacion) {
                $this->cotizacionEdicionId = $cotizacion->id;
                $this->modoEdicion = true; // ¡IMPORTANTE!

                // Cargar cliente para el buscador
                $clienteUser = User::where('email', $cotizacion->cliente_email)->first();
                if ($clienteUser) {
                    $this->cliente_id = $clienteUser->id;
                    $this->buscarCliente = $clienteUser->name;
                } else {
                    $this->buscarCliente = $cotizacion->cliente_nombre;
                }

                // Cargar items
                $detalles = DetalleCotizacion::where('cotizacion_id', $cotizacion->id)->get();

                foreach ($detalles as $detalle) {
                    $prod = Producto::find($detalle->producto_id);
                    $this->items[] = [
                        'id' => $detalle->producto_id,
                        'clave' => $prod ? $prod->clave : 'N/A',
                        'descripcion' => $prod ? $prod->nombre : 'Producto Eliminado',
                        'cantidad' => $detalle->cantidad,
                        'precio' => floatval($detalle->precio),
                        'importe' => floatval($detalle->importe),
                        'ubicacion' => $prod ? $prod->ubicacion : 'N/A' // Agregamos ubicación
                    ];
                }
                $this->calcularTotales();
            }
        }
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
        $this->resultadosClave = []; // Limpiamos resultados de clave

        if (strlen($this->buscarNombre) > 0) {
            // CORRECCIÓN: Ahora solo buscamos en la columna 'nombre'
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
        // ELIMINAMOS LA CONDICIÓN.
        // Al dar clic en la X, queremos limpiar todo incondicionalmente.
        $this->cliente_id = "";
        $this->buscarCliente = "";
        $this->resultadosClientes = [];
    }

    // --- SELECCIONAR PRODUCTO ---
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
                    'precio' => floatval($producto->precio),
                    'importe' => floatval($producto->precio),
                    'ubicacion' => $producto->ubicacion ?? 'N/A' // Agregamos ubicación
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

    // --- CÁLCULOS ---
    public function actualizarCantidad($index, $cantidad)
    {
        if ($cantidad > 0) {
            $this->items[$index]['cantidad'] = $cantidad;
            $this->items[$index]['importe'] = $cantidad * $this->items[$index]['precio'];
            $this->calcularTotales();
        }
    }

    public function actualizarPrecio($index, $precio)
    {
        if ($precio >= 0) {
            $this->items[$index]['precio'] = $precio;
            $this->items[$index]['importe'] = $this->items[$index]['cantidad'] * $precio;
            $this->calcularTotales();
        }
    }

    public function eliminarItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->calcularTotales();
    }

    public function calcularTotales()
    {
        $this->subtotal = 0;
        foreach ($this->items as $item) {
            $this->subtotal += $item['importe'];
        }
        // El precio ya incluye IVA
        $this->iva = 0;
        $this->total = $this->subtotal;
    }

    // --- GUARDAR ---
    public function guardarCotizacion()
    {
        $this->validate([
            'cliente_id' => 'required',
            'items' => 'required|array|min:1'
        ]);

        DB::transaction(function () {
            $cliente = User::find($this->cliente_id);

            // Verificamos el modo edición AQUÍ
            if ($this->modoEdicion && $this->cotizacionEdicionId) {
                // ACTUALIZAR
                $cotizacion = Cotizacion::find($this->cotizacionEdicionId);
                $cotizacion->update([
                    'cliente_nombre' => $cliente->name,
                    'cliente_email' => $cliente->email,
                    'subtotal' => $this->subtotal,
                    'impuesto' => $this->iva,
                    'total' => $this->total,
                ]);
                // Borramos detalles viejos
                DetalleCotizacion::where('cotizacion_id', $cotizacion->id)->delete();
                $this->cotizacionGuardadaId = $cotizacion->id;
            } else {
                // CREAR NUEVA
                $cotizacion = Cotizacion::create([
                    'user_id' => auth()->id(),
                    'cliente_nombre' => $cliente->name,
                    'cliente_email' => $cliente->email,
                    'subtotal' => $this->subtotal,
                    'impuesto' => $this->iva,
                    'total' => $this->total,
                    'estado' => 'Pendiente'
                ]);
                $this->cotizacionGuardadaId = $cotizacion->id;
            }

            // Insertar nuevos detalles
            foreach ($this->items as $item) {
                DetalleCotizacion::create([
                    'cotizacion_id' => $this->cotizacionGuardadaId,
                    'producto_id' => $item['id'],
                    'cantidad' => $item['cantidad'],
                    'precio' => $item['precio'],
                    'importe' => $item['importe']
                ]);
            }
        });

        // Lanzar evento para el modal
        // Lanzar evento para el modal
        $this->dispatchBrowserEvent('abrir-modal-exito', ['id' => $this->cotizacionGuardadaId]);

        // --- AGREGA ESTE BLOQUE NUEVO ---
        // Si acabamos de guardar una NUEVA, no limpiamos.
        // Al contrario, activamos el modo edición con el ID que acabamos de generar.
        if (!$this->modoEdicion) {
            $this->modoEdicion = true;
            $this->cotizacionEdicionId = $this->cotizacionGuardadaId;

            // Opcional: Esto cambia la URL del navegador sin recargar la página
            // para que si el usuario da F5, no pierda la cotización.
            $this->dispatchBrowserEvent('history-push-state', [
                'url' => route('cotizaciones.edit', $this->cotizacionGuardadaId)
            ]);
        }
    }

    public function render()
    {
        return view('livewire.admin.cotizaciones.crear-cotizacion', [
            'clientes' => \App\Models\User::all(),
        ])
            ->extends('adminlte::page')
            ->section('content');
    }
}
