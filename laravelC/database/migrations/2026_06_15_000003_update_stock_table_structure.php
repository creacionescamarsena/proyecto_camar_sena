<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Eliminar la tabla antigua si existe
        if (Schema::hasTable('stock')) {
            Schema::drop('stock');
        }

        // Crear la tabla stock con la estructura actualizada
        Schema::create('stock', function (Blueprint $table) {
            $table->id('cod_stock');
            $table->unsignedBigInteger('chaqueta_id_chaqueta');
            $table->unsignedBigInteger('talla_id_talla');
            $table->unsignedInteger('cantidad')->default(0);

            $table->foreign('chaqueta_id_chaqueta')
                  ->references('id_chaqueta')->on('chaqueta')
                  ->onDelete('cascade')->onUpdate('cascade');

            $table->foreign('talla_id_talla')
                  ->references('id_talla')->on('talla')
                  ->onDelete('cascade')->onUpdate('cascade');

            $table->unique(['chaqueta_id_chaqueta', 'talla_id_talla']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock');
    }
};
