<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ciudad', function (Blueprint $table) {
            $table->id('id_ciudad');
            $table->string('ciudad', 45);
            $table->unsignedBigInteger('pais_id_pais')->nullable();

            $table->foreign('pais_id_pais', 'FK_CIUDAD_PAIS')
                  ->references('id_pais')->on('pais')
                  ->onDelete('no action')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ciudad');
    }
};