<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EtapeValideeSeeder extends Seeder
{
    public function run(): void
    {
        $etapesValidees = [
            // Étape validée pour le parcoursAme 1
            [
                'parcours_ame_id' => 1,
                'etape_parcours_id' => 1, // ID d'étape du parcours 1
                'date_validation' => now()->subDays(9),
                'notes' => 'Bonne compréhension des concepts de base',
            ],
            [
                'parcours_ame_id' => 1,
                'etape_parcours_id' => 2, // ID d'étape du parcours 1
                'date_validation' => now()->subDays(7),
                'notes' => 'Intéressé par la personne de Jésus',
            ],

            // Étape validée pour le parcoursAme 2 (parcours terminé)
            [
                'parcours_ame_id' => 2,
                'etape_parcours_id' => 5, // ID d'étape du parcours 2
                'date_validation' => now()->subDays(4),
                'notes' => 'Très bonnes questions sur la Trinité',
            ],
            [
                'parcours_ame_id' => 2,
                'etape_parcours_id' => 6, // ID d'étape du parcours 2
                'date_validation' => now()->subDays(3),
                'notes' => 'Compréhension solide de l\'autorité biblique',
            ],
            [
                'parcours_ame_id' => 2,
                'etape_parcours_id' => 7, // ID d'étape du parcours 2
                'date_validation' => now()->subDays(2),
                'notes' => 'A bien saisi le concept de rédemption',
            ],
            [
                'parcours_ame_id' => 2,
                'etape_parcours_id' => 8, // ID d'étape du parcours 2
                'date_validation' => now()->subDays(1),
                'notes' => 'Terminé avec excellence',
            ],
        ];

        DB::table('etape_validees')->insert($etapesValidees);
    }
}
