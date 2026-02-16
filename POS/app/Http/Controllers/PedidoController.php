<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PedidoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // OPTIMIZACIÓN: Cargamos la relación 'user' y 'vendedor' INCLUYENDO ELIMINADOS
        // y ordenamos por ID para asegurar que las últimas ventas salgan primero (más fiable que updated_at)
        $pedidos = Pedido::with([
            'user' => function($q) { $q->withTrashed(); },
            'vendedor' => function($q) { $q->withTrashed(); }
        ])->orderByDesc("id")->get();

        return view('ventas.index', compact('pedidos'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // CORRECCIÓN CLAVE: Usamos 'with' para traer los datos del Cliente (user)
        // y los detalles de la compra. Así $pedido->user tendrá los datos correctos.
        $pedido = Pedido::with(['user', 'detalles.producto', 'vendedor'])->find($id);

        // 2. VALIDACIÓN: ¿El pedido está vacío (null)?
        if (!$pedido) {
            // Si no existe, NO cargamos la vista de edición.
            return redirect()->route('ventas.index')->with('error_modal', 'No se encontró el pedido solicitado.');
        }

        // 3. Si existe, continuamos normal enviando la variable $pedido completa
        return view('ventas.edit', compact('pedido'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $pedido = Pedido::findOrFail($id);

        // Actualizamos los datos (ej. Estado)
        $pedido->fill($request->all());
        $pedido->save();

        // CORRECCIÓN: Agregamos el mensaje 'success' para que salga la alerta verde al redirigir
        return redirect()->route('ventas.index')->with('success', 'Estado del pedido actualizado correctamente.');
    }

    public function descargarPDF($id)
    {
        // 1. Buscamos el pedido con sus detalles y cliente relacionado
        // Usamos la clase importada arriba en lugar de la ruta completa
        $pedido = Pedido::with(['detalles.producto', 'user'])->findOrFail($id);

        // 2. Cargamos la vista del PDF
        $pdf = Pdf::loadView('ventas.pdf', compact('pedido'));

        // 3. Descargamos el archivo
        return $pdf->download('venta-' . $pedido->id . '.pdf');
    }
}
