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
        'archer_id', 'round_type_id', 'distance_meters', 'target_face_cm',
        'date', 'location', 'weather', 'is_competition', 'competition_name', 'notes',
        'training_session_id', 'assigned_by_coach',
    ];

    protected $casts = [
        'date'               => 'date',
        'is_competition'     => 'boolean',
        'assigned_by_coach'  => 'boolean',
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

    public function trainingSession(): BelongsTo
    {
        return $this->belongsTo(TrainingSession::class);
    }

    /** Effective distance: session override or round type default */
    public function getEffectiveDistanceAttribute(): ?int
    {
        return $this->distance_meters ?? $this->roundType?->distance_meters;
    }

    /** Effective target face: session override or round type default */
    public function getEffectiveFaceAttribute(): ?int
    {
        return $this->target_face_cm ?? $this->roundType?->target_face_cm;
    }
}
