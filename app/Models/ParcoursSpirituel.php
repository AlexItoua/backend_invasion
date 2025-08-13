<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ParcoursSpirituel extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['nom', 'description', 'ordre', 'est_actif'];

    protected $casts = [
        'est_actif' => 'boolean',
        'ordre' => 'integer',
    ];

    protected $with = ['etapesValidees'];

    public function etapesValidees()
    {
        return $this->hasMany(EtapeValidee::class);
    }

    // Scopes utiles
    public function scopeActifs($query)
    {
        return $query->where('est_actif', true);
    }

    public function scopeOrdonnes($query)
    {
        return $query->orderBy('ordre');
    }

    // Méthodes utilitaires
    public function estCompletPourAme($ameId)
    {
        return $this->etapesValidees()
            ->where('ame_id', $ameId)
            ->count() === $this->etapes()->count();
    }
}
