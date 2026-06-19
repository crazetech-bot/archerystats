<?php

namespace App\Http\Controllers;

use App\Models\Archer;
use App\Models\ArcherySession;
use App\Models\Arrow;
use App\Models\Coach;
use App\Models\End;
use App\Models\RoundType;
use App\Models\Score;
use App\Support\TargetScoring;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SessionController extends Controller
{
    public function index(Archer $archer): View
    {
        $user = auth()->user();
        if ($user->role === 'archer' && $user->archer?->id !== $archer->id) {
            abort(403);
        }

        $sessions = $archer->sessions()
            ->with(['roundType', 'score'])
            ->orderByDesc('date')
            ->paginate(20);

        return view('sessions.index', compact('archer', 'sessions'));
    }

    public function create(Archer $archer): View
    {
        $user = auth()->user();
        if ($user->role === 'archer' && $user->archer?->id !== $archer->id) {
            abort(403);
        }
        if ($user->role === 'coach') {
            abort(403);
        }

        $roundTypes = RoundType::where('active', true)
            ->where('is_custom', false)
            ->orderByRaw("FIELD(category,'indoor','outdoor','field','3d','mssm','bakat') ASC")
            ->orderBy('distance_meters')
            ->orderBy('name')
            ->get()
            ->groupBy('category');

        // Default round type based on archer's primary division
        $divisions = is_array($archer->divisions)
            ? $archer->divisions
            : json_decode($archer->divisions ?? '[]', true);
        $primaryDivision = strtolower($divisions[0] ?? '');

        $defaultRoundNames = [
            'recurve'  => 'WA 70m Outdoor Recurve',
            'compound' => 'WA 50m Outdoor Compound',
            'barebow'  => 'WA 50m Outdoor Barebow',
        ];
        $defaultRoundType   = isset($defaultRoundNames[$primaryDivision])
            ? $roundTypes->flatten()->firstWhere('name', $defaultRoundNames[$primaryDivision])
            : null;
        $defaultRoundTypeId = $defaultRoundType?->id;
        $defaultTab         = $defaultRoundType?->category;
        $archerDiscipline   = $primaryDivision ?: 'recurve';

        return view('sessions.create', compact(
            'archer', 'roundTypes', 'defaultRoundTypeId', 'defaultTab', 'archerDiscipline'
        ));
    }

    public function store(Archer $archer, Request $request): RedirectResponse
    {
        $user = auth()->user();
        if ($user->role === 'archer' && $user->archer?->id !== $archer->id) {
            abort(403);
        }
        if ($user->role === 'coach') {
            abort(403);
        }

        if ($request->boolean('is_custom')) {
            // ── Custom Round Path ────────────────────────────────────────────
            $request->validate([
                'custom_name'                      => ['nullable', 'string', 'max:100'],
                'custom_discipline'                => ['nullable', 'in:recurve,compound,barebow,field,3d,clout,longbow'],
                'custom_segments'                  => ['required', 'array', 'min:1', 'max:10'],
                'custom_segments.*.distance'       => ['required', 'integer', 'min:1', 'max:300'],
                'custom_segments.*.face'           => ['required', 'integer', 'min:1'],
                'custom_segments.*.scoring'        => ['required', 'in:standard,compound,reduced,six_ring,field,3d,clout,standard_x11,six_ring_x11'],
                'custom_segments.*.num_ends'       => ['required', 'integer', 'min:1', 'max:24'],
                'custom_segments.*.arrows_per_end' => ['required', 'integer', 'min:1', 'max:12'],
            ]);

            $segs      = $request->input('custom_segments');
            $totalEnds = array_sum(array_column($segs, 'num_ends'));
            $ape       = (int) $segs[0]['arrows_per_end'];

            $roundType = RoundType::create([
                'name'                => $request->input('custom_name') ?: 'Custom Round',
                'category'            => 'custom',
                'discipline'          => $request->input('custom_discipline') ?: null,
                'is_custom'           => true,
                'num_ends'            => $totalEnds,
                'arrows_per_end'      => $ape,
                'scoring_system'      => $segs[0]['scoring'] ?? 'standard',
                'distance_meters'     => (int) $segs[0]['distance'],
                'target_face_cm'      => (int) $segs[0]['face'],
                'max_score_per_arrow' => 10,
                'active'              => true,
                'distance_segments'   => array_values(array_map(fn($s) => [
                    'distance'       => (int) $s['distance'],
                    'face'           => (int) $s['face'],
                    'scoring'        => $s['scoring'],
                    'num_ends'       => (int) $s['num_ends'],
                    'arrows_per_end' => (int) $s['arrows_per_end'],
                    'label'          => $s['distance'] . 'm · ' . $s['face'] . 'cm',
                ], $segs)),
            ]);
        } else {
            // ── Predefined Round Path ────────────────────────────────────────
            $request->validate([
                'round_type_id' => ['required', 'exists:round_types,id'],
            ]);
            $roundType = RoundType::findOrFail($request->input('round_type_id'));
        }

        // ── Shared Session Fields ────────────────────────────────────────────
        $validated = $request->validate([
            'date'             => ['required', 'date'],
            'distance_meters'  => ['nullable', 'integer', 'min:1', 'max:300'],
            'target_face_cm'   => ['nullable', 'integer', 'min:1'],
            'location'         => ['nullable', 'string', 'max:200'],
            'weather'          => ['nullable', 'in:sunny,cloudy,windy,rain,indoor,other'],
            'is_competition'   => ['nullable', 'boolean'],
            'competition_name' => ['nullable', 'string', 'max:200'],
            'notes'            => ['nullable', 'string'],
        ]);

        $session = ArcherySession::create([
            'archer_id'        => $archer->id,
            'round_type_id'    => $roundType->id,
            'distance_meters'  => $validated['distance_meters'] ?? null,
            'target_face_cm'   => $validated['target_face_cm'] ?? null,
            'date'             => $validated['date'],
            'location'         => $validated['location'] ?? null,
            'weather'          => $validated['weather'] ?? null,
            'is_competition'   => (bool) ($validated['is_competition'] ?? false),
            'competition_name' => $validated['competition_name'] ?? null,
            'notes'            => $validated['notes'] ?? null,
        ]);

        $score = Score::create([
            'archery_session_id' => $session->id,
            'total_score'        => 0,
            'x_count'            => 0,
            'gold_count'         => 0,
            'hit_count'          => 0,
            'miss_count'         => 0,
        ]);

        // Build end→scoring map from distance_segments
        $segScoring = [];
        if ($segments = $roundType->distance_segments) {
            $endNum = 1;
            foreach ($segments as $seg) {
                $segEnds = (int) ($seg['num_ends'] ?? 6);
                $sys     = $seg['scoring'] ?? $roundType->scoring_system ?? 'standard';
                for ($i = 0; $i < $segEnds; $i++) {
                    $segScoring[$endNum++] = $sys;
                }
            }
        }

        for ($e = 1; $e <= $roundType->num_ends; $e++) {
            End::create([
                'score_id'       => $score->id,
                'end_number'     => $e,
                'arrow_values'   => array_fill(0, $roundType->arrows_per_end, null),
                'end_total'      => 0,
                'scoring_system' => $segScoring[$e] ?? $roundType->scoring_system ?? 'standard',
            ]);
        }

        return redirect()->route('sessions.scorecard', $session)
            ->with('success', 'Session created. Enter your scores below.');
    }

    public function scorecard(ArcherySession $session): View
    {
        $user = auth()->user();
        if ($user->role === 'archer' && $user->archer?->id !== $session->archer_id) {
            abort(403);
        }
        if ($user->role === 'coach') {
            $coachClubId = $user->coach?->club_id;
            if ($coachClubId && $session->archer->club_id !== $coachClubId) {
                abort(403);
            }
        }

        $session->load(['archer.user', 'archer.club', 'roundType', 'score.ends.arrows']);
        $scoringSystem = $session->roundType->scoring_system ?? 'standard';

        return view('sessions.scorecard', compact('session', 'scoringSystem'));
    }

    public function saveScores(ArcherySession $session, Request $request): RedirectResponse
    {
        $user = auth()->user();
        if ($user->role === 'archer' && $user->archer?->id !== $session->archer_id) {
            abort(403);
        }
        if ($user->role === 'coach') {
            abort(403);
        }

        $request->validate([
            'arrows'     => ['nullable', 'array'],
            'arrows.*'   => ['nullable', 'array'],
            'arrows.*.*' => ['nullable', 'string', 'max:2'],
            'coords'     => ['nullable', 'array'],
            'coords.*'   => ['nullable', 'array'],
            'coords.*.*' => ['nullable', 'string', 'max:32'],
        ]);

        $scoringSystem = $session->roundType->scoring_system ?? 'standard';
        $score   = $session->score;
        $endsMap = $score->ends->keyBy('end_number');
        $ape     = $session->roundType->arrows_per_end;
        $input   = $request->input('arrows', []);
        $coords  = $request->input('coords', []);
        $layout  = $session->endLayout();

        foreach ($endsMap as $endNumber => $end) {
            $endSys    = $end->scoring_system ?? $scoringSystem;
            $faceCm    = (int) ($layout[$endNumber]['face'] ?? $session->effective_face ?? 122);
            $rawArrows = $input[$endNumber] ?? [];
            $endCoords = $coords[$endNumber] ?? [];
            $arrows    = [];
            $plotted   = []; // arrow_number => [x_mm, y_mm]

            for ($i = 0; $i < $ape; $i++) {
                $coord = $this->parseCoord($endCoords[$i] ?? null);

                if ($coord !== null) {
                    // Plotted: the impact coordinate is the source of truth — derive the value from it.
                    $res          = TargetScoring::resolve($endSys, $coord[0], $coord[1], $faceCm);
                    $arrows[$i]   = $this->normalizeArrow($res['display'], $endSys);
                    $plotted[$i + 1] = $coord;
                } else {
                    $v          = strtoupper(trim((string) ($rawArrows[$i] ?? '')));
                    $arrows[$i] = $this->normalizeArrow($v, $endSys);
                }
            }

            $end->arrow_values = $arrows;
            $end->end_total    = $end->calculateTotal($endSys);
            $end->save();

            // Resync the per-arrow coordinate layer for this end (additive analytics moat).
            $end->arrows()->delete();
            foreach ($plotted as $arrowNumber => [$x, $y]) {
                (new Arrow(['end_id' => $end->id, 'arrow_number' => $arrowNumber]))
                    ->setImpactFor($x, $y, $faceCm, $endSys)
                    ->save();
            }
        }

        $score->load('ends');
        $score->recalculate($scoringSystem);

        // Broadcast live score update (fires via Pusher if configured, silently skipped otherwise)
        try {
            $session->load(['archer.club', 'archer.stateTeam', 'roundType', 'score.ends']);
            $row = app(\App\Services\LiveScoringFormatterService::class)->formatRow($session);
            \App\Events\ScoreUpdated::dispatch($row);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('ScoreUpdated broadcast failed: ' . $e->getMessage());
        }

        // Auto-update Personal Best if this score beats the stored PB
        $score->refresh();
        $newTotal    = $score->total_score;
        $roundType   = $session->roundType;
        $totalArrows = $roundType->num_ends * $roundType->arrows_per_end;
        $archer      = $session->archer;
        $pbUpdated   = false;

        if ($newTotal > 0 && in_array($totalArrows, [36, 72])) {
            if ($session->is_competition) {
                // Official PB
                $pbScoreField = "pb_official_{$totalArrows}_score";
                $pbDateField  = "pb_official_{$totalArrows}_date";
                $pbTournField = "pb_official_{$totalArrows}_tournament";

                if ($newTotal > ($archer->$pbScoreField ?? 0)) {
                    $archer->update([
                        $pbScoreField => $newTotal,
                        $pbDateField  => $session->date,
                        $pbTournField => $session->competition_name,
                    ]);
                    $pbUpdated = true;
                }
            } else {
                // Unofficial PB
                $pbScoreField = "pb_unofficial_{$totalArrows}_score";
                $pbDateField  = "pb_unofficial_{$totalArrows}_date";

                if ($newTotal > ($archer->$pbScoreField ?? 0)) {
                    $archer->update([
                        $pbScoreField => $newTotal,
                        $pbDateField  => $session->date,
                    ]);
                    $pbUpdated = true;
                }
            }
        }

        $message = $pbUpdated
            ? 'Scores saved — new Personal Best recorded!'
            : 'Scores saved successfully.';

        return redirect()->route('sessions.scorecard', $session)
            ->with('success', $message);
    }

    public function show(ArcherySession $session): View
    {
        $session->load(['archer.user', 'archer.club', 'roundType', 'score.ends.arrows']);
        $scoringSystem = $session->roundType->scoring_system ?? 'standard';

        return view('sessions.scorecard', compact('session', 'scoringSystem'));
    }

    /** Parse a posted "x,y" mm coordinate string into [float, float], or null. */
    private function parseCoord(?string $raw): ?array
    {
        if ($raw === null) {
            return null;
        }
        $raw = trim($raw);
        if ($raw === '') {
            return null;
        }
        $parts = explode(',', $raw);
        if (count($parts) !== 2 || ! is_numeric($parts[0]) || ! is_numeric($parts[1])) {
            return null;
        }

        return [(float) $parts[0], (float) $parts[1]];
    }

    private function normalizeArrow(string $v, string $scoringSystem): int|string|null
    {
        return match ($scoringSystem) {
            'field' => match (true) {
                $v === 'X'                               => 'X',
                $v === 'M' || $v === '0'                 => 'M',
                is_numeric($v) && (int)$v >= 1 && (int)$v <= 6 => (int)$v,
                default                                  => null,
            },
            '3d' => match (true) {
                $v === 'M'                                         => 'M',
                is_numeric($v) && in_array((int)$v, [20, 17, 10]) => (int)$v,
                default                                            => null,
            },
            'clout' => match (true) {
                $v === 'M' || $v === '0'                        => 'M',
                is_numeric($v) && (int)$v >= 1 && (int)$v <= 5 => (int)$v,
                default                                         => null,
            },
            'compound' => match (true) {  // X·10–5·M (WA compound = same as reduced)
                $v === 'X'                                         => 'X',
                $v === 'M' || $v === '0'                          => 'M',
                is_numeric($v) && (int)$v >= 5 && (int)$v <= 10  => (int)$v,
                default                                            => null,
            },
            'reduced' => match (true) {  // X·10–5·M
                $v === 'X'                                         => 'X',
                $v === 'M' || $v === '0'                          => 'M',
                is_numeric($v) && (int)$v >= 5 && (int)$v <= 10  => (int)$v,
                default                                            => null,
            },
            'six_ring' => match (true) {  // X·10–6·M (6-ring face)
                $v === 'X'                                         => 'X',
                $v === 'M' || $v === '0'                          => 'M',
                is_numeric($v) && (int)$v >= 6 && (int)$v <= 10  => (int)$v,
                default                                            => null,
            },
            'standard_x11' => match (true) {  // X=11·10–1·M
                $v === 'X'                                         => 'X',
                $v === 'M' || $v === '0'                          => 'M',
                is_numeric($v) && (int)$v >= 1 && (int)$v <= 10  => (int)$v,
                default                                            => null,
            },
            'six_ring_x11' => match (true) {  // X=11·10–6·M
                $v === 'X'                                         => 'X',
                $v === 'M' || $v === '0'                          => 'M',
                is_numeric($v) && (int)$v >= 6 && (int)$v <= 10  => (int)$v,
                default                                            => null,
            },
            default => match (true) { // standard: X·10–1·M
                $v === 'X'                                         => 'X',
                $v === 'M' || $v === '0'                          => 'M',
                is_numeric($v) && (int)$v >= 1 && (int)$v <= 10  => (int)$v,
                default                                            => null,
            },
        };
    }

    public function destroy(ArcherySession $session): RedirectResponse
    {
        $user = auth()->user();
        $isOwner = $user->role === 'archer' && $user->archer?->id === $session->archer_id;
        if (!$user->isClubAdmin() && !$isOwner) {
            abort(403);
        }

        $archerId = $session->archer_id;
        $session->delete();

        return redirect()->route('sessions.index', $archerId)
            ->with('success', 'Session deleted.');
    }

    public function coachView(Coach $coach): View
    {
        $sessions = ArcherySession::with(['archer.user', 'roundType', 'score'])
            ->whereHas('archer', fn($q) => $q->where('club_id', $coach->club_id))
            ->orderByDesc('date')
            ->paginate(25);

        return view('coaches.club-results.index', compact('coach', 'sessions'));
    }

    public function coachShowSession(Coach $coach, ArcherySession $session): View
    {
        $session->load(['archer.user', 'archer.club', 'roundType', 'score.ends']);

        return view('coaches.club-results.show', compact('coach', 'session'));
    }
}
