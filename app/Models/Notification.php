<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre',
        'message',
        'type',
        'destinataire_id',
        'statut',
        'date_envoi',
        'metadata'
    ];

    protected $casts = [
        'date_envoi' => 'datetime',
        'metadata' => 'array',
    ];

    protected $with = ['destinataire'];

    public function destinataire()
    {
        return $this->belongsTo(User::class, 'destinataire_id');
    }

    // Scopes utiles
    public function scopePourDestinataire($query, $userId)
    {
        return $query->where('destinataire_id', $userId);
    }

    public function scopeNonLues($query)
    {
        return $query->where('statut', '!=', 'lue');
    }

    public function scopeAEnvoyer($query)
    {
        return $query->where('statut', 'en_attente')
                    ->where('date_envoi', '<=', now());
    }

    public function scopeParType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Marquer comme lue
    public function marquerCommeLue()
    {
        $this->update(['statut' => 'lue']);
        return $this;
    }
}
