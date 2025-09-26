<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Ame;
use App\Models\User;
use App\Models\Message;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class ChatController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Récupérer toutes les conversations de l'utilisateur connecté
     */
    /**
     * Récupérer toutes les conversations de l'utilisateur connecté
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();

            // Si l'utilisateur est une âme, récupérer ses conversations
            if ($user->role === 'ame' || $user->ame_id) {
                return $this->getAmeConversations($user);
            }

            // Pour les utilisateurs normaux (admin, evangeliste, etc.)
            $conversations = $user->conversations()
                ->with(['participants', 'ame'])
                ->withCount(['messages as unread_messages_count' => function ($query) use ($user) {
                    $query->where('is_read', false)
                        ->where('sender_id', '!=', $user->id);
                }])
                ->get()
                ->map(function ($conversation) {
                    // Charger le dernier message manuellement
                    $conversation->dernier_message = $conversation->messages()
                        ->with('sender')
                        ->latest('date_envoi')
                        ->first();
                    return $conversation;
                })
                ->sortByDesc('updated_at')
                ->values();

            return response()->json($conversations);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors du chargement des conversations',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer les conversations pour une âme
     */
    private function getAmeConversations($user)
    {
        // Trouver l'âme associée à cet utilisateur
        $ame = null;
        if ($user->ame_id) {
            $ame = Ame::find($user->ame_id);
        } else {
            // Si l'âme s'est connectée directement (à implémenter selon votre logique)
            $ame = Ame::where('telephone', $user->telephone)
                ->orWhere('email', $user->email)
                ->first();
        }

        if (!$ame) {
            return response()->json([
                'status' => false,
                'message' => 'Âme non trouvée pour cet utilisateur'
            ], 404);
        }

        $conversations = Conversation::where('ame_id', $ame->id)
            ->with(['participants', 'dernierMessage.sender', 'ame'])
            ->withCount(['messages as unread_messages_count' => function ($query) use ($user) {
                $query->where('is_read', false)
                    ->where('sender_id', '!=', $user->id);
            }])
            ->orderBy('updated_at', 'desc')
            ->get();

        return response()->json($conversations);
    }

    /**
     * Créer une nouvelle conversation
     */
    public function store(Request $request)
    {
        try {
            $user = Auth::user();

            // Déterminer l'ID de l'âme
            $ameId = $request->ame_id;

            if (!$ameId) {
                $ame = Ame::first();
                $ameId = $ame?->id;
            }

            if (!$ameId) {
                return response()->json([
                    'status' => false,
                    'message' => 'Aucune âme disponible pour créer la conversation.'
                ], 400);
            }

            // Vérifier si une conversation existe déjà avec cette âme
            $existingConversation = Conversation::where('ame_id', $ameId)
                ->whereHas('participants', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->first();

            if ($existingConversation) {
                return response()->json(
                    $existingConversation->load(['participants', 'dernierMessage.sender', 'ame'])
                );
            }

            // Créer la nouvelle conversation
            $conversation = Conversation::create([
                'titre' => $request->titre ?? "Conversation avec Ame #$ameId",
                'ame_id' => $ameId,
            ]);

            // Ajouter l'utilisateur comme participant
            $conversation->participants()->attach($user->id);

            // Notifier l'âme de la nouvelle conversation
            $ame = Ame::find($ameId);
            if ($ame) {
                $this->notificationService->notifyAme(
                    $ame->id,
                    "Nouvelle conversation créée avec {$user->nom}"
                );
            }

            return response()->json(
                $conversation->load(['participants', 'dernierMessage.sender', 'ame'])
            );
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la création de la conversation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Envoyer un message dans une conversation
     */
    public function sendMessage(Request $request, Conversation $conversation)
    {
        try {
            $user = Auth::user();

            $request->validate([
                'content' => 'required|string|max:2000',
            ]);

            // Vérifier que l'utilisateur peut envoyer un message dans cette conversation
            if (!$this->canUserSendMessage($user, $conversation)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Vous n\'avez pas l\'autorisation d\'envoyer un message dans cette conversation'
                ], 403);
            }

            // Déterminer le type d'expéditeur
            $senderType = $this->getSenderType($user, $conversation);

            // Créer le message
            $message = Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $user->id,
                'contenu' => $request->content,
                'sender_type' => $senderType,
                'date_envoi' => now(),
            ]);

            // Mettre à jour la conversation
            $conversation->update(['updated_at' => now()]);

            // Envoyer les notifications appropriées
            $this->sendMessageNotifications($user, $conversation, $request->content, $senderType);

            return response()->json([
                'status' => true,
                'data' => $message->load('sender'),
                'message' => 'Message envoyé avec succès'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de l\'envoi du message',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Vérifier si un utilisateur peut envoyer un message dans une conversation
     */
    private function canUserSendMessage($user, $conversation)
    {
        // Si l'utilisateur est participant à la conversation
        if ($conversation->participants()->where('user_id', $user->id)->exists()) {
            return true;
        }

        // Si l'utilisateur est l'âme de la conversation
        $ame = Ame::where('id', $conversation->ame_id)->first();
        if ($ame && ($ame->telephone === $user->telephone || $ame->email === $user->email)) {
            return true;
        }

        // Si l'utilisateur est l'encadreur de l'âme
        if ($ame && $ame->assigne_a === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Déterminer le type d'expéditeur
     */
    private function getSenderType($user, $conversation)
    {
        // Si c'est l'âme qui envoie le message
        $ame = Ame::where('id', $conversation->ame_id)->first();
        if ($ame && ($ame->telephone === $user->telephone || $ame->email === $user->email || $user->ame_id === $ame->id)) {
            return 'ame';
        }

        return 'user';
    }

    /**
     * Envoyer les notifications appropriées
     */
    private function sendMessageNotifications($sender, $conversation, $content, $senderType)
    {
        $messagePreview = substr($content, 0, 50) . (strlen($content) > 50 ? '...' : '');

        if ($senderType === 'ame') {
            // L'âme envoie un message, notifier tous les participants
            foreach ($conversation->participants as $participant) {
                if ($participant->id !== $sender->id) {
                    $this->notificationService->notifyUser(
                        $participant->id,
                        "Nouveau message de {$conversation->ame->nom}: $messagePreview"
                    );
                }
            }
        } else {
            // Un utilisateur envoie un message, notifier l'âme
            if ($conversation->ame) {
                $this->notificationService->notifyAme(
                    $conversation->ame->id,
                    "Nouveau message de {$sender->nom}: $messagePreview"
                );
            }
        }
    }

    /**
     * Récupérer les messages d'une conversation
     */
    public function messages(Conversation $conversation)
    {
        try {
            $user = Auth::user();

            // Vérifier que l'utilisateur peut voir cette conversation
            if (!$this->canUserAccessConversation($user, $conversation)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Accès non autorisé à cette conversation'
                ], 403);
            }

            $messages = $conversation->messages()
                ->with('sender')
                ->orderBy('date_envoi', 'asc')
                ->get();

            return response()->json($messages);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors du chargement des messages',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Vérifier si un utilisateur peut accéder à une conversation
     */
    private function canUserAccessConversation($user, $conversation)
    {
        // Si l'utilisateur est participant
        if ($conversation->participants()->where('user_id', $user->id)->exists()) {
            return true;
        }

        // Si c'est l'âme de la conversation
        $ame = Ame::where('id', $conversation->ame_id)->first();
        if ($ame && ($ame->telephone === $user->telephone || $ame->email === $user->email || $user->ame_id === $ame->id)) {
            return true;
        }

        // Si c'est l'encadreur de l'âme
        if ($ame && $ame->assigne_a === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Marquer les messages comme lus
     */
    public function markAsRead(Conversation $conversation)
    {
        try {
            $user = Auth::user();

            // Marquer tous les messages non lus comme lus (sauf ceux envoyés par l'utilisateur actuel)
            $conversation->messages()
                ->where('sender_id', '!=', $user->id)
                ->where('is_read', false)
                ->update(['is_read' => true]);

            return response()->json([
                'status' => true,
                'message' => 'Messages marqués comme lus'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors du marquage des messages',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer une conversation spécifique
     */
    public function show(Conversation $conversation)
    {
        try {
            $user = Auth::user();

            if (!$this->canUserAccessConversation($user, $conversation)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Accès non autorisé à cette conversation'
                ], 403);
            }

            $messages = $conversation->messages()
                ->with('sender')
                ->orderBy('date_envoi', 'asc')
                ->get();

            return response()->json([
                'status' => true,
                'data' => [
                    'conversation' => $conversation->load(['participants', 'ame']),
                    'messages' => $messages,
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors du chargement de la conversation',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
