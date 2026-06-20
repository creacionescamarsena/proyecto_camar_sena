<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chaqueta', function (Blueprint $table) {
            $table->id('id_chaqueta');
            $table->string('modelo_chaqueta', 25);
            $table->decimal('precio', 25, 1);
            $table->unsignedBigInteger('categoria_id_categoria')->nullable();

            $table->foreign('categoria_id_categoria', 'FK_CHAQUETA_CATEGORIA')
                  ->references('id_categoria')->on('categoria')
                  ->onDelete('no action')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chaqueta');
    }
};