<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'vendedor_id',
        'subtotal',
        'impuesto',
        'descuento',
        'total',
        'fechapedido',
        'estado'
    ];

    public function detalles(){
        return $this->hasMany(Detalle::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    // 2. AGREGAR ESTA NUEVA RELACIÓN
    public function vendedor()
    {
        return $this->belongsTo(User::class, 'vendedor_id');
    }
}
