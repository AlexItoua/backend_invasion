<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Ame;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Notifier une âme
     */
    public function notifyAme($ameId, $message, $type = 'message')
    {
        try {
            $ame = Ame::find($ameId);

            if (!$ame) {
                throw new Exception("Âme introuvable avec l'ID: $ameId");
            }

            // Créer la notification en base
            $notification = Notification::create([
                'destinataire_id' => $ameId,
                'destinataire_type' => 'ame',
                'message' => $message,
                'type' => $type,
                'lu' => false,
            ]);

            // Envoyer notification push si l'âme a un device_token
            if ($ame->device_token && $ame->notifications_actives) {
                $this->sendPushNotification(
                    $ame->device_token,
                    'Nouveau message',
                    $message
                );
            }

            // Si l'âme a un encadreur, le notifier aussi
            if ($ame->assigne_a) {
                $this->notifyUser($ame->assigne_a, "Âme {$ame->nom}: $message", 'ame_activity');
            }

            return $notification;
        } catch (Exception $e) {
            Log::error("Erreur notification âme: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Notifier un utilisateur
     */
    public function notifyUser($userId, $message, $type = 'message')
    {
        try {
            $user = User::find($userId);

            if (!$user) {
                throw new Exception("Utilisateur introuvable avec l'ID: $userId");
            }

            // Créer la notification en base
            $notification = Notification::create([
                'destinataire_id' => $userId,
                'destinataire_type' => 'user',
                'message' => $message,
                'type' => $type,
                'lu' => false,
            ]);

            // Envoyer notification push si l'utilisateur a un device_token
            if (isset($user->device_token) && $user->device_token) {
                $this->sendPushNotification(
                    $user->device_token,
                    'Nouveau message',
                    $message
                );
            }

            return $notification;
        } catch (Exception $e) {
            Log::error("Erreur notification utilisateur: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Envoyer une notification push
     */
    private function sendPushNotification($deviceToken, $title, $body)
    {
        try {
            // Configuration Firebase Cloud Messaging (FCM)
            $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
            $serverKey = config('services.fcm.server_key');

            if (!$serverKey) {
                Log::warning('Clé serveur FCM non configurée');
                return false;
            }

            $data = [
                'to' => $deviceToken,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                    'sound' => 'default',
                    'badge' => 1,
                ],
                'data' => [
                    'type' => 'chat_message',
                    'timestamp' => now()->toISOString(),
                ]
            ];

            $headers = [
                'Authorization: key=' . $serverKey,
                'Content-Type: application/json',
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $fcmUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

            $response = curl_exec($ch);
            curl_close($ch);

            Log::info('Notification push envoyée', [
                'token' => substr($deviceToken, 0, 20) . '...',
                'response' => $response
            ]);

            return true;
        } catch (Exception $e) {
            Log::error('Erreur notification push: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Marquer les notifications comme lues
     */
    public function markAsRead($notificationIds, $userId = null, $userType = 'user')
    {
        try {
            $query = Notification::whereIn('id', $notificationIds);

            if ($userId) {
                $query->where('destinataire_id', $userId)
                    ->where('destinataire_type', $userType);
            }

            $updated = $query->update(['lu' => true]);

            return $updated;
        } catch (Exception $e) {
            Log::error('Erreur marquage notifications: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtenir le nombre de notifications non lues
     */
    public function getUnreadCount($userId, $userType = 'user')
    {
        try {
            return Notification::where('destinataire_id', $userId)
                ->where('destinataire_type', $userType)
                ->where('lu', false)
                ->count();
        } catch (Exception $e) {
            Log::error('Erreur comptage notifications: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Obtenir les notifications d'un utilisateur
     */
    public function getUserNotifications($userId, $userType = 'user', $limit = 50)
    {
        try {
            return Notification::where('destinataire_id', $userId)
                ->where('destinataire_type', $userType)
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        } catch (Exception $e) {
            Log::error('Erreur récupération notifications: ' . $e->getMessage());
            return collect();
        }
    }
}
