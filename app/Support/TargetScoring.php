<?php

namespace App\Support;

/**
 * Converts a physical impact point into a scoring result, and describes the
 * geometry of a target face so the same rings can be both drawn and scored.
 *
 * The score is ALWAYS derived from the coordinate — the coordinate is the source of
 * truth, the value is just cached alongside it for fast queries. Keeping this logic in
 * one place means the on-screen target, score entry and analytics can never disagree.
 *
 * Supported (plottable) scoring systems share one of two concentric layouts:
 *   - metric 10-zone face (standard / *_x11 / six_ring / compound / reduced)
 *   - field 6-zone face
 * Non-concentric systems (3d, clout) are not plottable — callers fall back to value entry.
 */
class TargetScoring
{
    /** Hex fills per ring value on a WA metric face. */
    private const METRIC_COLORS = [
        10 => ['#f6c945', '#caa01f'], 9 => ['#f6c945', '#caa01f'],
        8  => ['#e23b3b', '#b91c1c'], 7 => ['#e23b3b', '#b91c1c'],
        6  => ['#1e63c2', '#1e40af'], 5 => ['#1e63c2', '#1e40af'],
        4  => ['#111111', '#000000'], 3 => ['#111111', '#000000'],
        2  => ['#ffffff', '#9ca3af'], 1 => ['#ffffff', '#9ca3af'],
    ];

    /** Hex fills per ring value on a WA field face (approximation for plotting). */
    private const FIELD_COLORS = [
        6 => ['#f6c945', '#caa01f'], 5 => ['#f6c945', '#caa01f'],
        4 => ['#111111', '#000000'], 3 => ['#111111', '#000000'],
        2 => ['#ffffff', '#9ca3af'], 1 => ['#ffffff', '#9ca3af'],
    ];

    /**
     * Systems whose face is a concentric set of rings we can render & click-score.
     */
    public static function isPlottable(string $system): bool
    {
        return in_array($system, [
            'standard', 'standard_x11', 'six_ring', 'six_ring_x11',
            'compound', 'reduced', 'field',
        ], true);
    }

    /**
     * Scoring parameters for a system + face: ring count, band width, the
     * lowest ring that still scores, and the X point value.
     *
     * @return array{kind:string,rings:int,minRing:int,bandMm:float,faceRadiusMm:float,scoringRadiusMm:float,xRadiusMm:float,xValue:int}
     */
    private static function params(string $system, int $faceCm): array
    {
        $faceRadiusMm = $faceCm * 5.0; // (faceCm / 2) * 10 mm

        if ($system === 'field') {
            $band = $faceRadiusMm / 6.0;
            return [
                'kind' => 'field', 'rings' => 6, 'minRing' => 1,
                'bandMm' => $band, 'faceRadiusMm' => $faceRadiusMm,
                'scoringRadiusMm' => 6 * $band, 'xRadiusMm' => $band / 2.0, 'xValue' => 6,
            ];
        }

        $minRing = match ($system) {
            'six_ring', 'six_ring_x11' => 6,
            'compound', 'reduced'      => 5,
            default                    => 1, // standard / standard_x11
        };
        $band   = $faceRadiusMm / 10.0;
        $xValue = in_array($system, ['standard_x11', 'six_ring_x11'], true) ? 11 : 10;

        return [
            'kind' => 'metric', 'rings' => 10, 'minRing' => $minRing,
            'bandMm' => $band, 'faceRadiusMm' => $faceRadiusMm,
            'scoringRadiusMm' => (11 - $minRing) * $band, 'xRadiusMm' => $band / 2.0, 'xValue' => $xValue,
        ];
    }

    /**
     * Render description for a face: ordered rings (outer→inner) with radius+colour,
     * plus the canvas radius (scoring edge + a margin where shots count as a miss).
     *
     * @return array{plottable:bool,kind:string,bandMm:float,scoringRadiusMm:float,xRadiusMm:float,viewRadiusMm:float,rings:array<int,array{value:int,outerMm:float,fill:string,stroke:string}>}
     */
    public static function geometry(string $system, int $faceCm): array
    {
        if (! self::isPlottable($system)) {
            return ['plottable' => false, 'kind' => 'none', 'bandMm' => 0, 'scoringRadiusMm' => 0,
                    'xRadiusMm' => 0, 'viewRadiusMm' => 0, 'rings' => []];
        }

        $p      = self::params($system, $faceCm);
        $isField = $p['kind'] === 'field';
        $top    = $p['rings'];                 // innermost ring value (10 or 6)
        $colors = $isField ? self::FIELD_COLORS : self::METRIC_COLORS;

        $rings = [];
        for ($v = $p['minRing']; $v <= $top; $v++) {
            $outer = ($top + 1 - $v) * $p['bandMm']; // larger radius for lower value
            [$fill, $stroke] = $colors[$v] ?? ['#ffffff', '#9ca3af'];
            $rings[] = ['value' => $v, 'outerMm' => round($outer, 2), 'fill' => $fill, 'stroke' => $stroke];
        }

        return [
            'plottable'       => true,
            'kind'            => $p['kind'],
            'bandMm'          => round($p['bandMm'], 4),
            'scoringRadiusMm' => round($p['scoringRadiusMm'], 2),
            'xRadiusMm'       => round($p['xRadiusMm'], 2),
            'viewRadiusMm'    => round($p['scoringRadiusMm'] * 1.18, 2),
            'rings'           => $rings,
        ];
    }

    /**
     * Resolve a click/impact into a full scoring result for the given system+face.
     *
     * @return array{score:int,is_x:bool,is_miss:bool,value:int,display:string}
     */
    public static function resolve(string $system, ?float $xMm, ?float $yMm, int $faceCm): array
    {
        if ($xMm === null || $yMm === null || ! self::isPlottable($system)) {
            return ['score' => 0, 'is_x' => false, 'is_miss' => true, 'value' => 0, 'display' => 'M'];
        }

        $p = self::params($system, $faceCm);
        $r = sqrt(($xMm ** 2) + ($yMm ** 2));

        $value = $p['rings'] - (int) floor($r / $p['bandMm']);
        $miss  = $value < $p['minRing'];
        $value = max($p['minRing'], min($p['rings'], $value));

        if ($miss) {
            return ['score' => 0, 'is_x' => false, 'is_miss' => true, 'value' => 0, 'display' => 'M'];
        }

        $isX   = $r <= $p['xRadiusMm'];
        $score = $isX ? $p['xValue'] : $value;

        return [
            'score'   => $score,
            'is_x'    => $isX,
            'is_miss' => false,
            'value'   => $value,
            'display' => $isX ? 'X' : (string) $value,
        ];
    }

    /**
     * Ten-zone metric face (WA), kept for the original Arrow::setImpact() contract.
     *
     * @return array{score:int,is_x:bool,is_miss:bool}
     */
    public static function tenZone(float $xMm, float $yMm, int $faceCm): array
    {
        $r = self::resolve('standard', $xMm, $yMm, $faceCm);

        return ['score' => $r['score'], 'is_x' => $r['is_x'], 'is_miss' => $r['is_miss']];
    }
}
