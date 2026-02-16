<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalles', function (Blueprint $table) {
            $table->id();
            $table->decimal('precio',8,2);
            $table->decimal('importe',8,2);
            $table->integer('cantidad');
            $table->unsignedBigInteger('pedido_id'); // Agregar la columna pedido_id
            $table->foreign('pedido_id')->references('id')->on('pedidos');
            $table->unsignedBigInteger('producto_id'); // Agregar la columna producto_id
            $table->foreign('producto_id')->references('id')->on('productos');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('detalles');
    }
};
