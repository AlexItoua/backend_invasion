<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TacheSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $taches = [
            // Tâches liées à la campagne Bacongo
            [
                'titre' => 'Suivi des nouveaux convertis',
                'description' => 'Assurer le suivi spirituel des personnes converties lors de la dernière campagne.',
                'user_id' => 5, // évangéliste
                'ame_id' => null,
                'echeance' => Carbon::create(2024, 2, 15),
                'statut' => 'en_attente',
                'priorite' => 'haute',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'titre' => 'Organisation d\'une cellule de prière',
                'description' => 'Mettre en place une cellule de prière hebdomadaire dans la zone Bacongo.',
                'user_id' => 3, // encadreur
                'ame_id' => null,
                'echeance' => Carbon::create(2024, 3, 1),
                'statut' => 'en_attente',
                'priorite' => 'normale',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Tâches liées à la campagne Poto-Poto
            [
                'titre' => 'Visite des familles',
                'description' => 'Rendre visite aux familles intéressées par l\'évangile.',
                'user_id' => 6, // évangéliste
                'ame_id' => null,
                'echeance' => Carbon::create(2024, 3, 12),
                'statut' => 'terminee',
                'priorite' => 'haute',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'titre' => 'Distribution de tracts',
                'description' => 'Distribuer des tracts dans le quartier central de Poto-Poto.',
                'user_id' => 4,
                'ame_id' => null,
                'echeance' => Carbon::create(2024, 3, 15),
                'statut' => 'en_attente',
                'priorite' => 'basse',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Tâches liées à la campagne Talangaï
            [
                'titre' => 'Organisation d\'un concert gospel',
                'description' => 'Préparer un concert gospel pour attirer les jeunes.',
                'user_id' => 7,
                'ame_id' => null,
                'echeance' => Carbon::create(2024, 4, 20),
                'statut' => 'en_attente',
                'priorite' => 'haute',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'titre' => 'Rencontre avec les leaders locaux',
                'description' => 'Rencontrer les responsables du quartier pour présenter le projet.',
                'user_id' => 8,
                'ame_id' => null,
                'echeance' => Carbon::create(2024, 4, 7),
                'statut' => 'terminee',
                'priorite' => 'normale',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('taches')->insert($taches);
    }
}