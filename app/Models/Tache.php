<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tache extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre',
        'description',
        'user_id',
        'ame_id',
        'echeance',
        'statut',
        'priorite',
    ];

    protected $casts = [
        'echeance' => 'datetime',
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ame()
    {
        return $this->belongsTo(Ame::class);
    }

    // Scopes utiles
    public function scopePourUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopePourAme($query, $ameId)
    {
        return $query->where('ame_id', $ameId);
    }

    public function scopeStatut($query, $statut)
    {
        return $query->where('statut', $statut);
    }

    public function scopePriorite($query, $priorite)
    {
        return $query->where('priorite', $priorite);
    }
}
