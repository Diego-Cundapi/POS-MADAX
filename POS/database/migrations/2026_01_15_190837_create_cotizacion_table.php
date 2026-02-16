<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cotizaciones', function (Blueprint $table) {
            $table->id();
            $table->decimal('subtotal',10,2);
            $table->decimal('impuesto',10,2);
            $table->decimal('total',10,2);
            $table->string('cliente_nombre',100);
            $table->string('cliente_email',100) ->nullable();
            $table->string('estado', ['pendiente', 'aprobada', 'rechazada']) ->default('pendiente');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cotizaciones');
    }
};
