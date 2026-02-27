<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArcherAchievement extends Model
{
    protected $fillable = [
        'archer_id',
        'date',
        'achievement',
        'team',
        'tournament',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function archer(): BelongsTo
    {
        return $this->belongsTo(Archer::class);
    }
}
