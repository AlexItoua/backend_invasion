<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ParcoursAmes extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'parcours_ames';

    protected $fillable = [
        'ame_id',
        'parcours_spirituel_id',
        'date_debut',
        'date_fin',
        'statut',
        'notes'
    ];

    protected $casts = [
        'date_debut' => 'datetime',
        'date_fin' => 'datetime',
    ];

    public function ame(): BelongsTo
    {
        return $this->belongsTo(Ame::class);
    }

    public function parcours(): BelongsTo
    {
        return $this->belongsTo(ParcoursSpirituel::class, 'parcours_spirituel_id');
    }

    public function etapesValidees(): HasMany
    {
        return $this->hasMany(EtapeValidee::class, 'parcours_ame_id');
    }

    public function terminer(): void
    {
        $this->update([
            'statut' => 'termine',
            'date_fin' => now()
        ]);
    }

    public function abandonner(): void
    {
        $this->update([
            'statut' => 'abandonne',
            'date_fin' => now()
        ]);
    }
}
