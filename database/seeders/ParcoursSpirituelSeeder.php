<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ParcoursSpirituelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $parcours = [
            [
                'nom' => 'Découverte de la Foi',
                'description' => 'Parcours initial pour les nouveaux convertis - bases du christianisme',
                'ordre' => 1,
                'est_actif' => true,
            ],
            [
                'nom' => 'Fondements Chrétiens',
                'description' => 'Approfondissement des doctrines essentielles de la foi',
                'ordre' => 2,
                'est_actif' => true,
            ],
            [
                'nom' => 'Disciples Actifs',
                'description' => 'Formation pour le service et l\'engagement dans l\'église',
                'ordre' => 3,
                'est_actif' => true,
            ],
            [
                'nom' => 'Leadership Spirituel',
                'description' => 'Préparation à responsabiliser d\'autres croyants',
                'ordre' => 4,
                'est_actif' => true,
            ],
            [
                'nom' => 'Ancien Parcours Alpha',
                'description' => 'Ancienne version du parcours découverte (désactivé)',
                'ordre' => 5,
                'est_actif' => false,
            ],
            [
                'nom' => 'École du Dimanche',
                'description' => 'Parcours d\'enseignement biblique systématique',
                'ordre' => 6,
                'est_actif' => true,
            ],
            [
                'nom' => 'Vie de Prière',
                'description' => 'Approfondissement de la communion avec Dieu',
                'ordre' => 7,
                'est_actif' => true,
            ],
        ];

        DB::table('parcours_spirituels')->insert($parcours);
    }
}
