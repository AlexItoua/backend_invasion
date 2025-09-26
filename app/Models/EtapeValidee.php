<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EtapeValidee extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'etape_validees'; // Spécifier explicitement le nom de la table
    protected $fillable = [
        'parcours_ame_id',
        'etape_parcours_id',
        'date_validation',
        'notes'
    ];

    protected $casts = [
        'date_validation' => 'datetime',
    ];

    public function parcoursAme(): BelongsTo
    {
        return $this->belongsTo(ParcoursAmes::class);
    }

    public function etape(): BelongsTo
    {
        return $this->belongsTo(EtapeParcours::class, 'etape_parcours_id');
    }
}
