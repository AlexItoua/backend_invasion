<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ZoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $zones = [
            [
                'nom' => 'Bacongo',
                'description' => 'Arrondissement historique au sud de Brazzaville',
            ],
            [
                'nom' => 'Poto-Poto',
                'description' => 'Quartier central et animé de Brazzaville',
            ],
            [
                'nom' => 'Moungali',
                'description' => 'Arrondissement résidentiel et commercial',
            ],
            [
                'nom' => 'Ouenzé',
                'description' => 'Zone en développement avec plusieurs activités économiques',
            ],
            [
                'nom' => 'Talangaï',
                'description' => 'Arrondissement populaire au nord de la ville',
            ],
            [
                'nom' => 'Makélékélé',
                'description' => 'Le plus grand arrondissement de Brazzaville',
            ],
            [
                'nom' => 'Djiri',
                'description' => 'Zone périurbaine en expansion',
            ],
            [
                'nom' => 'Mfilou',
                'description' => 'Arrondissement comprenant des zones résidentielles et rurales',
            ],
        ];

        DB::table('zones')->insert($zones);
    }
}
