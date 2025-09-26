<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Conversation;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function store(Request $request, NotificationService $notifier)
    {
        $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'contenu' => 'required|string',
        ]);

        $message = Message::create([
            'conversation_id' => $request->conversation_id,
            'sender_id' => auth()->id(),
            'contenu' => $request->contenu,
            'date_envoi' => now(),
        ]);

        $conversation = Conversation::with('ame')->find($request->conversation_id);

        if ($conversation->ame) {
            $notifier->notifyAme(
                $conversation->ame->id,
                "Vous avez reçu un nouveau message: " . substr($request->contenu, 0, 50) . "..."
            );
        }

        return response()->json($message, 201);
    }
}
