<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('usuario') && Schema::hasColumn('usuario', 'numero_identificacion')) {
            Schema::table('usuario', function (Blueprint $table) {
                // Drop the column; any indexes on it will be removed by the DB engine if present
                $table->dropColumn('numero_identificacion');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('usuario') && ! Schema::hasColumn('usuario', 'numero_identificacion')) {
            Schema::table('usuario', function (Blueprint $table) {
                $table->string('numero_identificacion', 50)->nullable()->unique()->after('email');
            });
        }
    }
};
