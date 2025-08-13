<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CelluleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cellules = [
            // Cellules de Bacongo (zone_id: 1)
            [
                'nom' => 'Cellule Bethel',
                'zone_id' => 1,
                'responsable_id' => 2, // Pasteur Nkounkou
            ],
            [
                'nom' => 'Cellule Sion',
                'zone_id' => 1,
                'responsable_id' => 4, // Soeur Loubaki
            ],

            // Cellules de Poto-Poto (zone_id: 2)
            [
                'nom' => 'Cellule Emmanuel',
                'zone_id' => 2,
                'responsable_id' => 3, // Frère Mbemba
            ],
            [
                'nom' => 'Cellule Jéricho',
                'zone_id' => 2,
                'responsable_id' => 5, // David Matsiona
            ],

            // Cellules de Talangaï (zone_id: 5)
            [
                'nom' => 'Cellule Béthel-Talangaï',
                'zone_id' => 5,
                'responsable_id' => 7, // Pasteur Itoua
            ],
            [
                'nom' => 'Cellule des Jeunes Elus',
                'zone_id' => 5,
                'responsable_id' => 6, // Sarah Bouanga
            ],

            // Cellule de Ouenzé (zone_id: 4)
            [
                'nom' => 'Cellule Shalom',
                'zone_id' => 4,
                'responsable_id' => null, // À assigner
            ],

            // Cellule de Makélékélé (zone_id: 6)
            [
                'nom' => 'Cellule des Victorieux',
                'zone_id' => 6,
                'responsable_id' => null, // À assigner
            ],
        ];

        DB::table('cellules')->insert($cellules);
    }
}
