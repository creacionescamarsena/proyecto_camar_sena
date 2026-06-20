<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuario', function (Blueprint $table) {
            $table->id('id_usuario');
            $table->string('nombres', 25)->nullable();
            $table->string('apellidos', 20)->nullable();
             $table->string('correo', 30);
            $table->string('telefono', 20)->nullable();
            $table->string('contraseña', 60);
            $table->enum('rol', ['Admin', 'Empleado', 'Cliente']);
            $table->enum('estado', ['Activo', 'Inactivo'])->default('Activo');
            $table->timestamps();
            

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuario');
    }
};

