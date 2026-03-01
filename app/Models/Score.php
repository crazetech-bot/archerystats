<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Score extends Model
{
    protected $fillable = [
        'archery_session_id', 'total_score', 'x_count',
        'gold_count', 'hit_count', 'miss_count',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(ArcherySession::class, 'archery_session_id');
    }

    public function ends(): HasMany
    {
        return $this->hasMany(End::class)->orderBy('end_number');
    }

    public function recalculate(string $scoringSystem = 'standard'): void
    {
        $total     = 0;
        $xCount    = 0;
        $goldCount = 0;
        $hitCount  = 0;
        $missCount = 0;

        foreach ($this->ends as $end) {
            // Per-end scoring system — stored on the end, falls back to session-level
            $sys = $end->scoring_system ?? $scoringSystem;

            $xPoints   = match ($sys) {
                'field'                            => 6,
                'standard_x11', 'six_ring_x11'    => 11,
                default                            => 10,
            };
            $goldValue = match ($sys) {
                'field' => 6,
                '3d'    => 20,
                'clout' => 5,
                default => 10,  // standard / compound / reduced
            };

            foreach ($end->arrow_values as $arrow) {
                if ($arrow === null || $arrow === '') {
                    continue; // unscored arrow
                }

                if ($arrow === 'X') {
                    // X exists only in standard, compound, field
                    $total += $xPoints;
                    $xCount++;
                    $goldCount++;
                    $hitCount++;
                } elseif ($arrow === 'M' || $arrow === 0 || $arrow === '0') {
                    $missCount++;
                } else {
                    $val = (int) $arrow;
                    $total += $val;
                    $hitCount++;
                    if ($val >= $goldValue) {
                        $goldCount++;
                    }
                }
            }
        }

        $this->update([
            'total_score' => $total,
            'x_count'     => $xCount,
            'gold_count'  => $goldCount,
            'hit_count'   => $hitCount,
            'miss_count'  => $missCount,
        ]);
    }
}
