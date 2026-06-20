<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facturacion_has_chaqueta', function (Blueprint $table) {
            $table->unsignedBigInteger('facturacion_id_facturacion');
            $table->unsignedBigInteger('chaqueta_id_chaqueta');
            $table->integer('cantidad_venta');
            $table->string('valor_venta', 45);
            
            $table->primary(['facturacion_id_facturacion', 'chaqueta_id_chaqueta']);

            $table->foreign('facturacion_id_facturacion', 'FK_FC_FACTURACION')
                  ->references('id_facturacion')->on('facturacion')
                  ->onDelete('cascade')->onUpdate('cascade');

            $table->foreign('chaqueta_id_chaqueta', 'FK_FC_CHAQUETA')
                  ->references('id_chaqueta')->on('chaqueta')
                  ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facturacion_has_chaqueta');
    }
};