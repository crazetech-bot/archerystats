<?php

namespace App\Services;

use App\Models\ArcherySession;
use Illuminate\Support\Collection;

class LiveScoringFormatterService
{
    /**
     * Format a full ranked scoreboard from a collection of sessions.
     *
     * @param  Collection<ArcherySession>  $sessions  Must be loaded with archer.club, roundType, score.ends
     * @return array<int, array>
     */
    public function formatScoreboard(Collection $sessions): array
    {
        $rows = $sessions->map(fn($s) => $this->formatRow($s))->values()->toArray();

        usort($rows, fn($a, $b) =>
            $b['total'] <=> $a['total'] ?: $b['x_count'] <=> $a['x_count'] ?: $b['tens_plus_x'] <=> $a['tens_plus_x']
        );

        foreach ($rows as $i => &$row) {
            $row['position'] = $i + 1;
        }

        return $rows;
    }

    /**
     * Format a single session row — used both in formatScoreboard() and the broadcast payload.
     */
    public function formatRow(ArcherySession $session): array
    {
        $score    = $session->score;
        $ends     = $score?->ends ?? collect();
        $segments = $session->roundType?->distance_segments ?? [];
        $archer   = $session->archer;

        [$d1, $d2] = $this->distanceTotals($ends, $segments);

        $totalArrows = ($score?->hit_count ?? 0) + ($score?->miss_count ?? 0);
        $avgPerArrow = $totalArrows > 0
            ? round(($score?->total_score ?? 0) / $totalArrows, 2)
            : 0;

        $lastEnd     = $ends->sortByDesc('end_number')->first();
        $currentEnd  = $lastEnd?->end_number ?? 0;
        $arrowValues = $lastEnd?->arrow_values ?? [];

        return [
            'session_id'    => $session->id,
            'archer_id'     => $archer->id,
            'name'          => $archer->full_name,
            'club'          => $archer->club?->name ?? '—',
            'club_id'       => $archer->club_id,
            'state'         => $archer->stateTeam?->state ?? $archer->state ?? '—',
            'state_team_id' => $archer->state_team_id,
            'national_team' => $archer->national_team ?? 'No',
            'distance_1'    => $d1,
            'distance_2'    => $d2,
            'total'         => $score?->total_score ?? 0,
            'tens_plus_x'   => $score?->gold_count ?? 0,
            'x_count'       => $score?->x_count ?? 0,
            'avg_per_arrow' => $avgPerArrow,
            'current_end'   => $currentEnd,
            'arrow_values'  => $arrowValues,
            'position'      => 0, // assigned by formatScoreboard()
        ];
    }

    /**
     * Split end totals across distance segments.
     *
     * Returns [distance_1_total, distance_2_total|null].
     * - 0 or 1 segments  → all ends counted as D1, D2 is null
     * - 2+ segments      → D1 = segment 1 ends, D2 = segment 2 ends
     */
    private function distanceTotals(Collection $ends, array $segments): array
    {
        if (count($segments) < 2) {
            return [$ends->sum('end_total'), null];
        }

        $seg1Ends = (int) ($segments[0]['num_ends'] ?? 6);

        $d1 = $ends->filter(fn($e) => $e->end_number <= $seg1Ends)->sum('end_total');
        $d2 = $ends->filter(fn($e) => $e->end_number > $seg1Ends)->sum('end_total');

        return [$d1, $d2];
    }
}
