<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categories; // Asegúrate de que tu modelo se llame así (plural) o Category (singular)

class CategoriaController extends Controller
{
    /**
     * Mostrar el listado de categorías.
     */
    public function index()
    {
        // Ordenamos por ID descendente para ver las nuevas primero
        $categorias = Categories::orderBy('id', 'desc')->paginate(10);
        return view('categoria.index', compact('categorias'));
    }

    /**
     * Mostrar el formulario para crear una nueva categoría.
     */
    public function create()
    {
        return view('categoria.create');
    }

    /**
     * Guardar una nueva categoría en la base de datos.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:45|regex:/^[a-zA-Z\s]+$/',
        ]);

        Categories::create([
            'name' => $request->name,
            'color' => sprintf('#%06X', mt_rand(0, 0xFFFFFF)),
        ]);

        // Redirigimos al INDEX de categorías para ver la nueva creación
        return redirect()->route('categoria.index')->with('success', 'Categoría creada exitosamente.');
    }

    /**
     * Mostrar una categoría específica (Opcional, generalmente no se usa mucho en categorías simples).
     */
    public function show($id)
    {
        // Si no tienes una vista para ver "detalle de categoría", redirigimos al index
        return redirect()->route('categoria.index');
    }

    /**
     * Mostrar el formulario para editar una categoría.
     */
    public function edit($id)
    {
        // Buscamos la categoría, si no existe lanza error 404 automáticamente
        $categoria = Categories::findOrFail($id);

        // Retornamos la vista 'edit' (que debes crear, similar a 'create')
        return view('categoria.edit', compact('categoria'));
    }

    /**
     * Actualizar la categoría en la base de datos.
     */
    public function update(Request $request, $id)
    {
        // 1. Validación (Mismas reglas que al crear)
        $request->validate([
            'name' => 'required|string|max:45|regex:/^[a-zA-Z\s]+$/',
        ]);

        // 2. Buscar
        $categoria = Categories::findOrFail($id);

        // 3. Actualizar
        $categoria->update([
            'name' => $request->name,
            // No actualizamos el color para mantener el que ya tenía
        ]);

        // 4. Redirigir
        return redirect()->route('categoria.index')->with('success', 'Categoría actualizada correctamente.');
    }

    /**
     * Eliminar la categoría de la base de datos.
     */
    public function destroy($id)
    {
        $categoria = Categories::findOrFail($id);

        // OPCIONAL: Validar si la categoría tiene productos antes de borrar
        // if ($categoria->productos()->count() > 0) {
        //     return back()->with('error', 'No puedes eliminar una categoría que tiene productos asociados.');
        // }

        $categoria->delete();

        return redirect()->route('categoria.index')->with('success', 'Categoría eliminada correctamente.');
    }
}
