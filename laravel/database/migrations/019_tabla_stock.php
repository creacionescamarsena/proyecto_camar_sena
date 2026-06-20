<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock', function (Blueprint $table) {
            $table->id('cod_stock');
            $table->string('talla', 5);
            $table->unsignedBigInteger('chaqueta_id_chaqueta')->nullable();

            $table->foreign('chaqueta_id_chaqueta', 'FK_STOCK_CHAQUETA')
                  ->references('id_chaqueta')->on('chaqueta')
                  ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock');
    }
};