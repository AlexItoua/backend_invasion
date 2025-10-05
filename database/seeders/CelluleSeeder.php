<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CelluleSeeder extends Seeder
{
    public function run(): void
    {
        $cellules = [
            // Cellules de Bacongo (zone_id: 1)
            [
                'nom' => 'MSN OUENZE BRAZZAVILLE',
                'zone_id' => 1,
                'responsable_id' => 1, // Itoua Caleb
            ],

        ];

        DB::table('cellules')->insert($cellules);
    }
}