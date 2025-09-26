<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'nom',
        'email',
        'password',
        'telephone',
        'role',
        'zone_id',
        'ame_id',
        'device_token',
        'notifications_actives'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'notifications_actives' => 'boolean',
    ];

    // Relations existantes
    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    public function amesAssignees(): HasMany
    {
        return $this->hasMany(Ame::class, 'assigne_a');
    }

    public function interactions(): HasMany
    {
        return $this->hasMany(Interaction::class);
    }

    public function cellulesResponsable(): HasMany
    {
        return $this->hasMany(Cellule::class, 'responsable_id');
    }

    // Relations pour le chat

    /**
     * Conversations où l'utilisateur est participant
     */
    public function conversations(): BelongsToMany
    {
        return $this->belongsToMany(Conversation::class, 'conversation_user')
            ->withTimestamps()
            ->orderBy('updated_at', 'desc');
    }

    /**
     * Messages envoyés par cet utilisateur
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * Âme associée à cet utilisateur (si l'utilisateur représente une âme)
     */
    public function ame(): BelongsTo
    {
        return $this->belongsTo(Ame::class, 'ame_id');
    }

    /**
     * Notifications reçues par l'utilisateur
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'destinataire_id')
            ->where('destinataire_type', 'user');
    }

    // Méthodes utilitaires pour le chat

    /**
     * Vérifier si l'utilisateur peut accéder à une conversation
     */
    public function canAccessConversation(Conversation $conversation): bool
    {
        // Si l'utilisateur est participant
        if ($this->conversations()->where('conversation_id', $conversation->id)->exists()) {
            return true;
        }

        // Si l'utilisateur est l'âme de la conversation (via ame_id)
        if ($this->ame_id && $conversation->ame_id === $this->ame_id) {
            return true;
        }

        // Si l'utilisateur est l'encadreur de l'âme
        if ($conversation->ame && $conversation->ame->assigne_a === $this->id) {
            return true;
        }

        // Vérification par email/téléphone (pour compatibilité)
        if ($conversation->ame) {
            $ame = $conversation->ame;
            if (($ame->telephone && $ame->telephone === $this->telephone) ||
                ($ame->email && $ame->email === $this->email)
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Vérifier si l'utilisateur représente une âme
     */
    public function isAme(): bool
    {
        return $this->role === 'ame' || !is_null($this->ame_id);
    }

    /**
     * Obtenir le type d'expéditeur pour les messages
     */
    public function getSenderType(): string
    {
        return $this->isAme() ? 'ame' : 'user';
    }

    /**
     * Obtenir le nom d'affichage selon le contexte
     */
    public function getDisplayName(): string
    {
        if ($this->isAme() && $this->ame) {
            return $this->ame->nom;
        }
        return $this->nom;
    }

    /**
     * Vérifier si l'utilisateur peut envoyer un message dans une conversation
     */
    public function canSendMessageTo(Conversation $conversation): bool
    {
        return $this->canAccessConversation($conversation);
    }

    /**
     * Obtenir le nombre de messages non lus
     */
    public function getUnreadMessagesCount(): int
    {
        return Message::whereHas('conversation', function ($query) {
            $query->whereHas('participants', function ($q) {
                $q->where('user_id', $this->id);
            });
        })
            ->where('sender_id', '!=', $this->id)
            ->where('is_read', false)
            ->count();
    }
}
