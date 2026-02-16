<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cotizacion;
use App\Models\DetalleCotizacion;
use App\Models\Producto;
use App\Models\Pedido;
use App\Models\Detalle; // Asegúrate de que este modelo corresponda a 'detalle_pedidos' o similar
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CotizacionController extends Controller
{
    // Mostrar lista
    // En tu método index:
    public function index()
    {
        // USAR get(), NO paginate()
        // DataTables se encargará de dividir las páginas automáticamente en la vista
        $cotizaciones = Cotizacion::with('user')->orderBy('created_at', 'desc')->get();

        return view('cotizaciones.index', compact('cotizaciones'));
    }

    // Editar (Cargar al carrito)
    public function edit($id)
    {
        // Cargamos productos incluso si están eliminados (SoftDelete) para que no falle la vista
        $cotizacion = Cotizacion::with(['detalles.producto' => function ($query) {
            $query->withTrashed();
        }])->findOrFail($id);

        if ($cotizacion->estado == 'Aceptada') {
            return back()->with('error', 'No se puede editar una cotización que ya es venta.');
        }

        // 1. Limpiamos el carrito actual
        Cart::destroy();

        // 2. Rellenamos el carrito con los datos de la cotización
        foreach ($cotizacion->detalles as $detalle) {
            // Protección contra borrado físico
            if (!$detalle->producto) {
                continue;
            }

            $nombreProducto = $detalle->producto->nombre;
            if ($detalle->producto->trashed()) {
                $nombreProducto = "(ELIMINADO) " . $nombreProducto;
            }

            Cart::add([
                'id'      => $detalle->producto_id,
                'name'    => $nombreProducto,
                'qty'     => $detalle->cantidad,
                'price'   => $detalle->precio,
                'weight'  => 1,
                'options' => [
                    'imagen' => $detalle->producto->imagen
                ]
            ]);
        }

        return redirect()->route('carrito.index')->with('success', 'Cotización cargada al carrito. Nota: Los productos eliminados aparecerán marcados.');
    }

    // Guardar nueva cotización
    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 1. FILTRO DE SEGURIDAD
        if (!$user->can('gestionar_cotizaciones')) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        // 2. Validar carrito
        if (Cart::count() > 0) {

            // 3. Crear cabecera
            $cotizacion = Cotizacion::create([
                'subtotal'       => floatval(str_replace(',', '', Cart::subtotal())),
                'impuesto'       => floatval(str_replace(',', '', Cart::tax())),
                'total'          => floatval(str_replace(',', '', Cart::total())),
                'cliente_nombre' => auth()->user()->name,
                'cliente_email'  => auth()->user()->email,
                'estado'         => 'Pendiente',
                'user_id'        => auth()->id(),
            ]);

            // 4. Guardar detalles
            foreach (Cart::content() as $item) {
                DetalleCotizacion::create([
                    'cotizacion_id' => $cotizacion->id,
                    'producto_id'   => $item->id,
                    'cantidad'      => $item->qty,
                    'precio'        => $item->price,
                    'importe'       => $item->price * $item->qty,
                ]);
            }

            // 5. Limpiar y redirigir
            Cart::destroy();

            return redirect()->route('cotizaciones.index')
                ->with('success', 'Cotización #' . $cotizacion->id . ' creada correctamente.')
                ->with('created_id', $cotizacion->id);
        } else {
            return back()->with('error', 'El carrito está vacío, agrega productos antes de cotizar.');
        }
    }

    // Eliminar
    public function destroy($id)
    {
        $cotizacion = Cotizacion::findOrFail($id);

        if ($cotizacion->estado == 'Aceptada') {
            return back()->with('error', 'No puedes eliminar una cotización que ya se convirtió en venta.');
        }

        $cotizacion->delete();
        return back()->with('success', 'Cotización eliminada correctamente.');
    }

    // PDF
    public function pdf($id)
    {
        // Cargamos productos y usuarios incluso si están eliminados
        $cotizacion = Cotizacion::with([
            'detalles.producto' => function ($query) {
                $query->withTrashed();
            },
            'user' => function ($query) {
                $query->withTrashed();
            }
        ])->findOrFail($id);
        $pdf = Pdf::loadView('cotizaciones.pdf', compact('cotizacion'));
        return $pdf->download('Cotizacion-' . $cotizacion->id . '.pdf');
    }

    // --- CONVERTIR A VENTA (LÓGICA ACTUALIZADA) ---
    // --- CONVERTIR A VENTA (LÓGICA FINAL CON VENDEDOR) ---
    public function convertirVenta($id)
    {
        try {
            DB::transaction(function () use ($id) {
                // 1. Cargar la cotización y sus productos (incluso eliminados para validar)
                $cotizacion = Cotizacion::with(['detalles.producto' => function ($query) {
                    $query->withTrashed();
                }])->findOrFail($id);

                // 2. Validar que no haya sido procesada antes
                if ($cotizacion->estado == 'Aceptada') {
                    throw new \Exception('Esta cotización ya fue convertida en venta anteriormente.');
                }

                // 3. VALIDACIÓN DE STOCK ACUMULATIVA
                $productosSinStock = [];

                foreach ($cotizacion->detalles as $detalle) {
                    if (!$detalle->producto) {
                        throw new \Exception("El producto ID {$detalle->producto_id} ya no existe físicamente.");
                    }

                    // Validación SoftDelete
                    if ($detalle->producto->trashed()) {
                         throw new \Exception("El producto '{$detalle->producto->nombre}' ha sido eliminado del catálogo y no se puede vender.");
                    }

                    if ($detalle->producto->disponible < $detalle->cantidad) {
                        $productosSinStock[] = "<li><b>" . $detalle->producto->clave . "</b> " .
                            "(Req: " . $detalle->cantidad . ", Disp: " . $detalle->producto->disponible . ")</li>";
                    }
                }

                // Si hay errores de stock, lanzamos la excepción
                if (count($productosSinStock) > 0) {
                    $mensaje = "Los siguientes productos no tienen stock suficiente:<br><ul style='text-align: left; margin-top:10px'>" .
                        implode('', $productosSinStock) .
                        "</ul>";
                    throw new \Exception($mensaje);
                }

                // --- 4. PREPARAR USUARIOS ---

                // CLIENTE: Buscamos por email (dueño de la cotización)
                $cliente = \App\Models\User::where('email', $cotizacion->cliente_email)->first();
                $clienteId = $cliente ? $cliente->id : auth()->id(); // Fallback al admin si no existe

                // VENDEDOR: El usuario logueado que está haciendo la conversión
                $vendedorId = auth()->id();


                // 5. Crear el PEDIDO (Venta)
                $pedido = Pedido::create([
                    'subtotal'    => $cotizacion->subtotal,
                    'impuesto'    => $cotizacion->impuesto,
                    'total'       => $cotizacion->total,
                    'fechapedido' => Carbon::now(),
                    'estado'      => 'Nuevo',

                    'user_id'     => $clienteId,  // El cliente que compra
                    'vendedor_id' => $vendedorId, // El empleado que vende (NUEVO)
                ]);

                // 6. Mover detalles y descontar stock
                foreach ($cotizacion->detalles as $detalle) {
                    Detalle::create([
                        'pedido_id'   => $pedido->id,
                        'producto_id' => $detalle->producto_id,
                        'cantidad'    => $detalle->cantidad,
                        'precio'      => $detalle->precio,
                        'importe'     => $detalle->importe
                    ]);

                    $detalle->producto->decrement('disponible', $detalle->cantidad);
                }

                // 7. Actualizar estado de la cotización
                $cotizacion->update(['estado' => 'Aceptada']);
            });

            return redirect()->route('ventas.index')->with('success', 'Cotización convertida en venta exitosamente.');
        } catch (\Throwable $e) {
            // En caso de error, volvemos a la edición con la alerta
            return redirect()
                ->route('cotizaciones.edit', ['cotizacionId' => $id])
                ->with('error_stock', $e->getMessage());
        }
    }
}
