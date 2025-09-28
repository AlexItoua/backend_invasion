<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => [
        'api/*',
        'sanctum/csrf-cookie',
        'health',
        'debug',
        'api-info'
    ],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:3000',
        'http://localhost:3001',
        'http://localhost:8080',
        'http://127.0.0.1:3000',
        'http://127.0.0.1:3001',
        'http://127.0.0.1:8080',
        // Ajoutez vos domaines de production ici
        'https://votre-frontend.netlify.app',
        'https://votre-frontend.vercel.app',
        // Domaine Railway si vous avez un frontend sur Railway aussi
        'https://*.railway.app',
    ],

    'allowed_origins_patterns' => [
        // Permettre tous les sous-domaines Railway pour le développement
        '/^https:\/\/.*\.railway\.app$/',
        // Permettre localhost sur tous les ports
        '/^http:\/\/localhost:\d+$/',
        '/^http:\/\/127\.0\.0\.1:\d+$/',
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => [
        'X-Total-Count',
        'X-Page-Count',
        'X-Per-Page',
        'X-Current-Page',
    ],

    'max_age' => 86400, // 24 heures

    'supports_credentials' => true,

];