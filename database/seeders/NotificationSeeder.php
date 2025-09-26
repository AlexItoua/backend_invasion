<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        // Vider la table
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('notifications')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $notifications = [
            [
                'destinataire_id' => 1,
                'type' => 'in_app',
                'contenu' => 'Jean Kimbangu a été assigné à votre cellule. Merci de prendre contact.',
                'lu' => false,
                'date_notification' => Carbon::create(2024, 1, 21, 9, 30),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'destinataire_id' => 1,
                'type' => 'sms',
                'contenu' => 'Votre présence est requise pour la réunion mensuelle demain à 10h.',
                'lu' => false,
                'date_notification' => Carbon::create(2024, 2, 5, 16, 45),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'destinataire_id' => 1,
                'type' => 'email',
                'contenu' => 'Marie Loubaki a validé le parcours "Découverte de la Foi".',
                'lu' => false,
                'date_notification' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'destinataire_id' => 1,
                'type' => 'push',
                'contenu' => 'La visite prévue avec la famille Mbemba est annulée.',
                'lu' => false,
                'date_notification' => Carbon::create(2024, 3, 15, 8, 0),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'destinataire_id' => 1,
                'type' => 'sms',
                'contenu' => 'N\'oubliez pas la formation demain à 14h à l\'église centrale.',
                'lu' => false,
                'date_notification' => Carbon::create(2024, 2, 10, 12, 0),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'destinataire_id' => null, // notification globale
                'type' => 'in_app',
                'contenu' => 'La campagne "Brazzaville pour Christ" démarre le 15 avril. Inscrivez-vous!',
                'lu' => false,
                'date_notification' => Carbon::create(2024, 4, 1, 10, 0),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('notifications')->insert($notifications);
    }
}
