<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chaqueta', function (Blueprint $table) {
            if (Schema::hasColumn('chaqueta', 'unidades')) {
                $table->dropColumn('unidades');
            }
        });
    }

    public function down(): void
    {
        Schema::table('chaqueta', function (Blueprint $table) {
            $table->unsignedInteger('unidades')->default(0)->after('precio');
        });
    }
};
