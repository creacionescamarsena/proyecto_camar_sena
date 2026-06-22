<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('chaqueta') && ! Schema::hasColumn('chaqueta', 'unidades')) {
            Schema::table('chaqueta', function (Blueprint $table) {
                $table->unsignedInteger('unidades')->default(0)->after('precio');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('chaqueta') && Schema::hasColumn('chaqueta', 'unidades')) {
            Schema::table('chaqueta', function (Blueprint $table) {
                $table->dropColumn('unidades');
            });
        }
    }
};
