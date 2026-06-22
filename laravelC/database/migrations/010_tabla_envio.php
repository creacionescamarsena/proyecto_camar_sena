<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('envio', function (Blueprint $table) {
            $table->id('id_envio');
            $table->unsignedBigInteger('facturacion_id_facturacion')->nullable();
            $table->string('tipo_envio', 14);
            $table->string('empresa_transportadora', 45);
            $table->string('direccion_id_direccion', 45)->nullable();

            $table->foreign('facturacion_id_facturacion', 'FK_ENVIO_FACTURACION')
                  ->references('id_facturacion')->on('facturacion')
                  ->onDelete('cascade')->onUpdate('cascade');

            $table->foreign('direccion_id_direccion', 'FK_ENVIO_DIRECCION')
                  ->references('id_direccion')->on('direccion')
                  ->onDelete('no action')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('envio');
    }
};