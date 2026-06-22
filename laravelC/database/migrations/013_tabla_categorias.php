<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categoria', function (Blueprint $table) {
            $table->id('id_categoria');
            $table->string('tipo_categoria', 25);
            $table->tinyInteger('estado_categoria');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categoria');
    }
};