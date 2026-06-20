<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Chaqueta;
use App\Models\Talla;

class StockSeeder extends Seeder
{
    public function run(): void
    {
        $chaquetas = Chaqueta::all();
        $tallas = Talla::all();

        // Datos de stock de ejemplo para cada chaqueta y talla
        $stockData = [
            'XS' => 5,
            'S' => 12,
            'M' => 8,
            'L' => 15,
            'XL' => 10,
            'XXL' => 3,
        ];

        foreach ($chaquetas as $chaqueta) {
            foreach ($tallas as $talla) {
                // Asignar cantidad según la talla
                $cantidad = $stockData[$talla->talla] ?? 0;

                DB::table('stock')->updateOrInsert(
                    [
                        'chaqueta_id_chaqueta' => $chaqueta->id_chaqueta,
                        'talla_id_talla' => $talla->id_talla,
                    ],
                    [
                        'cantidad' => $cantidad,
                    ]
                );
            }
        }
    }
}
