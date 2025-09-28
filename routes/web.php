<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    try {
        return response()->json([
            'status' => true,
            'message' => 'Test simple - Laravel fonctionne',
            'timestamp' => now()
        ]);
    } catch (Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ], 500);
    }
});