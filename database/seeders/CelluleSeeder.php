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
                'nom' => 'Cellule Bethel',
                'zone_id' => 1,
                'responsable_id' => 1, // Itoua Caleb
            ],
            [
                'nom' => 'Cellule Sion',
                'zone_id' => 1,
                'responsable_id' => 1,
            ],

            // Cellules de Poto-Poto (zone_id: 2)
            [
                'nom' => 'Cellule Emmanuel',
                'zone_id' => 2,
                'responsable_id' => 1,
            ],
            [
                'nom' => 'Cellule Jéricho',
                'zone_id' => 2,
                'responsable_id' => 1,
            ],

            // Cellules de Talangaï (zone_id: 5)
            [
                'nom' => 'Cellule Béthel-Talangaï',
                'zone_id' => 5,
                'responsable_id' => 1,
            ],
            [
                'nom' => 'Cellule des Jeunes Elus',
                'zone_id' => 5,
                'responsable_id' => 1,
            ],

            // Cellule de Ouenzé (zone_id: 4)
            [
                'nom' => 'Cellule Shalom',
                'zone_id' => 4,
                'responsable_id' => 1,
            ],

            // Cellule de Makélékélé (zone_id: 6)
            [
                'nom' => 'Cellule des Victorieux',
                'zone_id' => 6,
                'responsable_id' => 1,
            ],
        ];

        DB::table('cellules')->insert($cellules);
    }
}