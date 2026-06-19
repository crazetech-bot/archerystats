<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    /**
     * Optional per-arrow coordinate layer. Only populated when impact points are
     * captured; plain value entry still lives in the `arrow_values` JSON column.
     */
    public function arrows(): HasMany
    {
        return $this->hasMany(Arrow::class)->orderBy('arrow_number');
    }

    /**
     * Group centre (barycentre) in mm from dead centre — the accuracy/aim-bias signal,
     * computed from captured coordinates. Returns null axes when no coordinates exist.
     *
     * @return array{x: float|null, y: float|null}
     */
    public function groupCentreMm(): array
    {
        $q = $this->arrows()->whereNotNull('x_mm')->whereNotNull('y_mm');
        $x = $q->avg('x_mm');
        $y = $q->avg('y_mm');

        return [
            'x' => $x !== null ? round((float) $x, 2) : null,
            'y' => $y !== null ? round((float) $y, 2) : null,
        ];
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
