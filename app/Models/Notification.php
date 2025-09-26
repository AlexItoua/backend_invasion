<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'destinataire_id',
        'destinataire_type', // 'user' ou 'ame'
        'message',
        'type',
        'lu',
        'date_notification'
    ];

    protected $casts = [
        'lu' => 'boolean',
        'date_notification' => 'datetime',
    ];

    /**
     * Relation avec l'âme (quand destinataire_type = 'ame')
     */
    public function ame()
    {
        return $this->belongsTo(Ame::class, 'destinataire_id')
            ->where('destinataire_type', 'ame');
    }

    /**
     * Relation avec l'utilisateur (quand destinataire_type = 'user')
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'destinataire_id')
            ->where('destinataire_type', 'user');
    }

    /**
     * Obtenir le destinataire selon le type
     */
    public function getDestinataireAttribute()
    {
        if ($this->destinataire_type === 'ame') {
            return $this->ame;
        }
        return $this->user;
    }

    /**
     * Marquer comme lu
     */
    public function markAsRead()
    {
        return $this->update(['lu' => true]);
    }

    /**
     * Scope pour les notifications non lues
     */
    public function scopeUnread($query)
    {
        return $query->where('lu', false);
    }

    /**
     * Scope pour un type spécifique
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope pour un destinataire spécifique
     */
    public function scopeForRecipient($query, $recipientId, $recipientType = 'user')
    {
        return $query->where('destinataire_id', $recipientId)
            ->where('destinataire_type', $recipientType);
    }
}
