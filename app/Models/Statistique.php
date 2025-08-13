<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Statistique extends Model
{
    use HasFactory;

    protected $fillable = [
        'campagne_id',
        'total_ames',
        'baptises',
        'fidelises',
        'nouvelles_ames',
        'date_generation'
    ];

    protected $casts = [
        'date_generation' => 'date',
        'total_ames' => 'integer',
        'baptises' => 'integer',
        'fidelises' => 'integer',
        'nouvelles_ames' => 'integer',
    ];

    protected $appends = ['taux_bapteme', 'taux_fidelisation', 'taux_nouvelles_ames'];

    protected $with = ['campagne'];

    public function campagne()
    {
        return $this->belongsTo(Campagne::class);
    }

    // Scopes utiles
    public function scopePourCampagne($query, $campagneId)
    {
        return $query->where('campagne_id', $campagneId);
    }

    public function scopeEntreDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('date_generation', [$startDate, $endDate]);
    }

    public function scopeRecentFirst($query)
    {
        return $query->orderBy('date_generation', 'desc');
    }

    // Accesseurs calculés
    public function getTauxBaptemeAttribute()
    {
        if ($this->total_ames === 0) return 0;
        return round(($this->baptises / $this->total_ames) * 100, 2);
    }

    public function getTauxFidelisationAttribute()
    {
        if ($this->total_ames === 0) return 0;
        return round(($this->fidelises / $this->total_ames) * 100, 2);
    }

    public function getTauxNouvellesAmesAttribute()
    {
        if ($this->total_ames === 0) return 0;
        return round(($this->nouvelles_ames / $this->total_ames) * 100, 2);
    }

    // Méthodes utilitaires
    public function estValide()
    {
        return $this->baptises <= $this->total_ames
            && $this->fidelises <= $this->total_ames
            && $this->nouvelles_ames <= $this->total_ames
            && $this->baptises >= 0
            && $this->fidelises >= 0
            && $this->nouvelles_ames >= 0;
    }

    public function genererRapport()
    {
        return [
            'total_ames' => $this->total_ames,
            'baptises' => $this->baptises,
            'fidelises' => $this->fidelises,
            'nouvelles_ames' => $this->nouvelles_ames,
            'taux_bapteme' => $this->taux_bapteme,
            'taux_fidelisation' => $this->taux_fidelisation,
            'taux_nouvelles_ames' => $this->taux_nouvelles_ames,
        ];
    }
}
