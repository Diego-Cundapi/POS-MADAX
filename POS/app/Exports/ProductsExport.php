<?php

namespace App\Exports;

use App\Models\Producto;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ProductsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Producto::with('categoria')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Clave',
            'Nombre',
            'Categoría',
            'Modelo',
            'Marca',
            'Precio',
            'Stock',
            'Ubicación',
            'Descripción',
            'Año'
        ];
    }

    public function map($producto): array
    {
        return [
            $producto->id,
            $producto->clave,
            $producto->nombre,
            $producto->categoria ? $producto->categoria->name : 'N/A',
            $producto->modelo,
            $producto->marca,
            $producto->precio,
            $producto->disponible,
            $producto->ubicacion,
            $producto->descripcion,
            $producto->anio,
        ];
    }
}
