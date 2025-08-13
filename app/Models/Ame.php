<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ame extends Model
{
    use HasFactory;

    // Dans app/Models/Ame.php
protected $fillable = [
    'nom',
    'telephone',
    'sexe',
    'age',
    'adresse',
    'date_conversion',
    'campagne_id',
    'type_decision',
    'latitude',
    'longitude',
    'assigne_a',
    'cellule_id',
    'image',
    'suivi', // 👈 ajouté
    'derniere_interaction', // 👈 ajouté
];


    protected $casts = [
        'date_conversion' => 'date',
        'age' => 'integer',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'derniere_interaction' => 'date',
'suivi' => 'boolean',

    ];

    protected $with = ['campagne', 'encadreur', 'cellule'];

    public function campagne()
    {
        return $this->belongsTo(Campagne::class);
    }

    public function encadreur()
    {
        return $this->belongsTo(User::class, 'assigne_a');
    }
    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }


    public function cellule()
    {
        return $this->belongsTo(Cellule::class);
    }

    public function interactions()
    {
        return $this->hasMany(Interaction::class);
    }

    public function etapesValidees()
    {
        return $this->hasMany(EtapeValidee::class);
    }

    // Scopes utiles
    public function scopePourCampagne($query, $campagneId)
    {
        return $query->where('campagne_id', $campagneId);
    }

    public function scopePourEncadreur($query, $userId)
    {
        return $query->where('assigne_a', $userId);
    }

    public function scopeAvecPosition($query)
    {
        return $query->whereNotNull('latitude')->whereNotNull('longitude');
    }

    // Accesseurs
    public function getPositionAttribute()
    {
        if ($this->latitude && $this->longitude) {
            return [
                'latitude' => (float)$this->latitude,
                'longitude' => (float)$this->longitude
            ];
        }
        return null;
    }

    public function getEstLocaliseAttribute()
    {
        return !is_null($this->latitude) && !is_null($this->longitude);
    }
}
