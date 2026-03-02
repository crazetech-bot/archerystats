<?php

namespace App\Services;

use App\Models\ArcherySession;
use Illuminate\Support\Collection;

class LiveScoringFormatterService
{
    /**
     * Format a full ranked scoreboard from a collection of sessions.
     *
     * Returns ['rows' => [...], 'max_distances' => int].
     * Every row's `distances` array is padded to max_distances length so the
     * table always has a consistent column count.
     *
     * @param  Collection<ArcherySession>  $sessions  Must be loaded with archer.club, roundType, score.ends
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
        unset($row);

        $maxDistances = empty($rows) ? 1 : max(array_map(fn($r) => count($r['distances']), $rows));

        // Pad every row so all have the same column count
        foreach ($rows as &$row) {
            while (count($row['distances']) < $maxDistances) {
                $row['distances'][] = null;
            }
        }
        unset($row);

        return [
            'rows'          => $rows,
            'max_distances' => $maxDistances,
        ];
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

        $arrowsPerEnd = $session->roundType?->arrows_per_end ?? 6;
        $numEnds      = $session->roundType?->num_ends ?? $ends->max('end_number') ?? 0;
        $distances    = $this->distanceTotals($ends, $arrowsPerEnd, $numEnds);

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
            'distances'     => $distances,   // array: [d1, d2, d3, ...] — length = num segments
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
     * Split end totals into 36-arrow chunks.
     *
     * Each chunk represents one "distance" column on the scoreboard.
     * e.g. 72-arrow round (12 ends × 6 arrows) → [ends 1-6, ends 7-12]
     *      144-arrow round (24 ends × 6 arrows) → [ends 1-6, ends 7-12, ends 13-18, ends 19-24]
     *      18m indoor (10 ends × 3 arrows)       → [ends 1-12, ends 13-10...] → one chunk of 30 arrows
     */
    private function distanceTotals(Collection $ends, int $arrowsPerEnd, int $numEnds): array
    {
        if ($arrowsPerEnd <= 0 || $numEnds <= 0) {
            return [$ends->sum('end_total')];
        }

        $endsPerChunk = max(1, (int) round(36 / $arrowsPerEnd));
        $totals       = [];
        $startEnd     = 1;

        while ($startEnd <= $numEnds) {
            $endEnd   = min($startEnd + $endsPerChunk - 1, $numEnds);
            $totals[] = $ends
                ->filter(fn($e) => $e->end_number >= $startEnd && $e->end_number <= $endEnd)
                ->sum('end_total');
            $startEnd = $endEnd + 1;
        }

        return $totals ?: [$ends->sum('end_total')];
    }
}
