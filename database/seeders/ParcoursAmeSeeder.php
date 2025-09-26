<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ParcoursAmeSeeder extends Seeder
{
    public function run(): void
    {
        $parcoursAmes = [
            [
                'ame_id' => 1, // Remplacez par un ID d'âme existant
                'parcours_spirituel_id' => 1, // Remplacez par un ID de parcours existant
                'date_debut' => now()->subDays(10),
                'date_fin' => null,
                'statut' => 'en_cours',
                'notes' => 'Démarrage du parcours découverte de la foi',
            ],
            [
                'ame_id' => 2, // Remplacez par un ID d'âme existant
                'parcours_spirituel_id' => 2, // Remplacez par un ID de parcours existant
                'date_debut' => now()->subDays(5),
                'date_fin' => now()->subDays(1),
                'statut' => 'termine',
                'notes' => 'Parcours fondements chrétiens terminé avec succès',
            ],
            [
                'ame_id' => 3, // Remplacez par un ID d'âme existant
                'parcours_spirituel_id' => 3, // Remplacez par un ID de parcours existant
                'date_debut' => now()->subDays(15),
                'date_fin' => now()->subDays(3),
                'statut' => 'abandonne',
                'notes' => 'Abandon pour raisons personnelles',
            ],
        ];

        DB::table('parcours_ames')->insert($parcoursAmes);
    }
}
