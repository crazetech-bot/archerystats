<?php

namespace App\Http\Controllers;

use App\Models\Archer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ArcherPerformanceController extends Controller
{
    public function show(Archer $archer, Request $request): View
    {
        // Authorization
        $user = auth()->user();
        if ($user->role === 'archer' && $user->archer?->id !== $archer->id) {
            abort(403, 'You can only view your own performance.');
        }
        if ($user->role === 'coach') {
            $isAssigned = $user->coach?->archers()->where('archers.id', $archer->id)->exists();
            if (! $isAssigned) {
                abort(403, 'You can only view performance for your assigned archers.');
            }
        }

        $archer->load('user', 'club');

        // Resolve date range
        $range = $request->input('range', 'last30days');
        [$from, $to] = $this->resolveDateRange($range, $request, $archer);

        // Load sessions in range that have a recorded score
        $sessions = $archer->sessions()
            ->with(['roundType', 'score.ends'])
            ->whereHas('score')
            ->whereBetween('date', [$from, $to])
            ->orderBy('date')
            ->get();

        // Summary stats
        $totalSessions = $sessions->count();
        $bestScore     = $sessions->max(fn($s) => $s->score?->total_score ?? 0) ?: 0;
        $avgScore      = $totalSessions > 0
            ? round($sessions->avg(fn($s) => $s->score?->total_score ?? 0), 1)
            : 0;
        $totalArrows    = $sessions->sum(fn($s) => ($s->score?->hit_count ?? 0) + ($s->score?->miss_count ?? 0));
        $totalScore     = $sessions->sum(fn($s) => $s->score?->total_score ?? 0);
        $avgPerArrow    = $totalArrows > 0 ? round($totalScore / $totalArrows, 2) : 0;

        // Chart 1: Score trend
        $trendLabels = $sessions->map(fn($s) => $s->date->format('d M Y'))->values()->toArray();
        $trendData   = $sessions->map(fn($s) => $s->score?->total_score ?? 0)->values()->toArray();
        $trendMeta   = $sessions->map(fn($s) => [
            'round'       => $s->roundType->name,
            'competition' => (bool) $s->is_competition,
        ])->values()->toArray();

        // Chart 2: Arrow zone distribution (WA target face zones) by session date
        $zoneDatasets = ['gold' => [], 'red' => [], 'blue' => [], 'black' => [], 'white' => []];
        foreach ($sessions as $s) {
            $sys    = $s->roundType->scoring_system ?? 'standard';
            $counts = ['gold' => 0, 'red' => 0, 'blue' => 0, 'black' => 0, 'white' => 0];
            foreach ($s->score->ends as $end) {
                foreach ($end->arrow_values as $arrow) {
                    $zone = $this->arrowZone($arrow, $sys);
                    if ($zone) $counts[$zone]++;
                }
            }
            foreach ($counts as $z => $cnt) {
                $zoneDatasets[$z][] = $cnt;
            }
        }

        // Chart 3: Competition vs training
        $competition     = $sessions->where('is_competition', true);
        $training        = $sessions->where('is_competition', false);
        $compVsTrainData = [
            'competition' => [
                'count' => $competition->count(),
                'avg'   => $competition->count() > 0
                    ? round($competition->avg(fn($s) => $s->score?->total_score ?? 0), 1) : 0,
                'best'  => $competition->max(fn($s) => $s->score?->total_score ?? 0) ?: 0,
            ],
            'training' => [
                'count' => $training->count(),
                'avg'   => $training->count() > 0
                    ? round($training->avg(fn($s) => $s->score?->total_score ?? 0), 1) : 0,
                'best'  => $training->max(fn($s) => $s->score?->total_score ?? 0) ?: 0,
            ],
        ];

        // Sessions table (most recent first)
        $sessionsTable = $sessions->sortByDesc('date')->values();

        return view('archers.performance', compact(
            'archer',
            'range', 'from', 'to',
            'totalSessions', 'bestScore', 'avgScore', 'avgPerArrow',
            'trendLabels', 'trendData', 'trendMeta',
            'zoneDatasets',
            'compVsTrainData',
            'sessionsTable',
        ));
    }

    private function resolveDateRange(string $range, Request $request, Archer $archer): array
    {
        $today = Carbon::today();

        switch ($range) {
            case 'current_session':
                $latestDate = $archer->sessions()->max('date');
                $d = $latestDate ? Carbon::parse($latestDate)->toDateString() : $today->toDateString();
                return [$d, $d];

            case 'last_session':
                $dates = $archer->sessions()->orderByDesc('date')->pluck('date')
                    ->map(fn($d) => Carbon::parse($d)->toDateString())
                    ->unique()->values();
                $d = $dates->count() >= 2 ? $dates[1] : ($dates[0] ?? $today->subDay()->toDateString());
                return [$d, $d];

            case 'last7days':
                return [$today->copy()->subDays(6)->toDateString(), $today->toDateString()];

            case 'last30days':
                return [$today->copy()->subDays(29)->toDateString(), $today->toDateString()];

            case 'last_month':
                return [
                    $today->copy()->subMonthNoOverflow()->startOfMonth()->toDateString(),
                    $today->copy()->subMonthNoOverflow()->endOfMonth()->toDateString(),
                ];

            case 'this_year':
                return [Carbon::create($today->year, 1, 1)->toDateString(), $today->toDateString()];

            case 'last_year':
                return [
                    Carbon::create($today->year - 1, 1, 1)->toDateString(),
                    Carbon::create($today->year - 1, 12, 31)->toDateString(),
                ];

            case 'custom':
                return [
                    $request->input('from', $today->copy()->subDays(29)->toDateString()),
                    $request->input('to',   $today->toDateString()),
                ];

            default:
                return [$today->copy()->subDays(29)->toDateString(), $today->toDateString()];
        }
    }

    private function arrowZone($arrow, string $scoringSystem): ?string
    {
        if ($arrow === null || $arrow === '' || $arrow === 'M') return null;
        $v = strtoupper(trim((string)$arrow));
        if ($v === 'X') return 'gold';
        $n = (int)$v;
        if ($scoringSystem === '3d') {
            return match($n) { 20 => 'gold', 17 => 'blue', 10 => 'white', default => null };
        }
        if ($scoringSystem === 'field') {
            if ($n === 6) return 'gold';
            if ($n >= 4)  return 'red';
            if ($n >= 2)  return 'blue';
            if ($n === 1) return 'white';
            return null;
        }
        if ($scoringSystem === 'clout') {
            return match($n) { 5 => 'gold', 4 => 'red', 3 => 'blue', 2 => 'black', 1 => 'white', default => null };
        }
        // standard / compound
        if ($n >= 9) return 'gold';
        if ($n >= 7) return 'red';
        if ($n >= 5) return 'blue';
        if ($n >= 3) return 'black';
        if ($n >= 1) return 'white';
        return null;
    }
}
