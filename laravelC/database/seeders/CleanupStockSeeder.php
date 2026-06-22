<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CleanupStockSeeder extends Seeder
{
    public function run(): void
    {
        // Borrar todos los registros de stock
        if (\Illuminate\Support\Facades\Schema::hasTable('stock')) {
            DB::table('stock')->delete();
        }

        // Borrar todas las relaciones de chaqueta_has_talla
        if (\Illuminate\Support\Facades\Schema::hasTable('chaqueta_has_talla')) {
            DB::table('chaqueta_has_talla')->delete();
        }
    }
}
