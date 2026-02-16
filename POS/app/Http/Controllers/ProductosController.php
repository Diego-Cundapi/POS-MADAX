<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categories;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductosImport;
use App\Exports\ProductsExport;

class ProductosController extends Controller
{
    /**
     * Redirige al Dashboard (donde está tu tabla principal).
     */
    public function index()
    {
        return redirect()->route('dashboard');
    }

    /**
     * Muestra el formulario de creación.
     */
    public function create()
    {
        $categorias = Categories::all();
        return view('productos.create', compact('categorias'));
    }

    /**
     * Guarda el producto y su imagen en public/imagenes/productos.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nombre'        => 'required|string',
            'categories_id' => 'required|exists:categories,id',
            'modelo'        => 'required',
            'marca'         => 'nullable|string',
            'precio'        => 'required|numeric|min:0',
            'clave'         => 'required|string',
            'descripcion'   => 'nullable|string', // Opcional
            'imagen'        => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // Opcional
            'disponible'    => 'required|integer|min:0',
            'ubicacion'     => 'nullable|string|max:100',
            'anio'          => 'nullable|string|max:45',
            'clave_proveedor' => 'nullable|string|max:100',
        ]);

        $imagePath = null;

        // --- GUARDADO DE IMAGEN ---
        if ($request->hasFile('imagen')) {
            $image = $request->file('imagen');
            // Usamos time() para evitar nombres duplicados
            $imageName = time() . '_' . $image->getClientOriginalName();

            // Movemos la imagen a la carpeta public/imagenes/productos
            $image->move(public_path('imagenes/productos'), $imageName);

            // Guardamos la ruta relativa para la base de datos
            $imagePath = 'imagenes/productos/' . $imageName;
        }

        Producto::create([
            'nombre'        => $validatedData['nombre'],
            'categories_id' => $validatedData['categories_id'],
            'modelo'        => $validatedData['modelo'],
            'marca'         => $validatedData['marca'],
            'precio'        => $validatedData['precio'],
            'clave'         => $validatedData['clave'],
            'descripcion'   => $validatedData['descripcion'] ?? null,
            'imagen'        => $imagePath,
            'disponible'    => $validatedData['disponible'],
            'ubicacion'     => $validatedData['ubicacion'] ?? null,
            'anio'          => $validatedData['anio'] ?? null,
            'clave_proveedor' => $validatedData['clave_proveedor'] ?? null,
        ]);

        return redirect()->route('dashboard')->with('success', 'Producto creado exitosamente');
    }

    /**
     * Muestra el formulario de edición.
     */
    public function edit($id)
    {
        $producto = Producto::findOrFail($id);
        $categorias = Categories::orderBy('name')->get();
        return view('productos.edit', compact('producto', 'categorias'));
    }

    /**
     * Actualiza el producto y reemplaza la imagen si se sube una nueva.
     */
    public function update(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);

        $validatedData = $request->validate([
            'nombre'        => 'required|string',
            'categories_id' => 'required|exists:categories,id',
            'modelo'        => 'required',
            'marca'         => 'nullable|string',
            'precio'        => 'required|numeric|min:0',
            'clave'         => 'required|string',
            'descripcion'   => 'nullable|string',
            'imagen'        => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'disponible'    => 'required|integer|min:0',
            'ubicacion'     => 'nullable|string|max:100',
            'anio'          => 'nullable|string|max:45',
            'clave_proveedor' => 'nullable|string|max:100',
        ]);

        // --- ACTUALIZACIÓN DE IMAGEN ---
        if ($request->hasFile('imagen')) {
            // 1. Borrar la imagen ANTERIOR si existe en public/
            if ($producto->imagen && file_exists(public_path($producto->imagen))) {
                unlink(public_path($producto->imagen));
            }

            // 2. Subir la NUEVA imagen
            $image = $request->file('imagen');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('imagenes/productos'), $imageName);

            $validatedData['imagen'] = 'imagenes/productos/' . $imageName;
        } else {
            // Si no subió nada, quitamos 'imagen' del array para no borrar la ruta actual de la BD
            unset($validatedData['imagen']);
        }

        $producto->update($validatedData);

        return redirect()->route('dashboard')->with('success', 'Producto actualizado exitosamente');
    }

    /**
     * Elimina el producto y su imagen de la carpeta public.
     */
    public function destroy($id)
    {
        $producto = Producto::findOrFail($id);

        // --- BORRADO DE IMAGEN ---
        // Verificamos si tiene imagen Y si el archivo físico existe en public/imagenes/productos
        if ($producto->imagen && file_exists(public_path($producto->imagen))) {
            unlink(public_path($producto->imagen)); // unlink es la función nativa de PHP para borrar archivos
        }

        $producto->delete();

        return redirect()->route('dashboard')->with('success', 'Producto eliminado exitosamente');
    }

    /**
     * Importación de Excel.
     */
    public function importarExcel(Request $request)
    {
        $request->validate([
            'archivo_excel' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            Excel::import(new ProductosImport, $request->file('archivo_excel'));
            return redirect()->route('dashboard')->with('success', '¡Productos importados correctamente!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al importar: ' . $e->getMessage());
        }
    }

    /**
     * Exporta el inventario a Excel/CSV.
     * Nota: Usamos CSV porque el formato XLSX requiere la extensión XMLWriter
     * que no está disponible en el binario de PHP embebido de NativePHP.
     */
    public function exportarExcel()
    {
        // CSV funciona sin extensiones adicionales y se abre en Excel
        return Excel::download(new ProductsExport, 'inventario_productos.csv', \Maatwebsite\Excel\Excel::CSV);
    }
}
