<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cotizacion extends Model
{
    use HasFactory;
    protected $table = 'cotizaciones';
    protected $fillable = ['subtotal','impuesto','total','cliente_nombre', 'cliente_email','estado','user_id'];

    public function detalles(){
        return $this->hasMany(DetalleCotizacion::class, 'cotizacion_id');
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
