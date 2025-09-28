<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

// Test ultra simple
Route::get('/', function () {
    return response('Laravel is working on Railway! 🚀', 200)
        ->header('Content-Type', 'text/plain');
});

// Test JSON
Route::get('/api-test', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'API is working',
        'environment' => app()->environment(),
        'port' => $_ENV['PORT'] ?? 'unknown',
        'timestamp' => now()
    ]);
});

// Test base de données
Route::get('/db-test', function () {
    try {
        $pdo = DB::connection()->getPdo();
        return response()->json([
            'database' => 'connected',
            'driver' => config('database.default'),
            'host' => config('database.connections.mysql.host')
        ]);
    } catch (Exception $e) {
        return response()->json([
            'database' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
});


Route::fallback(function () {
    return response()->json(['message' => 'Route not found'], 404);
});
