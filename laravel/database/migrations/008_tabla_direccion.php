<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('direccion', function (Blueprint $table) {
            $table->string('id_direccion', 45)->primary();
            $table->string('direccion', 50);
            $table->string('pais', 45)->nullable();
            $table->string('ciudad', 45)->nullable();
            $table->string('codigo_postal', 45)->nullable();
            $table->unsignedBigInteger('cliente_usuario_id_usuario')->nullable();
            $table->unsignedBigInteger('ciudad_id_ciudad')->nullable();

            $table->foreign('ciudad_id_ciudad', 'FK_DIRECCION_CIUDAD')
                  ->references('id_ciudad')->on('ciudad')
                  ->onDelete('no action')->onUpdate('cascade');

            $table->foreign('cliente_usuario_id_usuario', 'FK_DIRECCION_CLIENTE')
                  ->references('usuario_id_usuario')->on('cliente')
                  ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('direccion');
    }
};