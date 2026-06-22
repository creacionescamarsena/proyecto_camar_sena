<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('talla', function (Blueprint $table) {
            $table->id('id_talla');
            $table->string('talla', 10)->unique();
            $table->integer('orden')->default(0);
        });

        Schema::create('chaqueta_has_talla', function (Blueprint $table) {
            $table->unsignedBigInteger('chaqueta_id_chaqueta');
            $table->unsignedBigInteger('talla_id_talla');

            $table->foreign('chaqueta_id_chaqueta', 'FK_CHAQUETA_TALLA_CHAQUETA')
                  ->references('id_chaqueta')->on('chaqueta')
                  ->onDelete('cascade')->onUpdate('cascade');

            $table->foreign('talla_id_talla', 'FK_CHAQUETA_TALLA_TALLA')
                  ->references('id_talla')->on('talla')
                  ->onDelete('cascade')->onUpdate('cascade');

            $table->primary(['chaqueta_id_chaqueta', 'talla_id_talla']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chaqueta_has_talla');
        Schema::dropIfExists('talla');
    }
};
