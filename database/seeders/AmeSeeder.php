<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AmeSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('ames')->delete();

        // Récupérer les IDs
        $userIds = DB::table('users')->pluck('id')->toArray();
        $celluleIds = DB::table('cellules')->pluck('id')->toArray();
        $campagneId = DB::table('campagnes')->where('nom', 'Invasion 2025')->value('id');

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

        $donnees = [
            ['nom' => 'Nensa bimpe olan', 'telephone' => '053338505', 'adresse' => 'jack opango', 'sexe' => 'homme'],
            ['nom' => 'Dykoka-Ngolo Benie Nathan', 'telephone' => '067819985', 'adresse' => '45 Avenue de la Liberté', 'sexe' => 'homme'],
            ['nom' => 'Dykoka-Ngolo Trésors', 'telephone' => '068597869', 'adresse' => '45 Avenue de la Liberté', 'sexe' => 'homme'],
            ['nom' => 'Itoua julia princia', 'telephone' => '066762383', 'adresse' => '145 Itoumbi rue Owando', 'sexe' => 'femme'],
            ['nom' => 'Ikama rugie christ', 'telephone' => '066929917', 'adresse' => '121 Och Mougali', 'sexe' => 'homme'],
            ['nom' => 'Zita diane', 'telephone' => '065195856', 'adresse' => '12 rue des Marthyre', 'sexe' => 'femme'],
            ['nom' => 'Nensa junior Olsen', 'telephone' => '69331121', 'adresse' => '57 Mazala', 'sexe' => 'homme'],
            ['nom' => 'Itoua yves caleb', 'telephone' => '068731172', 'adresse' => '145 Itoumbi rue Owando', 'sexe' => 'homme'],
        ];

        $imagesHommes = [
            'https://images.pexels.com/photos/33552516/pexels-photo-33552516.jpeg',
            'https://images.pexels.com/photos/13169320/pexels-photo-13169320.jpeg',
            'https://images.pexels.com/photos/13328793/pexels-photo-13328793.jpeg',
            'https://images.pexels.com/photos/11931208/pexels-photo-11931208.jpeg',
        ];

        $imageFemme = 'https://images.pexels.com/photos/10294305/pexels-photo-10294305.jpeg';

        $ames = [];
        foreach ($donnees as $d) {
            $ames[] = [
                'nom' => $d['nom'],
                'telephone' => $d['telephone'],
                'sexe' => $d['sexe'],
                'age' => rand(18, 60),
                'adresse' => $d['adresse'],
                'quartier' => 'Non défini',
                'ville' => 'Brazzaville',
                'date_conversion' => now()->subDays(rand(1, 100)),
                'campagne_id' => $campagneId,
                'type_decision' => 'Première décision',
                'latitude' => null,
                'longitude' => null,
                'geoloc_accuracy' => null,
                'geoloc_timestamp' => null,
                'assigne_a' => !empty($userIds) ? $userIds[array_rand($userIds)] : null,
                'cellule_id' => !empty($celluleIds) ? $celluleIds[array_rand($celluleIds)] : null,
                'image' => $d['sexe'] === 'homme'
                    ? $imagesHommes[array_rand($imagesHommes)]
                    : $imageFemme,
                'suivi' => false,
                'derniere_interaction' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('ames')->insert($ames);
    }
}
