<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'status' => true,
        'message' => 'Backend Invasion API',
        'endpoints' => [
            'api' => '/api/v1/',
            'auth' => '/api/v1/auth/'
        ]
    ]);
});