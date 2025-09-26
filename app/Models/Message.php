<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'sender_id',
        'sender_type', // 'user' ou 'ame'
        'contenu',
        'is_read',
        'date_envoi'
    ];

    protected $casts = [
        'date_envoi' => 'datetime',
        'is_read' => 'boolean',
    ];

    /**
     * Relation avec la conversation
     */
    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * Relation avec l'expéditeur (toujours User dans votre cas)
     * Même si c'est une âme qui envoie, c'est via un User
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Obtenir l'âme expéditrice si sender_type = 'ame'
     */
    public function ameExpeditor()
    {
        if ($this->sender_type === 'ame') {
            return $this->sender->ame ?? null;
        }
        return null;
    }

    /**
     * Obtenir le nom de l'expéditeur selon le type
     */
    public function getSenderNameAttribute()
    {
        if ($this->sender_type === 'ame' && $this->sender->ame) {
            return $this->sender->ame->nom;
        }
        return $this->sender->nom ?? 'Utilisateur inconnu';
    }

    /**
     * Marquer comme lu
     */
    public function markAsRead()
    {
        return $this->update(['is_read' => true]);
    }

    /**
     * Scope pour les messages non lus
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }
}
