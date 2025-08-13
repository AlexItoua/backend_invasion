<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cellule extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'zone_id', 'responsable_id'];

    protected $with = ['zone', 'responsable'];

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function responsable()
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    public function ames()
    {
        return $this->hasMany(Ame::class);
    }

    // Scopes utiles
    public function scopePourZone($query, $zoneId)
    {
        return $query->where('zone_id', $zoneId);
    }

    public function scopeAvecResponsable($query)
    {
        return $query->whereNotNull('responsable_id');
    }

    public function scopeSansResponsable($query)
    {
        return $query->whereNull('responsable_id');
    }
}
