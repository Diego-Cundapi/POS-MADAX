<?php

namespace App\Imports;

use App\Models\Producto;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow; // Importante para leer los encabezados

class ProductosImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // VALIDACIÓN BÁSICA: Solo Nombre, Clave son obligatorios
        if (!isset($row['nombre']) || !isset($row['clave'])) {
            return null;
        }

        return new Producto([
            // OBLIGATORIOS
            'clave'         => $row['clave'], 
            'nombre'        => $row['nombre'],
            'marca'         => $row['marca'] ?? null, 
            
            // OPCIONALES CON VALOR POR DEFECTO
            'precio'        => $row['precio'] ?? 0, 
            'disponible'    => $row['stock'] ?? $row['disponible'] ?? 0, // Acepta 'Stock' o 'Disponible'
            
            // OPCIONALES
            'modelo'        => $row['modelo'] ?? null,
            'anio'          => $row['anio'] ?? null,
            'descripcion'   => $row['descripcion'] ?? null,
            'ubicacion'     => $row['ubicacion'] ?? 'Bodega',
            'imagen'        => null,
            'clave_proveedor' => $row['clave_proveedor'] ?? null,

            // RELACIONES
            // 'Categoria_id' puede convertirse en 'categoria-id' al importarse
            'categories_id' => $row['categoria_id'] ?? $row['categoria-id'] ?? 1,
        ]);
    }
}
