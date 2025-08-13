<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CampagneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
   public function run(): void
{
    $currentYear = date('Y'); // Utilise l'année actuelle
    $nextYear = $currentYear + 1;

    $campagnes = [
        [
            'nom' => 'Opération Salongo ' . $currentYear,
            'date_debut' => Carbon::create($currentYear, 1, 15),
            'date_fin' => Carbon::create($currentYear, 3, 20),
            'zone_id' => 1,
            'description' => 'Campagne de salubrité dans le quartier Bacongo',
        ],
        [
            'nom' => 'Evangélisation Poto-Poto',
            'date_debut' => Carbon::create($currentYear, 5, 10),
            'date_fin' => Carbon::create($currentYear, 6, 5),
            'zone_id' => 2,
            'description' => 'Mission évangélique dans les marchés de Poto-Poto',
        ],

        [
            'nom' => 'Campagne de vaccination Makélékélé',
            'date_debut' => Carbon::create($nextYear, 7, 10), // Exemple pour l'année suivante
            'date_fin' => Carbon::create($nextYear, 7, 25),
            'zone_id' => 6,
            'description' => 'Vaccination des enfants contre la polio',
        ],
    ];

    DB::table('campagnes')->insert($campagnes);
}
}
