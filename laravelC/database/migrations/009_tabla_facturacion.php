<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facturacion', function (Blueprint $table) {
            $table->id('id_facturacion');
            $table->dateTime('fecha');
            $table->decimal('total', 15, 2);
            $table->string('impuestos', 20);
            $table->unsignedBigInteger('cliente_usuario_id_usuario')->nullable();
            $table->unsignedBigInteger('empleado_usuario_id_usuario')->nullable();

            $table->foreign('cliente_usuario_id_usuario', 'FK_FACTURACION_CLIENTE')
                  ->references('usuario_id_usuario')->on('cliente')
                  ->onDelete('no action')->onUpdate('cascade');

            $table->foreign('empleado_usuario_id_usuario', 'FK_FACTURACION_EMPLEADO')
                  ->references('usuario_id_usuario')->on('empleado')
                  ->onDelete('no action')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facturacion');
    }
};