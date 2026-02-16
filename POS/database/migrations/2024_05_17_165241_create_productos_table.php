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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('categories_id')->nullable();
            $table->foreign('categories_id')
                  ->references('id')
                  ->on('categories')
                  ->onDelete('set null');
            $table->string('nombre',45);
            $table->string('modelo', 45)->nullable(); // Modificado de numeric a string
            $table->string('anio', 45)->nullable();   // Modificado de numeric a string
            $table->string('marca',20);
            $table->decimal('precio',8,2);
            $table->string('clave',20);
            $table->string('descripcion',255)->nullable(); // Ahora opcional
            $table->string('imagen')->nullable();          // Ahora opcional
            $table->integer('disponible');

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
        Schema::dropIfExists('productos');
    }
};
