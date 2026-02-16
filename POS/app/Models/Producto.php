<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Producto extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    protected $fillable = [
        'nombre',
        'categories_id',
        'modelo',
        'anio', // NUEVO CAMPO
        'marca',
        'precio',
        'clave',
        'clave_proveedor', // NUEVO CAMPO
        'descripcion',
        'imagen',
        'disponible',
        'ubicacion',
    ];
    public function categoria(){
        return $this->belongsTo(Categories::class, 'categories_id');
    }
}
