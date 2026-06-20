<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('usuario') && ! Schema::hasColumn('usuario', 'numero_identificacion')) {
            Schema::table('usuario', function (Blueprint $table) {
                $table->string('numero_identificacion', 50)->nullable()->unique()->after('apellidos');
            });
        }

        if (Schema::hasTable('chaqueta') && ! Schema::hasColumn('chaqueta', 'codigo_referencia')) {
            Schema::table('chaqueta', function (Blueprint $table) {
                $table->string('codigo_referencia', 50)->nullable()->unique()->after('modelo_chaqueta');
            });
        }

        if (Schema::hasTable('materiales') && ! Schema::hasColumn('materiales', 'codigo_referencia')) {
            Schema::table('materiales', function (Blueprint $table) {
                $table->string('codigo_referencia', 50)->nullable()->unique()->after('material');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('usuario') && Schema::hasColumn('usuario', 'numero_identificacion')) {
            Schema::table('usuario', function (Blueprint $table) {
                $table->dropUnique(['numero_identificacion']);
                $table->dropColumn('numero_identificacion');
            });
        }

        if (Schema::hasTable('chaqueta') && Schema::hasColumn('chaqueta', 'codigo_referencia')) {
            Schema::table('chaqueta', function (Blueprint $table) {
                $table->dropUnique(['codigo_referencia']);
                $table->dropColumn('codigo_referencia');
            });
        }

        if (Schema::hasTable('materiales') && Schema::hasColumn('materiales', 'codigo_referencia')) {
            Schema::table('materiales', function (Blueprint $table) {
                $table->dropUnique(['codigo_referencia']);
                $table->dropColumn('codigo_referencia');
            });
        }
    }
};