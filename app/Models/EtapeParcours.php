<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EtapeParcours extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'etape_parcours';

    protected $fillable = [
        'parcours_spirituel_id',
        'titre',
        'description',
        'contenu',
        'ordre',
        'duree_estimee_minutes',
        'est_actif'
    ];

    protected $casts = [
        'est_actif' => 'boolean',
        'ordre' => 'integer',
        'duree_estimee_minutes' => 'integer',
    ];

    public function parcours(): BelongsTo
    {
        return $this->belongsTo(ParcoursSpirituel::class, 'parcours_spirituel_id');
    }

    public function etapesValidees(): HasMany
    {
        return $this->hasMany(EtapeValidee::class, 'etape_parcours_id');
    }
}
