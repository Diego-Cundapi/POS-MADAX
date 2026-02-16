<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Pedido;

class ComprasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Obtener el ID del usuario autenticado
        $userId = Auth::id();

        // Obtener los pedidos del usuario actual
        $pedidos = Pedido::where('user_id', $userId)->get();

        // Retornar la vista con los pedidos del usuario
        return view('compras.vercompras', compact('pedidos'));
    }

    public function detalle($id)
    {
        // Obtener el pedido con sus detalles
        $pedido = Pedido::with('detalles')->findOrFail($id);

        // Verificar que el pedido pertenece al usuario autenticado
        if ($pedido->user_id !== Auth::id()) {
            abort(403, 'No tienes permiso para ver este pedido.');
        }

        // Retornar la vista con los detalles del pedido
        return view('compras.detallecompra', compact('pedido'));
    }

    
}
