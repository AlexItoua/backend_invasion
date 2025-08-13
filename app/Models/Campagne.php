<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campagne extends Model
{
    protected $fillable = ['nom', 'date_debut', 'date_fin', 'zone_id', 'description'];

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function ames()
    {
        return $this->hasMany(Ame::class);
    }

    public function statistiques()
    {
        return $this->hasMany(Statistique::class);
    }
    protected $casts = [
    'date_debut' => 'date',
    'date_fin' => 'date',
];
}
