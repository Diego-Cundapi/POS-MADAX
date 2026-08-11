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
            $table->string('marca',50)->nullable()->change();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->string('marca',50)->nullable(false)->change();
        });
    }
};
