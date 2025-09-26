<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ParcoursSpirituel extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['nom', 'description', 'ordre', 'est_actif'];

    protected $casts = [
        'est_actif' => 'boolean',
        'ordre' => 'integer',
    ];

    // Chargement eager des étapes
    protected $with = ['etapes'];

    public function etapes(): HasMany
    {
        return $this->hasMany(EtapeParcours::class)->orderBy('ordre');
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
    public function estCompletPourAme($ameId): bool
    {
        $parcoursAme = $this->parcoursAmes()->where('ame_id', $ameId)->first();

        if (!$parcoursAme) {
            return false;
        }

        $etapesValidees = $parcoursAme->etapesValidees()->count();
        $totalEtapes = $this->etapes()->count();

        return $etapesValidees === $totalEtapes;
    }


    // Dans app/Models/ParcoursSpirituel.php

    public function parcoursAmes()
    {
        return $this->hasMany(ParcoursAmes::class);
    }

    public function progressionPourAme($ameId)
    {
        $parcoursAme = $this->parcoursAmes()->where('ame_id', $ameId)->first();

        if (!$parcoursAme) {
            return [
                'progression' => 0,
                'etapes_validees' => 0,
                'etapes_total' => $this->etapes()->count(),
                'statut' => 'non_commence'
            ];
        }

        $etapesValidees = $parcoursAme->etapesValidees()->count();
        $totalEtapes = $this->etapes()->count();
        $progression = $totalEtapes > 0 ? ($etapesValidees / $totalEtapes) * 100 : 0;

        return [
            'progression' => round($progression, 2),
            'etapes_validees' => $etapesValidees,
            'etapes_total' => $totalEtapes,
            'statut' => $parcoursAme->statut,
            'date_debut' => $parcoursAme->date_debut,
            'date_fin' => $parcoursAme->date_fin,
        ];
    }
}