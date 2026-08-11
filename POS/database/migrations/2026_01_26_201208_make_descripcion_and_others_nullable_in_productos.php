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
        // SQLite reconstruye la tabla completa para aplicar ->change(); desactivamos
        // las FK para que no falle si detalles/cotizaciones ya referencian productos.
        Schema::disableForeignKeyConstraints();

        Schema::table('productos', function (Blueprint $table) {
            $table->string('descripcion', 255)->nullable()->change();
            $table->string('imagen')->nullable()->change();

            // Verificamos si existe la columna ubicación antes de modificarla (se agregó en una migración posterior)
            if (Schema::hasColumn('productos', 'ubicacion')) {
                $table->string('ubicacion', 100)->nullable()->change();
            }
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            // No revertimos para evitar bloqueos, pero idealmente sería volver a nullable(false)
        });
    }
};
