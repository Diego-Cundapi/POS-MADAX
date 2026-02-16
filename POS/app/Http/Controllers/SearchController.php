<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Categories;
use App\Models\Pedido;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        // 1. Capturamos el término de búsqueda
        $query = $request->input('query');

        // Si la búsqueda está vacía, regresamos
        if (!$query) {
            return redirect()->back();
        }

        // 2. Búsqueda en PRODUCTOS
        $productos = Producto::where('nombre', 'LIKE', "%{$query}%")
            ->orWhere('marca', 'LIKE', "%{$query}%")
            ->orWhere('modelo', 'LIKE', "%{$query}%")
            ->orWhere('clave', 'LIKE', "%{$query}%")
            ->take(20)
            ->get();

        // 3. Búsqueda en CATEGORÍAS
        $categorias = Categories::where('name', 'LIKE', "%{$query}%")->get();

        // 4. Búsqueda en VENTAS (ID o Cliente)
        $pedidos = Pedido::where('id', 'LIKE', "%{$query}%")
            ->orWhereHas('user', function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%");
            })
            ->with('user')
            ->orderByDesc('created_at')
            ->take(10)
            ->get();

        // 5. Retornamos la vista
        return view('search.results', compact('productos', 'categorias', 'pedidos', 'query'));
    }
}
