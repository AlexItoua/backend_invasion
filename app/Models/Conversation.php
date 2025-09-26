<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Conversation extends Model
{
    protected $fillable = [
        'titre',
        'ame_id',
        'last_message_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    /**
     * Relation avec l'âme
     */
    public function ame(): BelongsTo
    {
        return $this->belongsTo(Ame::class);
    }

    /**
     * Participants de la conversation
     */
    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'conversation_user')
            ->withTimestamps();
    }

    /**
     * Tous les messages de la conversation
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Dernier message de la conversation
     */
    public function dernierMessage(): HasOne
    {
        return $this->hasOne(Message::class)->latest('date_envoi');
    }

    /**
     * Messages non lus pour un utilisateur
     */
    public function unreadMessagesForUser($userId)
    {
        return $this->messages()
            ->where('sender_id', '!=', $userId)
            ->where('is_read', false);
    }

    /**
     * Compter les messages non lus pour un utilisateur
     */
    public function getUnreadCountForUser($userId)
    {
        return $this->unreadMessagesForUser($userId)->count();
    }

    /**
     * Mettre à jour le timestamp du dernier message
     */
    public function updateLastMessage()
    {
        $this->update([
            'last_message_at' => now(),
            'updated_at' => now()
        ]);
    }
}
