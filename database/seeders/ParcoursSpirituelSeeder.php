<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ParcoursSpirituelSeeder extends Seeder
{
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

        // Ajouter des étapes pour chaque parcours
        $etapes = [
            // Étapes pour le parcours "Découverte de la Foi" (ID: 1)
            [
                'parcours_spirituel_id' => 1,
                'titre' => 'Introduction à la foi chrétienne',
                'description' => 'Découverte des bases de la foi chrétienne',
                'contenu' => 'Dans cette étape, nous explorerons les fondements de la foi chrétienne et ce que signifie suivre Jésus.',
                'ordre' => 1,
                'duree_estimee_minutes' => 30,
                'est_actif' => true,
            ],
            [
                'parcours_spirituel_id' => 1,
                'titre' => 'La personne de Jésus-Christ',
                'description' => 'Qui est Jésus et pourquoi est-il important?',
                'contenu' => 'Découvrez la personne et l\'œuvre de Jésus-Christ, le fondement de la foi chrétienne.',
                'ordre' => 2,
                'duree_estimee_minutes' => 45,
                'est_actif' => true,
            ],
            [
                'parcours_spirituel_id' => 1,
                'titre' => 'Le salut par la grâce',
                'description' => 'Comprendre le don gratuit du salut',
                'contenu' => 'Explorez le concept de la grâce et comment nous recevons le salut par la foi en Jésus.',
                'ordre' => 3,
                'duree_estimee_minutes' => 40,
                'est_actif' => true,
            ],
            [
                'parcours_spirituel_id' => 1,
                'titre' => 'La vie nouvelle en Christ',
                'description' => 'Vivre la transformation intérieure',
                'contenu' => 'Apprenez ce que signifie vivre une vie transformée par la puissance du Saint-Esprit.',
                'ordre' => 4,
                'duree_estimee_minutes' => 50,
                'est_actif' => true,
            ],

            // Étapes pour le parcours "Fondements Chrétiens" (ID: 2)
            [
                'parcours_spirituel_id' => 2,
                'titre' => 'La doctrine de la Trinité',
                'description' => 'Comprendre Dieu le Père, le Fils et le Saint-Esprit',
                'contenu' => 'Explorez le mystère de la Trinité et la nature de Dieu.',
                'ordre' => 1,
                'duree_estimee_minutes' => 60,
                'est_actif' => true,
            ],
            [
                'parcours_spirituel_id' => 2,
                'titre' => 'L\'autorité de la Bible',
                'description' => 'La Parole de Dieu comme fondement de la foi',
                'contenu' => 'Découvrez pourquoi la Bible est considérée comme la Parole inspirée de Dieu.',
                'ordre' => 2,
                'duree_estimee_minutes' => 45,
                'est_actif' => true,
            ],
            [
                'parcours_spirituel_id' => 2,
                'titre' => 'Le péché et la rédemption',
                'description' => 'La nature du péché et l\'œuvre de la croix',
                'contenu' => 'Comprenez la gravité du péché et comment Jésus nous en a rachetés.',
                'ordre' => 3,
                'duree_estimee_minutes' => 50,
                'est_actif' => true,
            ],
            [
                'parcours_spirituel_id' => 2,
                'titre' => 'L\'Église et les sacrements',
                'description' => 'La communauté des croyants et les ordonnances',
                'contenu' => 'Explorez le rôle de l\'Église et l\'importance du baptême et de la sainte cène.',
                'ordre' => 4,
                'duree_estimee_minutes' => 55,
                'est_actif' => true,
            ],

            // Étapes pour le parcours "Disciples Actifs" (ID: 3)
            [
                'parcours_spirituel_id' => 3,
                'titre' => 'Les disciplines spirituelles',
                'description' => 'Prière, jeûne et méditation de la Parole',
                'contenu' => 'Développez une vie de discipline spirituelle pour grandir dans votre foi.',
                'ordre' => 1,
                'duree_estimee_minutes' => 50,
                'est_actif' => true,
            ],
            [
                'parcours_spirituel_id' => 3,
                'titre' => 'Le témoignage personnel',
                'description' => 'Partager sa foi avec les autres',
                'contenu' => 'Apprenez à partager votre témoignage et l\'évangile de manière efficace.',
                'ordre' => 2,
                'duree_estimee_minutes' => 45,
                'est_actif' => true,
            ],
            [
                'parcours_spirituel_id' => 3,
                'titre' => 'Le service dans l\'église',
                'description' => 'Découvrir et utiliser ses dons spirituels',
                'contenu' => 'Identifiez vos dons spirituels et comment les utiliser pour servir l\'Église.',
                'ordre' => 3,
                'duree_estimee_minutes' => 60,
                'est_actif' => true,
            ],
            [
                'parcours_spirituel_id' => 3,
                'titre' => 'La gestion du temps et des ressources',
                'description' => 'Gérer sa vie selon les priorités divines',
                'contenu' => 'Apprenez à gérer votre temps, vos talents et vos ressources pour la gloire de Dieu.',
                'ordre' => 4,
                'duree_estimee_minutes' => 55,
                'est_actif' => true,
            ],

            // Étapes pour le parcours "Leadership Spirituel" (ID: 4)
            [
                'parcours_spirituel_id' => 4,
                'titre' => 'Le caractère du leader',
                'description' => 'Les qualités spirituelles d\'un leader',
                'contenu' => 'Développez le caractère nécessaire pour exercer un leadership spirituel.',
                'ordre' => 1,
                'duree_estimee_minutes' => 60,
                'est_actif' => true,
            ],
            [
                'parcours_spirituel_id' => 4,
                'titre' => 'La vision et la direction',
                'description' => 'Discerner et communiquer la vision divine',
                'contenu' => 'Apprenez à discerner la direction de Dieu et à la communiquer aux autres.',
                'ordre' => 2,
                'duree_estimee_minutes' => 55,
                'est_actif' => true,
            ],
            [
                'parcours_spirituel_id' => 4,
                'titre' => 'Le mentorat et le discipulat',
                'description' => 'Former la prochaine génération de leaders',
                'contenu' => 'Découvrez comment investir dans la vie des autres pour les faire grandir spirituellement.',
                'ordre' => 3,
                'duree_estimee_minutes' => 65,
                'est_actif' => true,
            ],
            [
                'parcours_spirituel_id' => 4,
                'titre' => 'Gérer les conflits et les défis',
                'description' => 'Navigation dans les situations difficiles',
                'contenu' => 'Apprenez à gérer les conflits et les défis avec sagesse et grâce.',
                'ordre' => 4,
                'duree_estimee_minutes' => 70,
                'est_actif' => true,
            ],

            // Étapes pour le parcours "École du Dimanche" (ID: 6)
            [
                'parcours_spirituel_id' => 6,
                'titre' => 'Introduction à l\'Ancien Testament',
                'description' => 'Panorama de l\'Ancien Testament',
                'contenu' => 'Découvrez la structure, les thèmes principaux et l\'importance de l\'Ancien Testament.',
                'ordre' => 1,
                'duree_estimee_minutes' => 60,
                'est_actif' => true,
            ],
            [
                'parcours_spirituel_id' => 6,
                'titre' => 'Introduction au Nouveau Testament',
                'description' => 'Panorama du Nouveau Testament',
                'contenu' => 'Explorez les livres du Nouveau Testament et leur message central.',
                'ordre' => 2,
                'duree_estimee_minutes' => 60,
                'est_actif' => true,
            ],
            [
                'parcours_spirituel_id' => 6,
                'titre' => 'Les grandes doctrines bibliques',
                'description' => 'Étude systématique de la théologie',
                'contenu' => 'Approfondissez votre compréhension des doctrines centrales de la foi chrétienne.',
                'ordre' => 3,
                'duree_estimee_minutes' => 75,
                'est_actif' => true,
            ],
            [
                'parcours_spirituel_id' => 6,
                'titre' => 'L\'histoire de l\'Église',
                'description' => 'Les grandes périodes de l\'histoire chrétienne',
                'contenu' => 'Découvrez comment l\'Église a évolué à travers les siècles.',
                'ordre' => 4,
                'duree_estimee_minutes' => 70,
                'est_actif' => true,
            ],

            // Étapes pour le parcours "Vie de Prière" (ID: 7)
            [
                'parcours_spirituel_id' => 7,
                'titre' => 'Les bases de la prière',
                'description' => 'Apprendre à parler avec Dieu',
                'contenu' => 'Découvrez les fondements de la prière et comment développer une relation avec Dieu.',
                'ordre' => 1,
                'duree_estimee_minutes' => 40,
                'est_actif' => true,
            ],
            [
                'parcours_spirituel_id' => 7,
                'titre' => 'Les différents types de prière',
                'description' => 'Adoration, intercession, supplication, etc.',
                'contenu' => 'Explorez les différentes formes de prière et leurs objectifs spécifiques.',
                'ordre' => 2,
                'duree_estimee_minutes' => 50,
                'est_actif' => true,
            ],
            [
                'parcours_spirituel_id' => 7,
                'titre' => 'Le jeûne et la prière',
                'description' => 'Combiner jeûne et prière pour une percée spirituelle',
                'contenu' => 'Apprenez comment le jeûne peut intensifier votre vie de prière.',
                'ordre' => 3,
                'duree_estimee_minutes' => 55,
                'est_actif' => true,
            ],
            [
                'parcours_spirituel_id' => 7,
                'titre' => 'La prière de combat spirituel',
                'description' => 'Prier avec autorité contre les forces des ténèbres',
                'contenu' => 'Découvrez comment prier avec autorité dans les situations spirituellement difficiles.',
                'ordre' => 4,
                'duree_estimee_minutes' => 60,
                'est_actif' => true,
            ],
        ];

        DB::table('etape_parcours')->insert($etapes);
    }
}
