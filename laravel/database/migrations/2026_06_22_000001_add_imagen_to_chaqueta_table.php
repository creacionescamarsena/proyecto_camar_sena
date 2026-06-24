<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('chaqueta') && ! Schema::hasColumn('chaqueta', 'imagen')) {
            Schema::table('chaqueta', function (Blueprint $table) {
                $table->string('imagen')->nullable()->after('precio');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('chaqueta') && Schema::hasColumn('chaqueta', 'imagen')) {
            Schema::table('chaqueta', function (Blueprint $table) {
                $table->dropColumn('imagen');
            });
        }
    }
};
