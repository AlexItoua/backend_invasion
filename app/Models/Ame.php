<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ame extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'telephone',
        'device_token',
        'notifications_actives',
        'image',
        'suivi',
        'derniere_interaction',
        'sexe',
        'age',
        'adresse',
        'quartier',
        'ville',
        'date_conversion',
        'campagne_id',
        'type_decision',
        'latitude',
        'longitude',
        'geoloc_accuracy',
        'geoloc_timestamp',
        'assigne_a',
        'cellule_id',
    ];

    protected $casts = [
        'date_conversion' => 'date',
        'age' => 'integer',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'geoloc_accuracy' => 'decimal:2',
        'geoloc_timestamp' => 'datetime',
        'derniere_interaction' => 'date',
        'suivi' => 'boolean',
        'notifications_actives' => 'boolean',
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

    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'destinataire_id');
    }

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

    public function scopeWithinRadius($query, $lat, $lng, $radius = 10)
    {
        return $query->selectRaw("*, (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance", [$lat, $lng, $lat])
            ->having('distance', '<', $radius)
            ->orderBy('distance');
    }

    public function getPositionAttribute()
    {
        if ($this->latitude && $this->longitude) {
            return [
                'latitude' => (float)$this->latitude,
                'longitude' => (float)$this->longitude,
                'accuracy' => (float)$this->geoloc_accuracy,
                'timestamp' => $this->geoloc_timestamp
            ];
        }
        return null;
    }

    public function getEstLocaliseAttribute()
    {
        return !is_null($this->latitude) && !is_null($this->longitude);
    }
}
