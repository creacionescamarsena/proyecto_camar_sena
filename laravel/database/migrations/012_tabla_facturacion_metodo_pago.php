<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facturacion_has_metodo_pago', function (Blueprint $table) {
            $table->unsignedBigInteger('facturacion_id_facturacion');
            $table->unsignedBigInteger('metodo_pago_id_pago');
            $table->string('monto', 45);
            
            $table->primary(['facturacion_id_facturacion', 'metodo_pago_id_pago']);

            $table->foreign('facturacion_id_facturacion', 'FK_FP_FACTURACION')
                  ->references('id_facturacion')->on('facturacion')
                  ->onDelete('cascade')->onUpdate('cascade');

            $table->foreign('metodo_pago_id_pago', 'FK_FP_METODO')
                  ->references('id_pago')->on('metodo_pago')
                  ->onDelete('no action')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facturacion_has_metodo_pago');
    }
};