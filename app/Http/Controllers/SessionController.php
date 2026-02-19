<?php

namespace App\Http\Controllers;

use App\Models\Archer;
use App\Models\ArcherySession;
use App\Models\End;
use App\Models\RoundType;
use App\Models\Score;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SessionController extends Controller
{
    public function index(Archer $archer): View
    {
        $sessions = $archer->sessions()
            ->with(['roundType', 'score'])
            ->orderByDesc('date')
            ->paginate(20);

        return view('sessions.index', compact('archer', 'sessions'));
    }

    public function create(Archer $archer): View
    {
        $roundTypes = RoundType::where('active', true)->orderBy('category')->orderBy('name')->get();

        return view('sessions.create', compact('archer', 'roundTypes'));
    }

    public function store(Archer $archer, Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'round_type_id'    => ['required', 'exists:round_types,id'],
            'date'             => ['required', 'date'],
            'location'         => ['nullable', 'string', 'max:200'],
            'weather'          => ['nullable', 'in:sunny,cloudy,windy,rain,indoor,other'],
            'is_competition'   => ['nullable', 'boolean'],
            'competition_name' => ['nullable', 'string', 'max:200'],
            'notes'            => ['nullable', 'string'],
        ]);

        $roundType = RoundType::findOrFail($validated['round_type_id']);

        $session = ArcherySession::create([
            'archer_id'        => $archer->id,
            'round_type_id'    => $roundType->id,
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

        for ($e = 1; $e <= $roundType->num_ends; $e++) {
            End::create([
                'score_id'     => $score->id,
                'end_number'   => $e,
                'arrow_values' => array_fill(0, $roundType->arrows_per_end, null),
                'end_total'    => 0,
            ]);
        }

        return redirect()->route('sessions.scorecard', $session)
            ->with('success', 'Session created. Enter your scores below.');
    }

    public function scorecard(ArcherySession $session): View
    {
        $session->load(['archer.user', 'archer.club', 'roundType', 'score.ends']);

        return view('sessions.scorecard', compact('session'));
    }

    public function saveScores(ArcherySession $session, Request $request): RedirectResponse
    {
        $request->validate([
            'arrows'     => ['nullable', 'array'],
            'arrows.*'   => ['nullable', 'array'],
            'arrows.*.*' => ['nullable', 'string', 'max:2'],
        ]);

        $score = $session->score;
        $endsMap = $score->ends->keyBy('end_number');
        $input = $request->input('arrows', []);

        foreach ($endsMap as $endNumber => $end) {
            $rawArrows = $input[$endNumber] ?? [];
            $arrows = [];

            foreach ($rawArrows as $val) {
                $v = strtoupper(trim((string) $val));
                if ($v === 'X') {
                    $arrows[] = 'X';
                } elseif ($v === 'M' || $v === '0') {
                    $arrows[] = 'M';
                } elseif (is_numeric($v) && (int) $v >= 1 && (int) $v <= 10) {
                    $arrows[] = (int) $v;
                } else {
                    $arrows[] = null;
                }
            }

            // Pad to arrows_per_end length
            $ape = $session->roundType->arrows_per_end;
            while (count($arrows) < $ape) {
                $arrows[] = null;
            }

            $end->arrow_values = $arrows;
            $end->end_total = $end->calculateTotal();
            $end->save();
        }

        $score->load('ends');
        $score->recalculate();

        return redirect()->route('sessions.scorecard', $session)
            ->with('success', 'Scores saved successfully.');
    }

    public function show(ArcherySession $session): View
    {
        $session->load(['archer.user', 'archer.club', 'roundType', 'score.ends']);

        return view('sessions.scorecard', compact('session'));
    }

    public function destroy(ArcherySession $session): RedirectResponse
    {
        $archerId = $session->archer_id;
        $session->delete();

        return redirect()->route('sessions.index', $archerId)
            ->with('success', 'Session deleted.');
    }
}
