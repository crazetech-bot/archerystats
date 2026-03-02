<?php

namespace App\Http\Controllers;

use App\Models\Archer;
use App\Models\EliminationMatch;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EliminationMatchController extends Controller
{
    public function index(Request $request): View
    {
        $query = EliminationMatch::with(['archerA', 'archerB', 'winner'])
            ->latest('date')
            ->latest('id');

        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        // Coaches see only matches involving their assigned archers or club archers
        if (auth()->user()->role === 'coach') {
            $coach = auth()->user()->coach;
            $relevantIds = collect();
            if ($coach) {
                $relevantIds = $coach->archers()->pluck('archers.id');
            }
            if (auth()->user()->club_id) {
                $clubIds = \App\Models\Archer::where('club_id', auth()->user()->club_id)->pluck('id');
                $relevantIds = $relevantIds->merge($clubIds)->unique();
            }
            $query->where(function ($q) use ($relevantIds) {
                $q->whereIn('archer_a_id', $relevantIds)
                  ->orWhereIn('archer_b_id', $relevantIds);
            });
        }

        $matches    = $query->paginate(20)->withQueryString();
        $total      = EliminationMatch::count();
        $completed  = EliminationMatch::where('status', 'completed')->count();
        $inProgress = $total - $completed;

        return view('elimination-matches.index', compact('matches', 'total', 'completed', 'inProgress'));
    }

    public function create(): View
    {
        $archers = Archer::join('users', 'archers.user_id', '=', 'users.id')
            ->orderBy('users.name')
            ->select('archers.id', 'archers.ref_no', \DB::raw('users.name as name'))
            ->get();
        return view('elimination-matches.create', compact('archers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'archer_a_type'    => 'required|in:registered,guest',
            'archer_b_type'    => 'required|in:registered,guest',
            'archer_a_id'      => 'nullable|exists:archers,id|required_if:archer_a_type,registered',
            'archer_a_name'    => 'nullable|string|max:255|required_if:archer_a_type,guest',
            'archer_b_id'      => 'nullable|exists:archers,id|required_if:archer_b_type,registered',
            'archer_b_name'    => 'nullable|string|max:255|required_if:archer_b_type,guest',
            'category'         => 'required|in:outdoor,indoor,mssm',
            'format'           => 'nullable|in:set_point,cumulative',
            'date'             => 'required|date',
            'location'         => 'nullable|string|max:255',
            'competition_name' => 'nullable|string|max:255',
        ]);

        // Prevent same registered archer in both slots
        if ($request->archer_a_type === 'registered' && $request->archer_b_type === 'registered'
            && $request->archer_a_id && $request->archer_a_id === $request->archer_b_id) {
            return back()->withErrors(['archer_b_id' => 'Archer B must be different from Archer A.'])->withInput();
        }

        $format = $request->input('format', 'set_point');

        // Compound cumulative only applies to outdoor
        if ($request->category !== 'outdoor') {
            $format = 'set_point';
        }

        // Auto-set distance for compound outdoor
        $distanceM = ($format === 'cumulative') ? 50 : null;

        $aIsRegistered = $request->archer_a_type === 'registered';
        $bIsRegistered = $request->archer_b_type === 'registered';

        $match = EliminationMatch::create([
            'archer_a_id'      => $aIsRegistered ? $request->archer_a_id : null,
            'archer_a_name'    => $aIsRegistered ? null : $request->archer_a_name,
            'archer_b_id'      => $bIsRegistered ? $request->archer_b_id : null,
            'archer_b_name'    => $bIsRegistered ? null : $request->archer_b_name,
            'category'         => $request->category,
            'format'           => $format,
            'distance_m'       => $distanceM,
            'date'             => $request->date,
            'location'         => $request->location,
            'competition_name' => $request->competition_name,
            'status'           => 'in_progress',
        ]);

        return redirect()->route('elimination-matches.scorecard', $match)
            ->with('success', 'Match created. Enter arrows below.');
    }

    public function scorecard(EliminationMatch $eliminationMatch): View
    {
        $eliminationMatch->load(['archerA', 'archerB', 'winner', 'shootOffWinner']);

        $arrowValues = $eliminationMatch->arrow_values ?? ['a' => [], 'b' => []];
        $arrowsA = $arrowValues['a'] ?? [];
        $arrowsB = $arrowValues['b'] ?? [];

        $setsInit = [];
        for ($i = 0; $i < 5; $i++) {
            $setsInit[] = [
                'a' => array_values(array_pad(array_map(fn($v) => $v ?? '', $arrowsA[$i] ?? []), 3, '')),
                'b' => array_values(array_pad(array_map(fn($v) => $v ?? '', $arrowsB[$i] ?? []), 3, '')),
            ];
        }

        $nameA = $eliminationMatch->archer_a_id
            ? $eliminationMatch->archerA->full_name
            : $eliminationMatch->archer_a_name;
        $nameB = $eliminationMatch->archer_b_id
            ? $eliminationMatch->archerB->full_name
            : $eliminationMatch->archer_b_name;

        return view('elimination-matches.scorecard', [
            'match'    => $eliminationMatch,
            'setsInit' => $setsInit,
            'nameA'    => $nameA,
            'nameB'    => $nameB,
            'refA'     => $eliminationMatch->archer_a_id ? $eliminationMatch->archerA->ref_no : '—',
            'refB'     => $eliminationMatch->archer_b_id ? $eliminationMatch->archerB->ref_no : '—',
        ]);
    }

    public function saveScores(Request $request, EliminationMatch $eliminationMatch): RedirectResponse
    {
        $rawArrows = $request->input('arrows', ['a' => [], 'b' => []]);

        // Normalize into 5×3 structure
        $arrowValues = ['a' => [], 'b' => []];
        foreach (['a', 'b'] as $side) {
            for ($i = 0; $i < 5; $i++) {
                $arrowValues[$side][$i] = [];
                for ($j = 0; $j < 3; $j++) {
                    $raw = strtoupper(trim($rawArrows[$side][$i][$j] ?? ''));
                    $arrowValues[$side][$i][] = $raw !== '' ? $raw : null;
                }
            }
        }

        $winnerId         = null;
        $shootOff         = false;
        $shootOffWinnerId = null;
        $status           = 'in_progress';

        if ($eliminationMatch->format === 'cumulative') {
            // ── Compound: cumulative scoring ──────────────────────────────────────

            // Validate all entered values against compound face
            $validCompound = ['X', '10', '9', '8', '7', '6', '5', 'M'];
            foreach (['a', 'b'] as $side) {
                for ($i = 0; $i < 5; $i++) {
                    for ($j = 0; $j < 3; $j++) {
                        $v = $arrowValues[$side][$i][$j];
                        if ($v !== null && !in_array(strtoupper($v), $validCompound)) {
                            return back()
                                ->withErrors(['arrows' => "Invalid compound score '{$v}'. Valid values: X, 10–5, M only."])
                                ->withInput();
                        }
                    }
                }
            }

            // Flatten and check completeness
            $allArrowsA  = array_merge(...$arrowValues['a']);
            $allArrowsB  = array_merge(...$arrowValues['b']);
            $allComplete = !in_array(null, $allArrowsA, true) && !in_array(null, $allArrowsB, true);

            if ($allComplete) {
                $totalA = array_sum(array_map(fn($v) => $this->normalizeCompoundArrow($v), $allArrowsA));
                $totalB = array_sum(array_map(fn($v) => $this->normalizeCompoundArrow($v), $allArrowsB));

                if ($totalA !== $totalB) {
                    $status   = 'completed';
                    $winnerId = $totalA > $totalB
                        ? $eliminationMatch->archer_a_id
                        : $eliminationMatch->archer_b_id;
                } else {
                    // Tied — check shoot-off
                    $shootOff = true;
                    $soA = strtoupper(trim($request->input('shoot_off_a', '')));
                    $soB = strtoupper(trim($request->input('shoot_off_b', '')));

                    if ($soA !== '' && $soB !== '') {
                        $soValA = $this->normalizeCompoundArrow($soA);
                        $soValB = $this->normalizeCompoundArrow($soB);

                        if ($soValA !== $soValB) {
                            $status           = 'completed';
                            $shootOffWinnerId = $soValA > $soValB
                                ? $eliminationMatch->archer_a_id
                                : $eliminationMatch->archer_b_id;
                            $winnerId = $shootOffWinnerId;
                        } else {
                            $nearest = $request->input('nearest_to_center');
                            if (in_array($nearest, ['a', 'b'])) {
                                $status           = 'completed';
                                $shootOffWinnerId = $nearest === 'a'
                                    ? $eliminationMatch->archer_a_id
                                    : $eliminationMatch->archer_b_id;
                                $winnerId = $shootOffWinnerId;
                            }
                        }
                    }
                }
            }
        } else {
            // ── Set-Point (Recurve): existing logic ───────────────────────────────
            $ptsA = 0;
            $ptsB = 0;

            for ($i = 0; $i < 5; $i++) {
                $totalA    = 0;
                $totalB    = 0;
                $completeA = true;
                $completeB = true;

                foreach ($arrowValues['a'][$i] as $v) {
                    if ($v === null) { $completeA = false; break; }
                    $totalA += $this->normalizeArrow($v);
                }
                foreach ($arrowValues['b'][$i] as $v) {
                    if ($v === null) { $completeB = false; break; }
                    $totalB += $this->normalizeArrow($v);
                }

                if (!$completeA || !$completeB) break;

                if ($totalA > $totalB)     { $ptsA += 2; }
                elseif ($totalB > $totalA) { $ptsB += 2; }
                else                       { $ptsA += 1; $ptsB += 1; }

                if ($ptsA >= 6 || $ptsB >= 6) {
                    $status   = 'completed';
                    $winnerId = $ptsA >= 6
                        ? $eliminationMatch->archer_a_id
                        : $eliminationMatch->archer_b_id;
                    break;
                }
            }

            // Check 5-5 shoot-off
            if ($status !== 'completed' && $ptsA === 5 && $ptsB === 5) {
                $shootOff = true;
                $soA = strtoupper(trim($request->input('shoot_off_a', '')));
                $soB = strtoupper(trim($request->input('shoot_off_b', '')));

                if ($soA !== '' && $soB !== '') {
                    $soValA = $this->normalizeArrow($soA);
                    $soValB = $this->normalizeArrow($soB);

                    if ($soValA !== $soValB) {
                        $status           = 'completed';
                        $shootOffWinnerId = $soValA > $soValB
                            ? $eliminationMatch->archer_a_id
                            : $eliminationMatch->archer_b_id;
                        $winnerId = $shootOffWinnerId;
                    } else {
                        $nearest = $request->input('nearest_to_center');
                        if (in_array($nearest, ['a', 'b'])) {
                            $status           = 'completed';
                            $shootOffWinnerId = $nearest === 'a'
                                ? $eliminationMatch->archer_a_id
                                : $eliminationMatch->archer_b_id;
                            $winnerId = $shootOffWinnerId;
                        }
                    }
                }
            }
        }

        $nearestToCenter = in_array($request->input('nearest_to_center'), ['a', 'b'])
            ? $request->input('nearest_to_center')
            : null;

        $eliminationMatch->update([
            'arrow_values'        => $arrowValues,
            'shoot_off_a'         => $request->input('shoot_off_a') ?: null,
            'shoot_off_b'         => $request->input('shoot_off_b') ?: null,
            'nearest_to_center'   => $nearestToCenter,
            'shoot_off'           => $shootOff,
            'shoot_off_winner_id' => $shootOffWinnerId,
            'winner_id'           => $winnerId,
            'status'              => $status,
        ]);

        $msg = $status === 'completed'
            ? 'Match completed and saved.'
            : 'Progress saved.';

        return redirect()->route('elimination-matches.scorecard', $eliminationMatch)
            ->with('success', $msg);
    }

    public function destroy(EliminationMatch $eliminationMatch): RedirectResponse
    {
        $eliminationMatch->delete();
        return redirect()->route('elimination-matches.index')
            ->with('success', 'Match deleted.');
    }

    private function normalizeArrow(string $v): int
    {
        $v = strtoupper(trim($v));
        if ($v === 'X') return 10;
        if ($v === 'M' || $v === '0') return 0;
        if (is_numeric($v) && (int)$v >= 1 && (int)$v <= 10) return (int)$v;
        return 0;
    }

    private function normalizeCompoundArrow(string $v): int
    {
        $v = strtoupper(trim($v));
        if ($v === 'X') return 10;
        if ($v === 'M') return 0;
        $n = (int) $v;
        if ($n >= 5 && $n <= 10) return $n;
        return 0;
    }
}
