<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SyncTallasFromStockSeeder extends Seeder
{
    public function run(): void
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('stock') || !\Illuminate\Support\Facades\Schema::hasTable('chaqueta') || !\Illuminate\Support\Facades\Schema::hasTable('talla')) {
            return;
        }

        $rows = DB::table('stock')
            ->where('cantidad', '>', 0)
            ->select('chaqueta_id_chaqueta', 'talla_id_talla')
            ->distinct()
            ->get();

        foreach ($rows as $row) {
            DB::table('chaqueta_has_talla')->updateOrInsert([
                'chaqueta_id_chaqueta' => $row->chaqueta_id_chaqueta,
                'talla_id_talla' => $row->talla_id_talla,
            ], []);
        }
    }
}
