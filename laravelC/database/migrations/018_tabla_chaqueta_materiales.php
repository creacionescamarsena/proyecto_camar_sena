<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chaqueta_has_materiales', function (Blueprint $table) {
        // foreignId crea la columna unsignedBigInteger y constrained define la relación
        $table->foreignId('chaqueta_id_chaqueta')
              ->constrained(table: 'chaqueta', column: 'id_chaqueta', indexName: 'fk_cm_chaqueta')
              ->onUpdate('cascade')
              ->onDelete('cascade');

        $table->foreignId('materiales_id_materiales')
              ->constrained(table: 'materiales', column: 'id_materiales', indexName: 'fk_cm_material')
              ->onUpdate('cascade')
              ->onDelete('cascade');

        // Declaramos la llave primaria compuesta al final
        $table->primary(['chaqueta_id_chaqueta', 'materiales_id_materiales']);
    });
    }

    public function down(): void
    {
        Schema::dropIfExists('chaqueta_has_materiales');
    }
};