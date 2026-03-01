<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class End extends Model
{
    protected $table = 'ends';

    protected $fillable = [
        'score_id', 'end_number', 'arrow_values', 'end_total', 'scoring_system',
    ];

    protected $casts = [
        'arrow_values' => 'array',
    ];

    public function score(): BelongsTo
    {
        return $this->belongsTo(Score::class);
    }

    public function calculateTotal(string $scoringSystem = null): int
    {
        $scoringSystem = $scoringSystem ?? $this->scoring_system ?? 'standard';
        $xPoints = match ($scoringSystem) {
            'field'                            => 6,
            'standard_x11', 'six_ring_x11'    => 11,
            default                            => 10,
        };
        $total   = 0;
        foreach ($this->arrow_values as $arrow) {
            if ($arrow === 'X') {
                $total += $xPoints;
            } elseif ($arrow !== null && $arrow !== 'M' && $arrow !== '') {
                $total += (int) $arrow;
            }
        }
        return $total;
    }
}
