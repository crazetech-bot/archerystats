<?php

namespace App\Support;

/**
 * Converts a physical impact point into a scoring result.
 *
 * The score is ALWAYS derived from the coordinate — the coordinate is the source of
 * truth, the value is just cached alongside it for fast queries. Keeping this logic in
 * one place means group/heatmap analytics and score entry can never disagree.
 */
class TargetScoring
{
    /**
     * Ten-zone metric face (WA): ten equal-width rings scoring 10..1 from the centre out,
     * with the inner-10 (X) being the inner half of the 10 ring.
     *
     * @param  float  $xMm     horizontal impact, mm from centre
     * @param  float  $yMm     vertical impact, mm from centre
     * @param  int    $faceCm  face diameter in cm (122, 80, 60, 40, ...)
     * @return array{score:int,is_x:bool,is_miss:bool}
     */
    public static function tenZone(float $xMm, float $yMm, int $faceCm): array
    {
        $faceRadiusMm = ($faceCm / 2) * 10.0;   // full scoring radius in mm
        $bandMm       = $faceRadiusMm / 10.0;   // width of one scoring ring
        $r            = sqrt(($xMm ** 2) + ($yMm ** 2));

        if ($r >= $faceRadiusMm) {
            return ['score' => 0, 'is_x' => false, 'is_miss' => true];
        }

        $score = (int) (10 - floor($r / $bandMm));
        $score = max(1, min(10, $score));

        return [
            'score'   => $score,
            'is_x'    => $r <= ($bandMm / 2.0), // inner half of the 10 ring
            'is_miss' => false,
        ];
    }
}
