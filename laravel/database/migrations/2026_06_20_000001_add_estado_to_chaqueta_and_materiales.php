<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('chaqueta') && ! Schema::hasColumn('chaqueta', 'estado')) {
            Schema::table('chaqueta', function (Blueprint $table) {
                $table->enum('estado', ['Activo', 'Inactivo'])->default('Activo')->after('precio');
            });
        }

        if (Schema::hasTable('materiales') && ! Schema::hasColumn('materiales', 'estado')) {
            Schema::table('materiales', function (Blueprint $table) {
                $table->enum('estado', ['Activo', 'Inactivo'])->default('Activo')->after('cantidad');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('chaqueta') && Schema::hasColumn('chaqueta', 'estado')) {
            Schema::table('chaqueta', function (Blueprint $table) {
                $table->dropColumn('estado');
            });
        }

        if (Schema::hasTable('materiales') && Schema::hasColumn('materiales', 'estado')) {
            Schema::table('materiales', function (Blueprint $table) {
                $table->dropColumn('estado');
            });
        }
    }
};
