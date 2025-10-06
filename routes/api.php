<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController,
    AmeController,
    CampagneController,
    CelluleController,
    InteractionController,
    ParcoursSpirituelController,
    EtapeValideeController,
    NotificationController,
    StatistiqueController,
    UserController,
    ZoneController,
    TacheController,
    ParcoursAmesController,
    EtapeParcoursController,
    ChatController
};
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Groupe principal API v1
Route::prefix('v1')->group(function () {

    // =============================================
    // Routes publiques (sans authentification)
    // =============================================

    // Authentification
    Route::prefix('auth')->controller(AuthController::class)->group(function () {
        Route::post('register', 'register')->name('api.auth.register');
        Route::post('login', 'login')->name('api.auth.login');
        Route::post('reset-password', 'resetPassword')->name('api.auth.reset-password');
        Route::post('forgot-password', 'forgotPassword')->name('api.auth.forgot-password');
        Route::post('verify-email', 'verifyEmail')->name('api.auth.verify-email');
    });

    // Zones accessibles publiquement (pour l'inscription)
    Route::prefix('zones')->controller(ZoneController::class)->group(function () {
        Route::get('/', 'indexPublic')->name('api.zones.public'); // Version publique pour inscription
        Route::get('/search', 'search')->name('api.zones.search'); // Recherche de zones
    });

    // Route de test API publique
    Route::get('/ping', function () {
        return response()->json([
            'status' => 'success',
            'message' => 'API v1 is working',
            'timestamp' => now()->toISOString(),
            'version' => '1.0.0'
        ]);
    })->name('api.ping');

    // =============================================
    // Routes protégées par Sanctum
    // =============================================

    Route::middleware('auth:sanctum')->group(function () {

        // Authentification (routes protégées)
        Route::prefix('auth')->group(function () {
            Route::post('logout', [AuthController::class, 'logout'])->name('api.auth.logout');
            Route::post('refresh', [AuthController::class, 'refresh'])->name('api.auth.refresh');
            Route::get('profile', [AuthController::class, 'profile'])->name('api.auth.profile');
            Route::put('profile', [AuthController::class, 'updateProfile'])->name('api.auth.update-profile');
        });

        // Utilisateur courant
        Route::get('user', function (Request $request) {
            $user = $request->user();
            $user->load(['zone', 'cellule']); // Charger les relations

            return response()->json([
                'status' => true,
                'message' => 'Utilisateur connecté récupéré avec succès',
                'data' => $user,
                'permissions' => $user->getAllPermissions()->pluck('name'),
                'roles' => $user->getRoleNames()
            ]);
        })->name('api.user.current');

        // =============================================
        // Routes des ressources principales
        // =============================================

        // Routes pour les âmes
        // Routes pour les âmes
        Route::prefix('ames')->controller(AmeController::class)->group(function () {
            // Routes spéciales (avant les routes avec paramètres)
            Route::get('/recentes', 'recentes')->name('api.ames.recentes');
            Route::get('/en-suivi', 'getAmesEnSuivi')->name('api.ames.en-suivi'); // ✅ NOUVELLE ROUTE
            Route::get('/nearby', 'nearBy')->name('api.ames.nearby');
            Route::get('/stats', 'stats')->name('api.ames.stats');
            Route::get('/search', 'search')->name('api.ames.search');
            Route::get('/export', 'export')->name('api.ames.export');

            // Routes CRUD standard
            Route::get('/', 'index')->name('api.ames.index');
            Route::post('/', 'store')->name('api.ames.store');
            Route::get('/{id}', 'show')->name('api.ames.show');
            Route::put('/{id}', 'update')->name('api.ames.update');
            Route::delete('/{id}', 'destroy')->name('api.ames.destroy');

            // Routes relationnelles
            Route::get('/{ame}/conversations', 'conversations')->name('api.ames.conversations');
            Route::get('/{ame}/interactions', 'interactions')->name('api.ames.interactions');
            Route::get('/{ame}/parcours', 'getParcoursParAme')->name('api.ames.parcours'); // ✅ NOUVELLE ROUTE
        });

        // Routes pour les campagnes
        Route::prefix('campagnes')->controller(CampagneController::class)->group(function () {
            Route::get('/', 'index')->name('api.campagnes.index');
            Route::post('/', 'store')->name('api.campagnes.store');
            Route::get('/active', 'active')->name('api.campagnes.active');
            Route::get('/{id}', 'show')->name('api.campagnes.show');
            Route::put('/{id}', 'update')->name('api.campagnes.update');
            Route::delete('/{id}', 'destroy')->name('api.campagnes.destroy');
            Route::post('/{id}/activate', 'activate')->name('api.campagnes.activate');
            Route::post('/{id}/deactivate', 'deactivate')->name('api.campagnes.deactivate');
        });

        // Routes pour les cartes et visualisations
        Route::prefix('cartes')->group(function () {
            Route::get('ames-par-zone', [AmeController::class, 'cartesData'])->name('api.cartes.ames-zones');
            Route::get('statistiques-geo', [StatistiqueController::class, 'statistiquesGeo'])->name('api.cartes.stats-geo');
            Route::get('heatmap', [AmeController::class, 'heatmapData'])->name('api.cartes.heatmap');
        });

        // Routes pour les rapports
        Route::prefix('rapports')->group(function () {
            Route::get('fidelisation', [StatistiqueController::class, 'fidelisation'])->name('api.rapports.fidelisation');
            Route::get('baptemes', [StatistiqueController::class, 'baptemes'])->name('api.rapports.baptemes');
            Route::get('croissance', [StatistiqueController::class, 'croissance'])->name('api.rapports.croissance');
            Route::get('activites', [StatistiqueController::class, 'activites'])->name('api.rapports.activites');
        });

        // Routes pour les cellules
        Route::prefix('cellules')->controller(CelluleController::class)->group(function () {
            Route::get('/', 'index')->name('api.cellules.index');
            Route::post('/', 'store')->name('api.cellules.store');
            Route::get('/my-cellule', 'maCellule')->name('api.cellules.my-cellule');
            Route::get('/{id}', 'show')->name('api.cellules.show');
            Route::put('/{id}', 'update')->name('api.cellules.update');
            Route::delete('/{id}', 'destroy')->name('api.cellules.destroy');
            Route::get('/{id}/membres', 'membres')->name('api.cellules.membres');
            Route::post('/{id}/join', 'rejoindre')->name('api.cellules.join');
            Route::post('/{id}/leave', 'quitter')->name('api.cellules.leave');
        });

        // Routes pour les tâches
        Route::prefix('taches')->controller(TacheController::class)->group(function () {
            Route::get('/recentes', 'recentes')->name('api.taches.recentes');
            Route::get('/my-tasks', 'mesTaches')->name('api.taches.my-tasks');
            Route::get('/', 'index')->name('api.taches.index');
            Route::post('/', 'store')->name('api.taches.store');
            Route::get('/{id}', 'show')->name('api.taches.show');
            Route::put('/{id}', 'update')->name('api.taches.update');
            Route::delete('/{id}', 'destroy')->name('api.taches.destroy');
            Route::post('/{id}/complete', 'marquerComplete')->name('api.taches.complete');
        });

        // Routes pour les étapes de parcours
        Route::prefix('etape-parcours')->controller(EtapeParcoursController::class)->group(function () {
            Route::get('/', 'index')->name('api.etape-parcours.index');
            Route::post('/', 'store')->name('api.etape-parcours.store');
            Route::get('/{id}', 'show')->name('api.etape-parcours.show');
            Route::put('/{id}', 'update')->name('api.etape-parcours.update');
            Route::delete('/{id}', 'destroy')->name('api.etape-parcours.destroy');
        });

        // Routes pour les interactions
        Route::prefix('interactions')->controller(InteractionController::class)->group(function () {
            Route::get('/', 'index')->name('api.interactions.index');
            Route::post('/', 'store')->name('api.interactions.store');
            Route::get('/recent', 'recent')->name('api.interactions.recent');
            Route::get('/my-interactions', 'mesInteractions')->name('api.interactions.my-interactions');
            Route::get('/{id}', 'show')->name('api.interactions.show');
            Route::put('/{id}', 'update')->name('api.interactions.update');
            Route::delete('/{id}', 'destroy')->name('api.interactions.destroy');
        });

        // Routes pour les conversations/chat
        Route::prefix('conversations')->controller(ChatController::class)->group(function () {
            Route::get('/', 'index')->name('api.conversations.index');
            Route::post('/', 'store')->name('api.conversations.store');
            Route::get('/unread-count', 'unreadCount')->name('api.conversations.unread-count');
            Route::get('/{conversation}/messages', 'messages')->name('api.conversations.messages');
            Route::post('/{conversation}/messages', 'sendMessage')->name('api.conversations.send-message');
            Route::post('/{conversation}/read', 'markAsRead')->name('api.conversations.mark-read');
            Route::delete('/{conversation}', 'destroy')->name('api.conversations.destroy');
        });

        // Routes pour les parcours spirituels
        Route::prefix('parcours-spirituels')->controller(ParcoursSpirituelController::class)->group(function () {
            Route::get('/', 'index')->name('api.parcours-spirituels.index');
            Route::post('/', 'store')->name('api.parcours-spirituels.store');
            Route::get('/disponibles', 'disponibles')->name('api.parcours-spirituels.disponibles');
            Route::get('/{id}', 'show')->name('api.parcours-spirituels.show');
            Route::put('/{id}', 'update')->name('api.parcours-spirituels.update');
            Route::delete('/{id}', 'destroy')->name('api.parcours-spirituels.destroy');

            // Gestion des parcours pour les âmes
            Route::post('/{id}/demarrer', 'demarrerParcours')->name('api.parcours-spirituels.demarrer');
            Route::get('/{id}/progression/{ameId}', 'progression')->name('api.parcours-spirituels.progression');
            Route::get('/{id}/etapes', 'etapes')->name('api.parcours-spirituels.etapes');
        });

        // Routes pour la gestion des parcours des âmes
        Route::prefix('parcours-ames')->controller(ParcoursAmesController::class)->group(function () {
            Route::post('/valider-etape', 'validerEtape')->name('api.parcours-ames.valider-etape');
            Route::get('/en-cours/{ameId}', 'parcoursEnCours')->name('api.parcours-ames.en-cours');
            Route::get('/mes-parcours', 'mesParcours')->name('api.parcours-ames.mes-parcours');
            Route::get('/statistiques', 'statistiques')->name('api.parcours-ames.statistiques');
        });

        // Routes pour les étapes validées
        Route::prefix('etape-validees')->controller(EtapeValideeController::class)->group(function () {
            Route::get('/', 'index')->name('api.etape-validees.index');
            Route::post('/', 'store')->name('api.etape-validees.store');
            Route::get('/{id}', 'show')->name('api.etape-validees.show');
            Route::put('/{id}', 'update')->name('api.etape-validees.update');
            Route::delete('/{id}', 'destroy')->name('api.etape-validees.destroy');
        });

        // Routes pour les notifications
        Route::prefix('notifications')->controller(NotificationController::class)->group(function () {
            Route::get('/', 'index')->name('api.notifications.index');
            Route::post('/', 'store')->name('api.notifications.store');
            Route::get('/unread', 'unread')->name('api.notifications.unread');
            Route::get('/count', 'count')->name('api.notifications.count');
            Route::get('/{id}', 'show')->name('api.notifications.show');
            Route::put('/{id}', 'update')->name('api.notifications.update');
            Route::delete('/{id}', 'destroy')->name('api.notifications.destroy');
            Route::post('/mark-as-read', 'markAsRead')->name('api.notifications.mark-as-read');
            Route::post('/mark-all-read', 'markAllAsRead')->name('api.notifications.mark-all-read');
        });

        // Routes pour les statistiques
        Route::prefix('statistiques')->controller(StatistiqueController::class)->group(function () {
            // Routes fixes en premier
            Route::get('hebdomadaires', 'statsHebdomadaires')->name('api.statistiques.hebdomadaires');
            Route::get('mensuelles', 'statsMensuelles')->name('api.statistiques.mensuelles');
            Route::get('dashboard', 'dashboard')->name('api.statistiques.dashboard');
            Route::get('globales', 'globales')->name('api.statistiques.globales');
            Route::get('comparatives', 'comparatives')->name('api.statistiques.comparatives');

            // Routes CRUD standard
            Route::get('/', 'index')->name('api.statistiques.index');
            Route::post('/', 'store')->name('api.statistiques.store');
            Route::get('/{id}', 'show')->name('api.statistiques.show');
            Route::put('/{id}', 'update')->name('api.statistiques.update');
            Route::delete('/{id}', 'destroy')->name('api.statistiques.destroy');
        });

        // Routes pour les utilisateurs
        Route::prefix('users')->controller(UserController::class)->group(function () {
            Route::get('/', 'index')->name('api.users.index');
            Route::post('/', 'store')->name('api.users.store');
            Route::get('/search', 'search')->name('api.users.search');
            Route::get('/active', 'active')->name('api.users.active');
            Route::get('/historiques', [\App\Http\Controllers\HistoriqueController::class, 'index'])->name('api.users.historiques');
            // ✅ ajout
            Route::get('/{id}', 'show')->name('api.users.show');
            Route::put('/{id}', 'update')->name('api.users.update');
            Route::delete('/{id}', 'destroy')->name('api.users.destroy');
            Route::post('/{id}/activate', 'activate')->name('api.users.activate');
            Route::post('/{id}/deactivate', 'deactivate')->name('api.users.deactivate');
        });


        // Routes zones protégées (pour les opérations sensibles)
        Route::prefix('zones')->controller(ZoneController::class)->group(function () {
            Route::post('/', 'store')->name('api.zones.store');
            Route::get('/{id}', 'show')->name('api.zones.show');
            Route::put('/{id}', 'update')->name('api.zones.update');
            Route::delete('/{id}', 'destroy')->name('api.zones.destroy');
            Route::get('/{id}/statistiques', 'statistiques')->name('api.zones.statistiques');
            Route::get('/{id}/ames', 'ames')->name('api.zones.ames');
        });

        // =============================================
        // Routes utilitaires
        // =============================================

        // Route de test authentifiée
        Route::get('/auth-ping', function (Request $request) {
            return response()->json([
                'status' => 'success',
                'message' => 'Authenticated API access working',
                'user' => $request->user()->only(['id', 'name', 'email']),
                'timestamp' => now()->toISOString()
            ]);
        })->name('api.auth-ping');
    });

    // =============================================
    // Route fallback pour l'API
    // =============================================
    Route::fallback(function () {
        return response()->json([
            'error' => 'API endpoint not found',
            'message' => 'The requested API endpoint does not exist',
            'available_endpoints' => [
                'POST /api/v1/auth/login' => 'User authentication',
                'GET /api/v1/zones' => 'Get zones (public)',
                'GET /api/v1/ping' => 'API health check',
            ],
            'documentation' => url('/api/documentation'),
            'timestamp' => now()->toISOString()
        ], 404);
    });
});