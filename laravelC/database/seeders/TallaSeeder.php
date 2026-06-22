<?php

namespace Database\Seeders;

use App\Models\Talla;
use Illuminate\Database\Seeder;

class TallaSeeder extends Seeder
{
    public function run(): void
    {
        $tallas = [
            ['talla' => 'XS', 'orden' => 1],
            ['talla' => 'S', 'orden' => 2],
            ['talla' => 'M', 'orden' => 3],
            ['talla' => 'L', 'orden' => 4],
            ['talla' => 'XL', 'orden' => 5],
            ['talla' => 'XXL', 'orden' => 6],
        ];

        foreach ($tallas as $talla) {
            Talla::firstOrCreate(
                ['talla' => $talla['talla']],
                ['orden' => $talla['orden']]
            );
        }
    }
}
