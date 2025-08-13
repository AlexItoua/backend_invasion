<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    protected $fillable = ['nom', 'description'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function campagnes()
    {
        return $this->hasMany(Campagne::class);
    }

    public function cellules()
    {
        return $this->hasMany(Cellule::class);
    }
}
