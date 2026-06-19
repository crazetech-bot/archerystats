<?php

namespace App\Models;

use App\Support\TargetScoring;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Arrow extends Model
{
    protected $fillable = [
        'end_id', 'arrow_number', 'score', 'is_x', 'is_miss', 'x_mm', 'y_mm',
    ];

    protected $casts = [
        'arrow_number' => 'integer',
        'score'        => 'integer',
        'is_x'         => 'boolean',
        'is_miss'      => 'boolean',
        'x_mm'         => 'decimal:2',
        'y_mm'         => 'decimal:2',
    ];

    public function end(): BelongsTo
    {
        return $this->belongsTo(End::class);
    }

    /**
     * Set the impact point and derive score / is_x / is_miss from it.
     * Pass the face size explicitly to avoid a surprise lazy-load of the end relation.
     *
     * $arrow->setImpact(12.5, -40.0, 122);   // 122cm face
     * $arrow->setImpact(null, null, 122);     // explicit miss
     */
    public function setImpact(?float $xMm, ?float $yMm, int $faceCm): static
    {
        if ($xMm === null || $yMm === null) {
            $this->forceFill(['x_mm' => null, 'y_mm' => null, 'score' => 0, 'is_x' => false, 'is_miss' => true]);

            return $this;
        }

        $result = TargetScoring::tenZone($xMm, $yMm, $faceCm);

        $this->forceFill([
            'x_mm'    => $xMm,
            'y_mm'    => $yMm,
            'score'   => $result['score'],
            'is_x'    => $result['is_x'],
            'is_miss' => $result['is_miss'],
        ]);

        return $this;
    }

    /**
     * Like setImpact() but scores against any plottable system (compound 6-ring,
     * field, six_ring, x11 …) rather than only the 10-zone face. Coordinates are
     * stored even for a plotted miss so wayward shots still appear in heatmaps.
     */
    public function setImpactFor(?float $xMm, ?float $yMm, int $faceCm, string $system): static
    {
        if ($xMm === null || $yMm === null) {
            $this->forceFill(['x_mm' => null, 'y_mm' => null, 'score' => 0, 'is_x' => false, 'is_miss' => true]);

            return $this;
        }

        $result = TargetScoring::resolve($system, $xMm, $yMm, $faceCm);

        $this->forceFill([
            'x_mm'    => $xMm,
            'y_mm'    => $yMm,
            'score'   => $result['score'],
            'is_x'    => $result['is_x'],
            'is_miss' => $result['is_miss'],
        ]);

        return $this;
    }

    /** Radial distance from centre in mm — the per-arrow input to group-size stats. */
    public function radiusMm(): ?float
    {
        if ($this->x_mm === null || $this->y_mm === null) {
            return null;
        }

        return round(sqrt(((float) $this->x_mm) ** 2 + ((float) $this->y_mm) ** 2), 2);
    }
}
