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
        'notifications_actives',
        'is_active' // AJOUT
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'notifications_actives' => 'boolean',
        'is_active' => 'boolean', // AJOUT
    ];

    // Relations existantes (garder toutes vos relations actuelles)
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
    public function conversations(): BelongsToMany
    {
        return $this->belongsToMany(Conversation::class, 'conversation_user')
            ->withTimestamps()
            ->orderBy('updated_at', 'desc');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function ame(): BelongsTo
    {
        return $this->belongsTo(Ame::class, 'ame_id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'destinataire_id')
            ->where('destinataire_type', 'user');
    }

    // MÉTHODES POUR L'ACTIVATION/DÉSACTIVATION - AJOUT
    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }

    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    // Méthodes utilitaires existantes (garder tout le reste)
    public function canAccessConversation(Conversation $conversation): bool
    {
        if ($this->conversations()->where('conversation_id', $conversation->id)->exists()) {
            return true;
        }

        if ($this->ame_id && $conversation->ame_id === $this->ame_id) {
            return true;
        }

        if ($conversation->ame && $conversation->ame->assigne_a === $this->id) {
            return true;
        }

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

    public function isAme(): bool
    {
        return $this->role === 'ame' || !is_null($this->ame_id);
    }

    public function getSenderType(): string
    {
        return $this->isAme() ? 'ame' : 'user';
    }

    public function getDisplayName(): string
    {
        if ($this->isAme() && $this->ame) {
            return $this->ame->nom;
        }
        return $this->nom;
    }

    public function canSendMessageTo(Conversation $conversation): bool
    {
        return $this->canAccessConversation($conversation);
    }

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