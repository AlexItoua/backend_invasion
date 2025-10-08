<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatistiqueSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('statistiques')->delete();

        $campagneId = DB::table('campagnes')->where('nom', 'Invasion 2025')->value('id');

        // Si la campagne n'existe pas encore
        if (!$campagneId) {
            $campagneId = DB::table('campagnes')->insertGetId([
                'nom' => 'Invasion 2025',
                'date_debut' => Carbon::create(2025, 1, 1),
                'date_fin' => Carbon::create(2025, 12, 31),
                'zone_id' => 1,
                'description' => 'Grande campagne d\'évangélisation et de mobilisation spirituelle pour 2025.',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Statistiques cohérentes avec une petite campagne (8 âmes)
        $statistiques = [
            // Situation à mi-campagne (juin)
            [
                'campagne_id' => $campagneId,
                'total_ames' => 8,            // 8 âmes gagnées
                'baptises' => 3,             // 3 baptisés sur 8
                'fidelises' => 5,            // 5 fidèles (suivis ou intégrés)
                'nouvelles_ames' => 8,       // Toutes sont nouvelles à ce stade
                'taux_conversion' => 37.5,   // 3 / 8 * 100
                'taux_fidelisation' => 62.5, // 5 / 8 * 100
                'date_generation' => Carbon::create(2025, 6, 30),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Situation à fin de campagne (décembre)
            [
                'campagne_id' => $campagneId,
                'total_ames' => 15,           // progression totale
                'baptises' => 7,              // 7 baptisés sur 15
                'fidelises' => 10,            // 10 fidèles
                'nouvelles_ames' => 7,        // 7 nouvelles depuis juin
                'taux_conversion' => 46.7,    // 7 / 15 * 100
                'taux_fidelisation' => 66.7,  // 10 / 15 * 100
                'date_generation' => Carbon::create(2025, 12, 31),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('statistiques')->insert($statistiques);
    }
}
