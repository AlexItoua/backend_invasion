<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Interaction extends Model
{
    use HasFactory;

    protected $fillable = ['ame_id', 'user_id', 'type', 'note', 'date_interaction'];

    protected $casts = [
        'date_interaction' => 'date',
    ];

    protected $with = ['ame', 'user'];

    public function ame()
    {
        return $this->belongsTo(Ame::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes utiles
    public function scopePourAme($query, $ameId)
    {
        return $query->where('ame_id', $ameId);
    }

    public function scopePourUtilisateur($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeParType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeEntreDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('date_interaction', [$startDate, $endDate]);
    }
}
