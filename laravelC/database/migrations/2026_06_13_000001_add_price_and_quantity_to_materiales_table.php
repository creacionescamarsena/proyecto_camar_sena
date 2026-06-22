<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('materiales', function (Blueprint $table) {
            if (! Schema::hasColumn('materiales', 'precio')) {
                $table->decimal('precio', 10, 2)->default(0);
            }

            if (! Schema::hasColumn('materiales', 'cantidad')) {
                $table->integer('cantidad')->default(0);
            }
        });
    }

    public function down(): void
    {
        Schema::table('materiales', function (Blueprint $table) {
            if (Schema::hasColumn('materiales', 'precio')) {
                $table->dropColumn('precio');
            }

            if (Schema::hasColumn('materiales', 'cantidad')) {
                $table->dropColumn('cantidad');
            }
        });
    }
};
