<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuario_has_roles', function (Blueprint $table) {
            $table->unsignedBigInteger('usuario_id_usuario');
            $table->unsignedBigInteger('roles_id_rol');
            
            $table->primary(['usuario_id_usuario', 'roles_id_rol']);

            $table->foreign('usuario_id_usuario', 'FK_USUARIO_ROL_USUARIO')
                  ->references('id_usuario')->on('usuario')
                  ->onDelete('cascade')->onUpdate('cascade');

            $table->foreign('roles_id_rol', 'FK_USUARIO_ROL_ROL')
                  ->references('id_rol')->on('roles')
                  ->onDelete('no action')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuario_has_roles');
    }
};