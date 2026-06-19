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

    /**
     * Per-end layout: the face size, scoring system and distance that apply to each
     * end (1-based). Multi-distance rounds vary these by `distance_segments`; single
     * rounds fall back to the effective face / round-type defaults. Reused by score
     * entry and coordinate scoring so the on-screen target always matches the end.
     *
     * @return array<int,array{face:int,system:string,distance:?int}>
     */
    public function endLayout(): array
    {
        $rt       = $this->roundType;
        $defFace  = (int) ($this->effective_face ?? $rt?->target_face_cm ?? 122);
        $defSys   = $rt?->scoring_system ?? 'standard';
        $defDist  = $this->effective_distance ?? $rt?->distance_meters;

        $layout = [];
        if ($rt && ($segments = $rt->distance_segments)) {
            $endNum = 1;
            foreach ($segments as $seg) {
                $n = (int) ($seg['num_ends'] ?? 6);
                for ($i = 0; $i < $n; $i++) {
                    $layout[$endNum++] = [
                        'face'     => (int) ($seg['face'] ?? $defFace),
                        'system'   => $seg['scoring'] ?? $defSys,
                        'distance' => isset($seg['distance']) ? (int) $seg['distance'] : $defDist,
                    ];
                }
            }
        }

        $totalEnds = (int) ($rt?->num_ends ?? 0);
        for ($e = 1; $e <= $totalEnds; $e++) {
            $layout[$e] ??= ['face' => $defFace, 'system' => $defSys, 'distance' => $defDist];
        }

        ksort($layout);

        return $layout;
    }
}
