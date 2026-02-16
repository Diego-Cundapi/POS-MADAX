<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detalle extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = [
            'precio',
            'importe',
            'cantidad',
            'pedido_id',
            'producto_id',
        ];
    public function pedido(){
        return $this->belongsTo(Pedido::class);
    }

    public function producto(){
        return $this->belongsTo(Producto::class)->withTrashed();
    }
}
