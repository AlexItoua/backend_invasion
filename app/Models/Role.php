<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nom',
        'description',
        'permissions',
        'est_actif'
    ];

    protected $casts = [
        'permissions' => 'array',
        'est_actif' => 'boolean'
    ];

    protected $with = ['users'];

    // Rôles par défaut
    const ADMIN = 'admin';
    const ENCADREUR = 'encadreur';
    const EVANGELISTE = 'evangeliste';

    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Scopes utiles
    public function scopeActifs($query)
    {
        return $query->where('est_actif', true);
    }

    public function scopeAvecPermission($query, $permission)
    {
        return $query->whereJsonContains('permissions', $permission);
    }

    // Méthodes utilitaires
    public function aPermission($permission)
    {
        return in_array($permission, $this->permissions ?? []);
    }

    public function estAdmin()
    {
        return $this->nom === self::ADMIN;
    }

    public function estEncadreur()
    {
        return $this->nom === self::ENCADREUR;
    }

    public function estEvangeliste()
    {
        return $this->nom === self::EVANGELISTE;
    }
}
