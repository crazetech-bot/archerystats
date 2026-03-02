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

        // Detect weakest / strongest — filter() drops positions with no avg key (null)
        $averages = collect($positions)->pluck('avg');

        if ($averages->filter()->isEmpty()) {
            return null;
        }

        $weakest   = (int) $averages->filter()->keys()->sortBy(fn($k)     => $averages[$k])->first();
        $strongest = (int) $averages->filter()->keys()->sortByDesc(fn($k)  => $averages[$k])->first();

        return [
            'arrows_per_end' => $arrowsPerEnd,
            'positions'      => $positions,
            'weakest'        => $weakest,
            'strongest'      => $strongest,
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
