<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'nom',
        'email',
        'password',
        'telephone',
        'role',
        'zone_id'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Relations
    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function amesAssignees()
    {
        return $this->hasMany(Ame::class, 'assigne_a');
    }

    public function interactions()
    {
        return $this->hasMany(Interaction::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'destinataire_id');
    }

    public function cellulesResponsable()
    {
        return $this->hasMany(Cellule::class, 'responsable_id');
    }
}
