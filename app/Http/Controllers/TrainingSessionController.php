<?php

namespace App\Http\Controllers;

use App\Models\ArcherySession;
use App\Models\Coach;
use App\Models\EliminationMatch;
use App\Models\End;
use App\Models\RoundType;
use App\Models\Score;
use App\Models\TrainingSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TrainingSessionController extends Controller
{
    public function index(Coach $coach): View
    {
        $sessions = $coach->trainingSessions()
            ->withCount('archers')
            ->orderByDesc('date')
            ->paginate(20);

        return view('coaches.training.index', compact('coach', 'sessions'));
    }

    public function create(Coach $coach): View
    {
        $clubArchers = $coach->archers()->with('user')->orderBy('ref_no')->get();
        $roundTypes  = RoundType::where('active', true)->orderBy('category')->orderBy('name')->get()->groupBy('category');

        return view('coaches.training.create', compact('coach', 'clubArchers', 'roundTypes'));
    }

    public function store(Coach $coach, Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'date'             => ['required', 'date'],
            'location'         => ['nullable', 'string', 'max:200'],
            'focus_area'       => ['nullable', 'string', 'max:200'],
            'duration_minutes' => ['nullable', 'integer', 'min:1', 'max:1440'],
            'notes'            => ['nullable', 'string'],
            'archer_ids'       => ['nullable', 'array'],
            'archer_ids.*'     => ['exists:archers,id'],
            'round_type_id'    => ['nullable', 'exists:round_types,id'],
            'distance_meters'  => ['nullable', 'integer', 'min:1', 'max:500'],
            'target_face_cm'   => ['nullable', 'integer', 'min:10'],
        ]);

        $session = $coach->trainingSessions()->create([
            'date'             => $validated['date'],
            'location'         => $validated['location'] ?? null,
            'focus_area'       => $validated['focus_area'] ?? null,
            'duration_minutes' => $validated['duration_minutes'] ?? null,
            'notes'            => $validated['notes'] ?? null,
            'round_type_id'    => $validated['round_type_id'] ?? null,
            'distance_meters'  => $validated['distance_meters'] ?? null,
            'target_face_cm'   => $validated['target_face_cm'] ?? null,
        ]);

        $archerIds = $validated['archer_ids'] ?? [];

        if (!empty($archerIds)) {
            $pivotData = collect($archerIds)
                ->mapWithKeys(fn($id) => [$id => ['attended' => true]])
                ->all();
            $session->archers()->sync($pivotData);
        }

        // Create an ArcherySession (with Score + Ends) for each attending archer if a round type was assigned
        if (!empty($validated['round_type_id']) && !empty($archerIds)) {
            $roundType = RoundType::find($validated['round_type_id']);
            foreach ($archerIds as $archerId) {
                $this->createAssignedSession($session->id, $archerId, $roundType, $validated);
            }
        }

        // Create elimination match pairs
        foreach ($request->input('em_pairs', []) as $pair) {
            $typeA  = $pair['archer_a_type'] ?? 'registered';
            $typeB  = $pair['archer_b_type'] ?? 'registered';
            $aId    = $typeA === 'registered' ? ($pair['archer_a_id'] ?? null) : null;
            $bId    = $typeB === 'registered' ? ($pair['archer_b_id'] ?? null) : null;
            $aName  = $typeA === 'guest' ? (trim($pair['archer_a_name'] ?? '') ?: null) : null;
            $bName  = $typeB === 'guest' ? (trim($pair['archer_b_name'] ?? '') ?: null) : null;
            $cat    = $pair['category'] ?? 'outdoor';
            $validA = ($typeA === 'registered' && $aId) || ($typeA === 'guest' && $aName);
            $validB = ($typeB === 'registered' && $bId) || ($typeB === 'guest' && $bName);
            $notSame = !($typeA === 'registered' && $typeB === 'registered' && $aId && $aId == $bId);
            if ($validA && $validB && $notSame && in_array($cat, ['outdoor', 'indoor', 'mssm'])) {
                EliminationMatch::create([
                    'training_session_id' => $session->id,
                    'archer_a_id'         => $aId,
                    'archer_a_name'       => $aName,
                    'archer_b_id'         => $bId,
                    'archer_b_name'       => $bName,
                    'category'            => $cat,
                    'date'                => $validated['date'],
                    'location'            => $validated['location'] ?? null,
                    'status'              => 'in_progress',
                ]);
            }
        }

        return redirect()->route('coaches.training.show', [$coach, $session])
            ->with('success', 'Training session created successfully.');
    }

    public function show(Coach $coach, TrainingSession $training): View
    {
        $training->load([
            'archers.user',
            'roundType',
            'assignedSessions.archer.user',
            'assignedSessions.score',
            'eliminationMatches.archerA.user',
            'eliminationMatches.archerB.user',
            'eliminationMatches.winner',
        ]);

        return view('coaches.training.show', compact('coach', 'training'));
    }

    public function edit(Coach $coach, TrainingSession $training): View
    {
        $clubArchers  = $coach->archers()->with('user')->orderBy('ref_no')->get();
        $attendingIds = $training->archers->pluck('id')->toArray();
        $roundTypes   = RoundType::where('active', true)->orderBy('category')->orderBy('name')->get()->groupBy('category');

        return view('coaches.training.edit', compact('coach', 'training', 'clubArchers', 'attendingIds', 'roundTypes'));
    }

    public function update(Coach $coach, TrainingSession $training, Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'date'             => ['required', 'date'],
            'location'         => ['nullable', 'string', 'max:200'],
            'focus_area'       => ['nullable', 'string', 'max:200'],
            'duration_minutes' => ['nullable', 'integer', 'min:1', 'max:1440'],
            'notes'            => ['nullable', 'string'],
            'archer_ids'       => ['nullable', 'array'],
            'archer_ids.*'     => ['exists:archers,id'],
            'round_type_id'    => ['nullable', 'exists:round_types,id'],
            'distance_meters'  => ['nullable', 'integer', 'min:1', 'max:500'],
            'target_face_cm'   => ['nullable', 'integer', 'min:10'],
        ]);

        $training->update([
            'date'             => $validated['date'],
            'location'         => $validated['location'] ?? null,
            'focus_area'       => $validated['focus_area'] ?? null,
            'duration_minutes' => $validated['duration_minutes'] ?? null,
            'notes'            => $validated['notes'] ?? null,
            'round_type_id'    => $validated['round_type_id'] ?? null,
            'distance_meters'  => $validated['distance_meters'] ?? null,
            'target_face_cm'   => $validated['target_face_cm'] ?? null,
        ]);

        $archerIds = collect($validated['archer_ids'] ?? []);

        $pivotData = $archerIds->mapWithKeys(fn($id) => [$id => ['attended' => true]])->all();
        $training->archers()->sync($pivotData);

        // Sync assigned ArcherySessions
        if (!empty($validated['round_type_id'])) {
            $roundType = RoundType::find($validated['round_type_id']);
            $existing  = $training->assignedSessions()->with('score')->get()->keyBy('archer_id');

            // Create sessions for newly added archers
            $archerIds->diff($existing->keys())->each(function ($archerId) use ($training, $roundType, $validated) {
                $this->createAssignedSession($training->id, $archerId, $roundType, $validated);
            });

            // Remove unscored sessions for archers removed from the list
            $existing->filter(fn($s, $id) => !$archerIds->contains($id) && ($s->score?->total_score ?? 0) === 0)->each->delete();

            // Update unscored existing sessions with new round type/distance/face/date/location
            $existing->filter(fn($s, $id) => $archerIds->contains($id) && ($s->score?->total_score ?? 0) === 0)->each(
                fn($s) => $s->update([
                    'round_type_id'   => $validated['round_type_id'],
                    'distance_meters' => $validated['distance_meters'] ?? null,
                    'target_face_cm'  => $validated['target_face_cm'] ?? null,
                    'date'            => $validated['date'],
                    'location'        => $validated['location'] ?? null,
                ])
            );
        } else {
            // Round type removed — delete only unscored assigned sessions
            $training->assignedSessions()->whereHas('score', fn($q) => $q->where('total_score', 0))->delete();
        }

        // Create new elimination match pairs (existing ones are preserved)
        foreach ($request->input('em_pairs', []) as $pair) {
            $typeA  = $pair['archer_a_type'] ?? 'registered';
            $typeB  = $pair['archer_b_type'] ?? 'registered';
            $aId    = $typeA === 'registered' ? ($pair['archer_a_id'] ?? null) : null;
            $bId    = $typeB === 'registered' ? ($pair['archer_b_id'] ?? null) : null;
            $aName  = $typeA === 'guest' ? (trim($pair['archer_a_name'] ?? '') ?: null) : null;
            $bName  = $typeB === 'guest' ? (trim($pair['archer_b_name'] ?? '') ?: null) : null;
            $cat    = $pair['category'] ?? 'outdoor';
            $validA = ($typeA === 'registered' && $aId) || ($typeA === 'guest' && $aName);
            $validB = ($typeB === 'registered' && $bId) || ($typeB === 'guest' && $bName);
            $notSame = !($typeA === 'registered' && $typeB === 'registered' && $aId && $aId == $bId);
            if ($validA && $validB && $notSame && in_array($cat, ['outdoor', 'indoor', 'mssm'])) {
                EliminationMatch::create([
                    'training_session_id' => $training->id,
                    'archer_a_id'         => $aId,
                    'archer_a_name'       => $aName,
                    'archer_b_id'         => $bId,
                    'archer_b_name'       => $bName,
                    'category'            => $cat,
                    'date'                => $validated['date'],
                    'location'            => $validated['location'] ?? null,
                    'status'              => 'in_progress',
                ]);
            }
        }

        return redirect()->route('coaches.training.show', [$coach, $training])
            ->with('success', 'Training session updated successfully.');
    }

    public function destroy(Coach $coach, TrainingSession $training): RedirectResponse
    {
        // Remove unscored assigned sessions; keep scored ones (they belong to the archer's history)
        $training->assignedSessions()->whereHas('score', fn($q) => $q->where('total_score', 0))->delete();

        $training->delete();

        return redirect()->route('coaches.training.index', $coach)
            ->with('success', 'Training session deleted.');
    }

    /** Create an ArcherySession with a blank Score + Ends for a coach-assigned archer */
    private function createAssignedSession(int $trainingSessionId, int $archerId, RoundType $roundType, array $validated): void
    {
        $archerSession = ArcherySession::create([
            'archer_id'           => $archerId,
            'round_type_id'       => $roundType->id,
            'distance_meters'     => $validated['distance_meters'] ?? null,
            'target_face_cm'      => $validated['target_face_cm'] ?? null,
            'date'                => $validated['date'],
            'location'            => $validated['location'] ?? null,
            'is_competition'      => false,
            'training_session_id' => $trainingSessionId,
            'assigned_by_coach'   => true,
        ]);

        $score = Score::create([
            'archery_session_id' => $archerSession->id,
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
    }
}
