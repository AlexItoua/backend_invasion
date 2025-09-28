<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route de santé principale pour Railway
Route::get('/', function () {
    try {
        // Test de base de données (optionnel)
        $dbStatus = 'unknown';
        try {
            DB::connection()->getPdo();
            $dbStatus = 'connected';
        } catch (\Exception $e) {
            $dbStatus = 'disconnected: ' . $e->getMessage();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Laravel Backend API - Invasion App',
            'version' => '1.0.0',
            'timestamp' => now()->toISOString(),
            'environment' => app()->environment(),
            'database' => $dbStatus,
            'api_base_url' => url('/api/v1'),
            'endpoints' => [
                'auth' => [
                    'register' => 'POST /api/v1/auth/register',
                    'login' => 'POST /api/v1/auth/login',
                    'logout' => 'POST /api/v1/auth/logout',
                ],
                'public' => [
                    'zones' => 'GET /api/v1/zones',
                ],
                'protected' => [
                    'ames' => 'GET /api/v1/ames',
                    'campagnes' => 'GET /api/v1/campagnes',
                    'cellules' => 'GET /api/v1/cellules',
                    'interactions' => 'GET /api/v1/interactions',
                    'parcours-spirituels' => 'GET /api/v1/parcours-spirituels',
                    'notifications' => 'GET /api/v1/notifications',
                    'statistiques' => 'GET /api/v1/statistiques',
                ]
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Application error',
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => app()->environment('local') ? $e->getTraceAsString() : null
        ], 500);
    }
});

// Route de santé pour les checks Railway
Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'timestamp' => now()->toISOString(),
        'uptime' => File::exists(base_path('bootstrap/cache/config.php')) ? 'optimized' : 'normal',
        'memory_usage' => memory_get_usage(true),
        'memory_peak' => memory_get_peak_usage(true)
    ]);
});

// Route de debug (uniquement en développement)
Route::get('/debug', function () {
    if (app()->environment('production')) {
        abort(404);
    }

    return response()->json([
        'app' => [
            'name' => config('app.name'),
            'version' => app()->version(),
            'environment' => app()->environment(),
            'debug' => config('app.debug'),
            'url' => config('app.url'),
            'timezone' => config('app.timezone'),
            'locale' => config('app.locale'),
        ],
        'server' => [
            'php_version' => PHP_VERSION,
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown',
            'port' => $_SERVER['PORT'] ?? $_ENV['PORT'] ?? 'Unknown',
        ],
        'database' => [
            'default' => config('database.default'),
            'connections' => array_keys(config('database.connections')),
        ],
        'cache' => [
            'config_cached' => file_exists(base_path('bootstrap/cache/config.php')),
            'routes_cached' => file_exists(base_path('bootstrap/cache/routes-v7.php')),
            'views_cached' => file_exists(resource_path('views')),
        ],
        'storage' => [
            'app_writable' => is_writable(storage_path('app')),
            'logs_writable' => is_writable(storage_path('logs')),
            'cache_writable' => is_writable(storage_path('framework/cache')),
        ],
        'env_vars' => [
            'APP_KEY' => config('app.key') ? 'SET' : 'NOT_SET',
            'DB_CONNECTION' => config('database.default'),
            'SANCTUM_STATEFUL_DOMAINS' => config('sanctum.stateful'),
        ]
    ]);
});

// Route pour les informations de l'API
Route::get('/api-info', function () {
    return response()->json([
        'api_version' => 'v1',
        'documentation' => url('/api/documentation'),
        'base_url' => url('/api/v1'),
        'authentication' => 'Laravel Sanctum',
        'content_type' => 'application/json',
        'cors_enabled' => true,
        'rate_limiting' => 'Applied to API routes',
        'last_updated' => '2025-09-28'
    ]);
});

// Fallback pour rediriger vers l'API
Route::fallback(function () {
    return response()->json([
        'error' => 'Route not found',
        'message' => 'This is the backend API. Please use /api/v1/ endpoints',
        'available_endpoints' => [
            'GET /' => 'API status and documentation',
            'GET /health' => 'Health check',
            'GET /api-info' => 'API information',
            'POST /api/v1/auth/register' => 'User registration',
            'POST /api/v1/auth/login' => 'User login',
            'GET /api/v1/zones' => 'Get zones (public)',
        ],
        'timestamp' => now()->toISOString()
    ], 404);
});