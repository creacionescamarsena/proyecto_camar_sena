<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipo_documento', function (Blueprint $table) {
            $table->tinyIncrements('id_tipo');
            $table->string('tipo', 50);
        });

        DB::table('tipo_documento')->insert([
            ['tipo' => 'Cedula de ciudadania'],
            ['tipo' => 'Cedula de extranjeria'],
            ['tipo' => 'Pasaporte'],
            ['tipo' => 'Documento nacional'],
            ['tipo' => 'Otro'],
        ]);

        if (Schema::hasTable('usuario')) {
            Schema::table('usuario', function (Blueprint $table) {
                $table->unsignedTinyInteger('tipo_documento_id')->nullable()->after('apellidos');
            });

            Schema::table('usuario', function (Blueprint $table) {
                $table->foreign('tipo_documento_id', 'FK_USUARIO_TIPO_DOCUMENTO')
                    ->references('id_tipo')
                    ->on('tipo_documento')
                    ->onDelete('restrict')
                    ->onUpdate('cascade');
            });
        }

        $this->dropUsuarioForeignKeys();
        $this->alterUsuarioIdColumnsToString();
        $this->recreateUsuarioForeignKeys();
    }

    public function down(): void
    {
        $this->dropUsuarioForeignKeys();
        $this->alterUsuarioIdColumnsToBigInt();
        $this->recreateUsuarioForeignKeys();

        if (Schema::hasTable('usuario')) {
            Schema::table('usuario', function (Blueprint $table) {
                $table->dropForeign('FK_USUARIO_TIPO_DOCUMENTO');
                $table->dropColumn('tipo_documento_id');
            });
        }

        Schema::dropIfExists('tipo_documento');
    }

    protected function dropUsuarioForeignKeys(): void
    {
        $this->safeDropForeign('usuario_has_roles', 'FK_USUARIO_ROL_USUARIO');
        $this->safeDropForeign('cliente', 'FK_CLIENTE_USUARIO');
        $this->safeDropForeign('empleado', 'FK_EMPLEADO_USUARIO');
        $this->safeDropForeign('direccion', 'FK_DIRECCION_CLIENTE');
        $this->safeDropForeign('facturacion', 'FK_FACTURACION_CLIENTE');
        $this->safeDropForeign('facturacion', 'FK_FACTURACION_EMPLEADO');
    }

    protected function recreateUsuarioForeignKeys(): void
    {
        if (Schema::hasTable('usuario_has_roles')) {
            Schema::table('usuario_has_roles', function (Blueprint $table) {
                $table->foreign('usuario_id_usuario', 'FK_USUARIO_ROL_USUARIO')
                    ->references('id_usuario')
                    ->on('usuario')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
            });
        }

        if (Schema::hasTable('cliente')) {
            Schema::table('cliente', function (Blueprint $table) {
                $table->foreign('usuario_id_usuario', 'FK_CLIENTE_USUARIO')
                    ->references('id_usuario')
                    ->on('usuario')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
            });
        }

        if (Schema::hasTable('empleado')) {
            Schema::table('empleado', function (Blueprint $table) {
                $table->foreign('usuario_id_usuario', 'FK_EMPLEADO_USUARIO')
                    ->references('id_usuario')
                    ->on('usuario')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
            });
        }

        if (Schema::hasTable('direccion')) {
            Schema::table('direccion', function (Blueprint $table) {
                $table->foreign('cliente_usuario_id_usuario', 'FK_DIRECCION_CLIENTE')
                    ->references('usuario_id_usuario')
                    ->on('cliente')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
            });
        }

        if (Schema::hasTable('facturacion')) {
            Schema::table('facturacion', function (Blueprint $table) {
                $table->foreign('cliente_usuario_id_usuario', 'FK_FACTURACION_CLIENTE')
                    ->references('usuario_id_usuario')
                    ->on('cliente')
                    ->onDelete('no action')
                    ->onUpdate('cascade');

                $table->foreign('empleado_usuario_id_usuario', 'FK_FACTURACION_EMPLEADO')
                    ->references('usuario_id_usuario')
                    ->on('empleado')
                    ->onDelete('no action')
                    ->onUpdate('cascade');
            });
        }
    }

    protected function alterUsuarioIdColumnsToString(): void
    {
        $this->safeStatement('ALTER TABLE `usuario` MODIFY `id_usuario` VARCHAR(20) NOT NULL');
        $this->safeStatement('ALTER TABLE `usuario` DROP PRIMARY KEY, ADD PRIMARY KEY (`id_usuario`)');

        $this->safeStatement('ALTER TABLE `usuario_has_roles` MODIFY `usuario_id_usuario` VARCHAR(20) NOT NULL');
        $this->safeStatement('ALTER TABLE `cliente` MODIFY `usuario_id_usuario` VARCHAR(20) NOT NULL');
        $this->safeStatement('ALTER TABLE `empleado` MODIFY `usuario_id_usuario` VARCHAR(20) NOT NULL');
        $this->safeStatement('ALTER TABLE `direccion` MODIFY `cliente_usuario_id_usuario` VARCHAR(20) NULL');
        $this->safeStatement('ALTER TABLE `facturacion` MODIFY `cliente_usuario_id_usuario` VARCHAR(20) NULL');
        $this->safeStatement('ALTER TABLE `facturacion` MODIFY `empleado_usuario_id_usuario` VARCHAR(20) NULL');
    }

    protected function alterUsuarioIdColumnsToBigInt(): void
    {
        $this->safeStatement('ALTER TABLE `usuario` MODIFY `id_usuario` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
        $this->safeStatement('ALTER TABLE `usuario` DROP PRIMARY KEY, ADD PRIMARY KEY (`id_usuario`)');

        $this->safeStatement('ALTER TABLE `usuario_has_roles` MODIFY `usuario_id_usuario` BIGINT UNSIGNED NOT NULL');
        $this->safeStatement('ALTER TABLE `cliente` MODIFY `usuario_id_usuario` BIGINT UNSIGNED NOT NULL');
        $this->safeStatement('ALTER TABLE `empleado` MODIFY `usuario_id_usuario` BIGINT UNSIGNED NOT NULL');
        $this->safeStatement('ALTER TABLE `direccion` MODIFY `cliente_usuario_id_usuario` BIGINT UNSIGNED NULL');
        $this->safeStatement('ALTER TABLE `facturacion` MODIFY `cliente_usuario_id_usuario` BIGINT UNSIGNED NULL');
        $this->safeStatement('ALTER TABLE `facturacion` MODIFY `empleado_usuario_id_usuario` BIGINT UNSIGNED NULL');
    }

    protected function safeDropForeign(string $table, string $constraint): void
    {
        if (! Schema::hasTable($table)) {
            return;
        }

        try {
            Schema::table($table, function (Blueprint $table) use ($constraint) {
                $table->dropForeign($constraint);
            });
        } catch (\Throwable $e) {
            // ignore missing constraints
        }
    }

    protected function safeStatement(string $statement): void
    {
        try {
            DB::statement($statement);
        } catch (\Throwable $e) {
            // ignore failures for schema variants
        }
    }
};
