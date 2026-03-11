<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class ArcherySession extends Model
{
    protected $table = 'archery_sessions';

    protected $fillable = [
        'archer_id', 'round_type_id', 'date', 'location',
        'weather', 'is_competition', 'competition_name', 'notes',
    ];

    protected $casts = [
        'date'           => 'date',
        'is_competition' => 'boolean',
    ];

    public function archer(): BelongsTo
    {
        return $this->belongsTo(Archer::class);
    }

    public function roundType(): BelongsTo
    {
        return $this->belongsTo(RoundType::class);
    }

    public function score(): HasOne
    {
        return $this->hasOne(Score::class, 'archery_session_id');
    }
}
