<?php

namespace App\Services;

use Illuminate\Support\Collection;

class ArrowAnalysisService
{
    /**
     * Compute per-arrow-position statistics from a collection of End models.
     *
     * Only ends whose arrow_values count matches $arrowsPerEnd are included,
     * so mixed-format sessions (3-arrow and 6-arrow) don't pollute each other.
     *
     * @param  Collection  $ends
     * @param  int         $arrowsPerEnd
     * @return array{
     *   arrows_per_end: int,
     *   positions: array<int, array{count: int, sum: int, avg?: float}>,
     *   weakest: int,
     *   strongest: int
     * }|null  null when there is no scoreable data
     */
    public function analyse(Collection $ends, int $arrowsPerEnd): ?array
    {
        $relevant = $ends->filter(
            fn($end) => is_array($end->arrow_values)
                     && count($end->arrow_values) === $arrowsPerEnd
        );

        if ($relevant->isEmpty()) {
            return null;
        }

        // Build position-based accumulators (avg is derived later, not stored here)
        $positions = [];
        for ($i = 1; $i <= $arrowsPerEnd; $i++) {
            $positions[$i] = ['count' => 0, 'sum' => 0];
        }

        foreach ($relevant as $end) {
            foreach ($end->arrow_values as $idx => $raw) {
                if ($raw === null) {
                    continue; // not yet scored — skip
                }
                $pos = $idx + 1; // 0-based index → 1-based position
                $val = $this->normalizeScore($raw);
                if ($val !== null) {
                    $positions[$pos]['count']++;
                    $positions[$pos]['sum'] += $val;
                }
            }
        }

        // Compute averages only for positions that have scored data
        foreach ($positions as $pos => &$data) {
            if ($data['count'] > 0) {
                $data['avg'] = round($data['sum'] / $data['count'], 1);
            }
        }
        unset($data);

        // Detect weakest / strongest — keep the 1-based position keys (pluck() would
        // reindex them to 0-based and desync from $positions, breaking lookups).
        $averages = [];
        foreach ($positions as $pos => $data) {
            if (isset($data['avg'])) {
                $averages[$pos] = $data['avg'];
            }
        }

        if (empty($averages)) {
            return null;
        }

        asort($averages); // ascending by average, preserving position keys
        $weakest   = (int) array_key_first($averages);
        $strongest = (int) array_key_last($averages);

        return [
            'arrows_per_end' => $arrowsPerEnd,
            'positions'      => $positions,
            'weakest'        => $weakest,
            'strongest'      => $strongest,
        ];
    }

    /**
     * Coordinate group analysis from the per-arrow impact layer (x_mm / y_mm).
     *
     * Group statistics (barycentre, dispersion, spread) are computed over scoring
     * hits only; misses are still returned as points so the scatter shows them.
     * Coordinates are absolute mm from dead centre, so the barycentre offset reveals
     * aim bias and the sd_x vs sd_y split reveals horizontal vs vertical stringing.
     *
     * @param  Collection  $ends  End models with their `arrows` relation loaded
     * @return array{
     *   n:int, hits:int,
     *   centre:array{x:float,y:float},
     *   mean_radius:float, rms:float, sd_x:float, sd_y:float, extreme_spread:?float,
     *   view_radius:float,
     *   points:array<int,array{x:float,y:float,score:int,is_x:bool,is_miss:bool}>
     * }|null  null when there are no plotted arrows
     */
    public function coordinateGroup(Collection $ends): ?array
    {
        $points = [];
        foreach ($ends as $end) {
            if (! $end->relationLoaded('arrows')) {
                continue;
            }
            foreach ($end->arrows as $arrow) {
                if ($arrow->x_mm === null || $arrow->y_mm === null) {
                    continue;
                }
                $points[] = [
                    'x'       => (float) $arrow->x_mm,
                    'y'       => (float) $arrow->y_mm,
                    'score'   => (int) $arrow->score,
                    'is_x'    => (bool) $arrow->is_x,
                    'is_miss' => (bool) $arrow->is_miss,
                ];
            }
        }

        if (empty($points)) {
            return null;
        }

        $hits = array_values(array_filter($points, fn($p) => ! $p['is_miss']));
        $base = $hits ?: $points; // fall back to all points if every shot was a miss
        $n    = count($base);

        $cx = array_sum(array_column($base, 'x')) / $n;
        $cy = array_sum(array_column($base, 'y')) / $n;

        $sumR = 0.0; $sumR2 = 0.0; $sumDx2 = 0.0; $sumDy2 = 0.0;
        foreach ($base as $p) {
            $dx = $p['x'] - $cx;
            $dy = $p['y'] - $cy;
            $r2 = $dx * $dx + $dy * $dy;
            $sumR   += sqrt($r2);
            $sumR2  += $r2;
            $sumDx2 += $dx * $dx;
            $sumDy2 += $dy * $dy;
        }

        // Extreme spread (widest centre-to-centre pair) — capped to keep it O(n²)-safe
        $extreme = null;
        if ($n >= 2 && $n <= 400) {
            $max = 0.0;
            for ($i = 0; $i < $n; $i++) {
                for ($j = $i + 1; $j < $n; $j++) {
                    $dx = $base[$i]['x'] - $base[$j]['x'];
                    $dy = $base[$i]['y'] - $base[$j]['y'];
                    $d  = sqrt($dx * $dx + $dy * $dy);
                    if ($d > $max) $max = $d;
                }
            }
            $extreme = round($max, 1);
        }

        $maxAbs = 1.0;
        foreach ($points as $p) {
            $maxAbs = max($maxAbs, sqrt($p['x'] * $p['x'] + $p['y'] * $p['y']));
        }

        return [
            'n'              => count($points),
            'hits'           => count($hits),
            'centre'         => ['x' => round($cx, 1), 'y' => round($cy, 1)],
            'mean_radius'    => round($sumR / $n, 1),
            'rms'            => round(sqrt($sumR2 / $n), 1),
            'sd_x'           => round(sqrt($sumDx2 / $n), 1),
            'sd_y'           => round(sqrt($sumDy2 / $n), 1),
            'extreme_spread' => $extreme,
            'view_radius'    => round($maxAbs * 1.12, 1),
            'points'         => $points,
        ];
    }

    private function normalizeScore(mixed $value): ?int
    {
        if ($value === null)                    return null;
        if ($value === 'X' || $value === 'x')   return 10;
        if ($value === 'M' || $value === 'm')   return 0;
        if (is_numeric($value))                 return (int) $value;

        return null;
    }
}
