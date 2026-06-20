<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cliente', function (Blueprint $table) {
            $table->unsignedBigInteger('usuario_id_usuario')->primary();

            $table->foreign('usuario_id_usuario', 'FK_CLIENTE_USUARIO')
                  ->references('id_usuario')->on('usuario')
                  ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cliente');
    }
};