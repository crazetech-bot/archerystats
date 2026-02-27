<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrainingSession extends Model
{
    protected $fillable = [
        'coach_id', 'date', 'location', 'focus_area', 'duration_minutes', 'notes',
        'round_type_id', 'distance_meters', 'target_face_cm',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function coach(): BelongsTo
    {
        return $this->belongsTo(Coach::class);
    }

    public function roundType(): BelongsTo
    {
        return $this->belongsTo(RoundType::class);
    }

    public function archers(): BelongsToMany
    {
        return $this->belongsToMany(Archer::class, 'training_session_archer')
                    ->withPivot('attended');
    }

    public function assignedSessions(): HasMany
    {
        return $this->hasMany(ArcherySession::class);
    }

    public function eliminationMatches(): HasMany
    {
        return $this->hasMany(EliminationMatch::class);
    }

    public function getDurationLabelAttribute(): string
    {
        if (!$this->duration_minutes) return '—';
        $h = intdiv($this->duration_minutes, 60);
        $m = $this->duration_minutes % 60;
        return $h > 0 ? "{$h}h " . ($m > 0 ? "{$m}m" : '') : "{$m}m";
    }
}
