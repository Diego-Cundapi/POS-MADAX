<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleCotizacion extends Model
{
    use HasFactory;
    protected $table = 'detalle_cotizaciones';
    protected $fillable = ['cotizacion_id', 'producto_id', 'cantidad', 'precio', 'importe'];

    public function producto(){
        return $this->belongsTo(Producto::class);
    }
}
