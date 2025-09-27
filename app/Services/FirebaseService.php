<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class FirebaseService
{
    protected $client;
    protected $serverKey;
    protected $projectId;
    protected $databaseUrl;

    public function __construct()
    {
        $this->client = new Client();
        $this->serverKey = config('services.firebase.server_key');
        $this->projectId = config('services.firebase.project_id');
        $this->databaseUrl = config('services.firebase.database_url');

        if (!$this->serverKey) {
            Log::warning('Clé serveur Firebase non configurée');
        }
    }

    /**
     * Envoie une notification push via FCM
     */
    public function sendNotification($tokens, $title, $body, $data = [])
    {
        if (!$this->serverKey) {
            throw new \Exception('Clé serveur Firebase non configurée');
        }

        $payload = [
            'registration_ids' => is_array($tokens) ? $tokens : [$tokens],
            'notification' => [
                'title' => $title,
                'body' => $body,
                'sound' => 'default',
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            ],
            'data' => array_merge(['type' => 'message'], $data),
            'apns' => [
                'payload' => [
                    'aps' => [
                        'sound' => 'default',
                        'badge' => 1,
                    ],
                ],
            ],
            'android' => [
                'priority' => 'high',
            ],
        ];

        try {
            $response = $this->client->post('https://fcm.googleapis.com/fcm/send', [
                'headers' => [
                    'Authorization' => 'key=' . $this->serverKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => $payload,
                'timeout' => 10,
            ]);

            $responseData = json_decode($response->getBody(), true);

            if (isset($responseData['failure']) && $responseData['failure'] > 0) {
                Log::error('Erreurs Firebase: ' . json_encode($responseData));
            }

            return $responseData;
        } catch (\Exception $e) {
            Log::error('Erreur Firebase: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Envoie des données vers Firebase Realtime Database (si configuré)
     */
    public function sendToDatabase($path, $data)
    {
        if (!$this->databaseUrl) {
            throw new \Exception('URL de base de données Firebase non configurée');
        }

        try {
            $response = $this->client->put($this->databaseUrl . '/' . trim($path, '/') . '.json', [
                'json' => $data,
                'timeout' => 10,
            ]);

            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            Log::error('Erreur Firebase Database: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Lit des données depuis Firebase Realtime Database
     */
    public function readFromDatabase($path)
    {
        if (!$this->databaseUrl) {
            throw new \Exception('URL de base de données Firebase non configurée');
        }

        try {
            $response = $this->client->get($this->databaseUrl . '/' . trim($path, '/') . '.json', [
                'timeout' => 10,
            ]);

            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            Log::error('Erreur Firebase Database: ' . $e->getMessage());
            throw $e;
        }
    }
}