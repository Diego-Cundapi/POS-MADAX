<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Models\Cotizacion;

class TicketController extends Controller
{
    /**
     * Imprimir ticket de Venta o Cotización
     * Ruta: /ticket/{tipo}/{id}
     */
    public function imprimir($tipo, $id)
    {
        $data = [];

        if ($tipo === 'venta') {
            $venta = Pedido::with(['detalles.producto', 'user', 'vendedor'])->findOrFail($id);
            
            $data = [
                'id' => $venta->id,
                'fecha' => $venta->created_at->format('d/m/Y h:i A'),
                'cliente' => $venta->user->name ?? 'Cliente Eliminado',
                'vendedor' => $venta->vendedor->name ?? 'Sistema',
                'tipo' => 'venta',
                'subtotal' => $venta->subtotal,
                'impuesto' => $venta->impuesto,
                'descuento' => $venta->descuento ?? 0,
                'total' => $venta->total,
                'detalles' => $venta->detalles->map(function($d) {
                    return [
                        'cantidad' => $d->cantidad,
                        'descripcion' => $d->producto->nombre ?? 'Producto Eliminado',
                        'precio' => $d->precio,
                        'importe' => $d->importe
                    ];
                })
            ];

        } elseif ($tipo === 'cotizacion') {
            // CORRECCIÓN LÓGICA:
            // $coti->user es el VENDEDOR (creador del registro).
            // $coti->cliente_nombre es el CLIENTE.
            
            $coti = Cotizacion::with(['detalles.producto', 'user'])->findOrFail($id);

            $data = [
                'id' => $coti->id,
                'fecha' => $coti->created_at->format('d/m/Y h:i A'),
                'cliente' => $coti->cliente_nombre ?? 'Público General', // CLIENTE REAL
                'vendedor' => $coti->user->name ?? 'Sistema',             // VENDEDOR (Creador)
                'tipo' => 'cotizacion',
                'subtotal' => 0, 
                'impuesto' => 0,
                'descuento' => $coti->descuento ?? 0, // Ajuste solicitado
                'total' => $coti->total,
                'detalles' => $coti->detalles->map(function($d) {
                    return [
                        'cantidad' => $d->cantidad,
                        'descripcion' => $d->producto->nombre ?? 'Producto Eliminado',
                        'precio' => $d->precio,
                        'importe' => $d->importe
                    ];
                })
            ];
            
            // Recalculamos subtotal para cotizaciones si no viene explícito
            // Asumimos que el total ya incluye IVA si la lógica de negocio es así.
            // Si $coti->subtotal existe en DB, úsalo preferentemente.
            $data['subtotal'] = $coti->subtotal ?? ($data['total'] / 1.16);
            $data['impuesto'] = $coti->impuesto ?? ($data['total'] - $data['subtotal']);
        } else {
            abort(404);
        }

        return view('tickets.print', $data);
    }
}
