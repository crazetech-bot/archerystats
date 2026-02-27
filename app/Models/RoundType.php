<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RoundType extends Model
{
    protected $fillable = [
        'name', 'category', 'discipline', 'distance_meters', 'target_face_cm',
        'distance_segments', 'scoring_system', 'num_ends', 'arrows_per_end',
        'max_score_per_arrow', 'description', 'active',
    ];

    protected $casts = [
        'active'             => 'boolean',
        'distance_segments'  => 'array',
    ];

    public function sessions(): HasMany
    {
        return $this->hasMany(ArcherySession::class);
    }

    public function getMaxScoreAttribute(): int
    {
        return $this->num_ends * $this->arrows_per_end * $this->max_score_per_arrow;
    }
}
