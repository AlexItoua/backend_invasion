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
    ZoneController
};
use Illuminate\Http\Request;

Route::prefix('v1')->group(function () {
    // Authentification (routes publiques)
    Route::prefix('auth')->controller(AuthController::class)->group(function () {
        Route::post('register', 'register');
        Route::post('login', 'login');
        Route::post('reset-password', 'resetPassword');
    });

    // Zones accessibles publiquement (pour l'inscription)
    Route::prefix('zones')->controller(ZoneController::class)->group(function () {
        Route::get('/', 'indexPublic'); // Version publique
    });

    // Routes protégées par Sanctum
    Route::middleware('auth:sanctum')->group(function () {
        // Authentification
        Route::prefix('auth')->group(function () {
            Route::post('logout', [AuthController::class, 'logout']);
        });

        // Utilisateur courant
        Route::get('user', function (Request $request) {
            return response()->json([
                'status' => true,
                'message' => 'Utilisateur connecté récupéré avec succès',
                'data' => $request->user()
            ]);
        });

        // Routes avec préfixes
        Route::prefix('ames')->controller(AmeController::class)->group(function () {
            Route::get('/recentes', 'recentes'); // ✅ mettre AVANT /{id}

            Route::get('/', 'index');
            Route::post('/', 'store');
            Route::get('/{id}', 'show');
            Route::put('/{id}', 'update');
            Route::delete('/{id}', 'destroy');
        });

        Route::prefix('campagnes')->controller(CampagneController::class)->group(function () {
            Route::get('/', 'index');
            Route::post('/', 'store');
            Route::get('/{id}', 'show');
            Route::put('/{id}', 'update');
            Route::delete('/{id}', 'destroy');
        });
        // Dans api.php
        Route::prefix('cartes')->group(function () {
            Route::get('ames-par-zone', [AmeController::class, 'cartesData']);
        });

        Route::prefix('rapports')->group(function () {
            Route::get('fidelisation', [StatistiqueController::class, 'fidelisation']);
            Route::get('baptemes', [StatistiqueController::class, 'baptemes']);
        });

        Route::prefix('cellules')->controller(CelluleController::class)->group(function () {
            Route::get('/', 'index');
            Route::post('/', 'store');
            Route::get('/{id}', 'show');
            Route::put('/{id}', 'update');
            Route::delete('/{id}', 'destroy');
        });

        Route::prefix('interactions')->controller(InteractionController::class)->group(function () {
            Route::get('/', 'index');
            Route::post('/', 'store');
            Route::get('/{id}', 'show');
            Route::put('/{id}', 'update');
            Route::delete('/{id}', 'destroy');
        });

        Route::prefix('parcours-spirituels')->controller(ParcoursSpirituelController::class)->group(function () {
            Route::get('/', 'index');
            Route::post('/', 'store');
            Route::get('/{id}', 'show');
            Route::put('/{id}', 'update');
            Route::delete('/{id}', 'destroy');
        });

        Route::prefix('etapes-validees')->controller(EtapeValideeController::class)->group(function () {
            Route::get('/', 'index');
            Route::post('/', 'store');
            Route::get('/{id}', 'show');
            Route::put('/{id}', 'update');
            Route::delete('/{id}', 'destroy');
        });

        Route::prefix('notifications')->controller(NotificationController::class)->group(function () {
            Route::get('/', 'index');
            Route::post('/', 'store');
            Route::get('/{id}', 'show');
            Route::put('/{id}', 'update');
            Route::delete('/{id}', 'destroy');
            Route::post('mark-as-read', 'markAsRead');
        });
        Route::prefix('statistiques')->controller(StatistiqueController::class)->group(function () {
            Route::get('/', 'index');
            Route::post('/', 'store');

            // ⚠️ Les routes fixes en premier
            Route::get('hebdomadaires', 'statsHebdomadaires');
            Route::get('mensuelles', 'statsMensuelles');

            // Ensuite la route dynamique
            Route::get('/{id}', 'show');
            Route::put('/{id}', 'update');
            Route::delete('/{id}', 'destroy');
        });

        Route::prefix('users')->controller(UserController::class)->group(function () {
            Route::get('/', 'index');
            Route::post('/', 'store');
            Route::get('/{id}', 'show');
            Route::put('/{id}', 'update');
            Route::delete('/{id}', 'destroy');
        });

        // Routes zones protégées (pour les opérations sensibles)
        Route::prefix('zones')->controller(ZoneController::class)->group(function () {
            Route::post('/', 'store');
            Route::get('/{id}', 'show');
            Route::put('/{id}', 'update');
            Route::delete('/{id}', 'destroy');
        });
    });
});