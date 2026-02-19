<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RoundType extends Model
{
    protected $fillable = [
        'name', 'category', 'distance_meters', 'num_ends',
        'arrows_per_end', 'max_score_per_arrow', 'description', 'active',
    ];

    protected $casts = [
        'active' => 'boolean',
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
