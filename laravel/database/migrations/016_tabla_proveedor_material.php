<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proveedor_material', function (Blueprint $table) {
            $table->id('cod_proveedor');
            $table->string('proveedor_material', 45);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proveedor_material');
    }
};