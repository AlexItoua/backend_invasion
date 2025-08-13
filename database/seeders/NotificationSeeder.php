<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Désactiver les contraintes de clé étrangère temporairement
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('notifications')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $notifications = [
            // Notifications pour le Pasteur Nkounkou (user_id: 2)
            [
                'titre' => 'Nouvelle âme à suivre',
                'message' => 'Jean Kimbangu a été assigné à votre cellule. Merci de prendre contact.',
                'type' => 'in_app',
                'destinataire_id' => 2,
                'statut' => 'lue',
                'date_envoi' => Carbon::create(2024, 1, 21, 9, 30),
                'metadata' => json_encode(['ame_id' => 1, 'urgence' => 'normal']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'titre' => 'Rappel: Réunion des responsables',
                'message' => 'Votre présence est requise pour la réunion mensuelle demain à 10h.',
                'type' => 'sms',
                'destinataire_id' => 2,
                'statut' => 'envoyee',
                'date_envoi' => Carbon::create(2024, 2, 5, 16, 45),
                'metadata' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Notifications pour Soeur Loubaki (user_id: 4)
            [
                'titre' => 'Validation de parcours',
                'message' => 'Marie Loubaki a validé le parcours "Découverte de la Foi".',
                'type' => 'email',
                'destinataire_id' => 4,
                'statut' => 'en_attente',
                'date_envoi' => null,
                'metadata' => json_encode(['parcours_id' => 1, 'ame_id' => 2]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'titre' => 'Annulation de visite',
                'message' => 'La visite prévue avec la famille Mbemba est annulée.',
                'type' => 'push',
                'destinataire_id' => 4,
                'statut' => 'lue',
                'date_envoi' => Carbon::create(2024, 3, 15, 8, 0),
                'metadata' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Notification échouée
            [
                'titre' => 'Rappel: Formation évangélisation',
                'message' => 'N\'oubliez pas la formation demain à 14h à l\'église centrale.',
                'type' => 'sms',
                'destinataire_id' => 3, // Frère Mbemba
                'statut' => 'echouee',
                'date_envoi' => Carbon::create(2024, 2, 10, 12, 0),
                'metadata' => json_encode(['raison' => 'numéro indisponible']),
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Notification globale (sans destinataire spécifique)
            [
                'titre' => 'Nouvelle campagne lancée',
                'message' => 'La campagne "Brazzaville pour Christ" démarre le 15 avril. Inscrivez-vous!',
                'type' => 'in_app',
                'destinataire_id' => null,
                'statut' => 'envoyee',
                'date_envoi' => Carbon::create(2024, 4, 1, 10, 0),
                'metadata' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Insertion par lots avec gestion des timestamps
        DB::table('notifications')->insert($notifications);
    }
}
