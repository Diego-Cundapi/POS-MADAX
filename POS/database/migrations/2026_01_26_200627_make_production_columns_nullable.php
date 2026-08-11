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
        // SQLite reconstruye la tabla completa para aplicar ->change() (no soporta
        // ALTER COLUMN nativo). Sin desactivar las FK, esa reconstrucción falla
        // (DROP TABLE productos) en cuanto detalles/cotizaciones ya tienen filas
        // que referencian productos.
        Schema::disableForeignKeyConstraints();

        Schema::table('productos', function (Blueprint $table) {
            // Hacemos nulos los campos que no sean clave, marca o nombre
            $table->unsignedBigInteger('categories_id')->nullable()->change();
            $table->string('modelo', 45)->nullable()->change();
            // anio ya lo hicimos nullable en la migración anterior
            $table->decimal('precio', 8, 2)->default(0)->change(); // Precio puede ser 0 por defecto
            $table->integer('disponible')->default(0)->change();   // Stock puede ser 0 por defecto
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            // Revertir es complejo sin saber el estado exacto anterior, 
            // pero podemos intentar volver a ponerlos como NO nulos si fuera necesario.
            // Por seguridad en desarrollo, lo dejamos así 
            // o revertimos solo el cambio de nulabilidad estricta si se requiere.
            $table->string('modelo', 45)->nullable(false)->change();
        });
    }
};
