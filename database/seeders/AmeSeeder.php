<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AmeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ames = [
            // Âmes de la campagne Bacongo
            [
                'nom' => 'Jean Kimbangu',
                'telephone' => '242054512345',
                'sexe' => 'H',
                'age' => 32,
                'adresse' => 'Avenue Matsoua, Bacongo',
                'date_conversion' => Carbon::create(2024, 1, 20),
                'campagne_id' => 1,
                'type_decision' => 'Première décision',
                'latitude' => -4.2694400,
                'longitude' => 15.2711100,
                'assigne_a' => 3,
                'cellule_id' => 1,
                'image' => 'https://images.pexels.com/photos/14931950/pexels-photo-14931950.jpeg',
                 'suivi' => true,
    'derniere_interaction' => Carbon::create(2024, 2, 18),
            ],
            [
                'nom' => 'Marie Loubaki',
                'telephone' => '242055523456',
                'sexe' => 'F',
                'age' => 28,
                'adresse' => 'Rue Loutassi, Bacongo',
                'date_conversion' => Carbon::create(2024, 2, 15),
                'campagne_id' => 1,
                'type_decision' => 'Rédication',
                'latitude' => -4.2700000,
                'longitude' => 15.2720000,
                'assigne_a' => 4,
                'cellule_id' => 1,
                'image' => 'https://images.pexels.com/photos/7372390/pexels-photo-7372390.jpeg',
                 'suivi' => false,
    'derniere_interaction' => Carbon::create(2024, 2, 20),
            ],

            // Âmes de la campagne Poto-Poto
            [
                'nom' => 'Marcelline Nkounkou',
                'telephone' => '242066634567',
                'sexe' => 'F',
                'age' => 45,
                'adresse' => 'Avenue Foch, Poto-Poto',
                'date_conversion' => Carbon::create(2024, 3, 5),
                'campagne_id' => 2,
                'type_decision' => 'Première décision',
                'latitude' => -4.2638900,
                'longitude' => 15.2791700,
                'assigne_a' => 2,
                'cellule_id' => 2,
                'image' => 'https://images.pexels.com/photos/16748461/pexels-photo-16748461.jpeg',
                'suivi' => true,
    'derniere_interaction' => Carbon::create(2024, 3, 7),
            ],
            [
                'nom' => 'Didier Mboungou',
                'telephone' => '242067745678',
                'sexe' => 'H',
                'age' => 22,
                'adresse' => 'Rue Mfoa, Poto-Poto',
                'date_conversion' => null,
                'campagne_id' => 2,
                'type_decision' => 'En réflexion',
                'latitude' => -4.2650000,
                'longitude' => 15.2800000,
                'assigne_a' => 5,
                'cellule_id' => 3,
                'image' => 'https://images.pexels.com/photos/5945245/pexels-photo-5945245.jpeg',
                 'suivi' => false,
    'derniere_interaction' => null,
            ],

            // Âmes de la campagne Talangaï
            [
                'nom' => 'Grâce Okombi',
                'telephone' => '242078856789',
                'sexe' => 'F',
                'age' => 35,
                'adresse' => 'Quartier 15, Talangaï',
                'date_conversion' => Carbon::create(2024, 4, 10),
                'campagne_id' => 3,
                'type_decision' => 'Renouvellement',
                'latitude' => -4.2200000,
                'longitude' => 15.3000000,
                'assigne_a' => 6,
                'cellule_id' => 4,
                'image' => 'https://images.pexels.com/photos/7088971/pexels-photo-7088971.jpeg',
                 'suivi' => true,
    'derniere_interaction' => Carbon::create(2024, 5, 1),
            ],
            [
                'nom' => 'Jonathan Itoua',
                'telephone' => '242079967890',
                'sexe' => 'H',
                'age' => 19,
                'adresse' => 'Quartier 20, Talangaï',
                'date_conversion' => Carbon::create(2024, 5, 12),
                'campagne_id' => 3,
                'type_decision' => 'Première décision',
                'latitude' => -4.2250000,
                'longitude' => 15.3050000,
                'assigne_a' => 7,
                'cellule_id' => 4,
                'image' => 'https://images.pexels.com/photos/15451658/pexels-photo-15451658.jpeg',
                'suivi' => false,
    'derniere_interaction' => Carbon::create(2024, 5, 18),
            ],
        ];

        DB::table('ames')->insert($ames);
    }
}
