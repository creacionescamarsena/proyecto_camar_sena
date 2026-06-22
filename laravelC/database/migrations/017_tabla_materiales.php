<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materiales', function (Blueprint $table) {
            $table->id('id_materiales');
            $table->string('material', 25);
            $table->unsignedBigInteger('proveedor_material_cod_proveedor')->nullable();

            $table->foreign('proveedor_material_cod_proveedor', 'FK_MATERIAL_PROVEEDOR')
                  ->references('cod_proveedor')->on('proveedor_material')
                  ->onDelete('no action')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materiales');
    }
};