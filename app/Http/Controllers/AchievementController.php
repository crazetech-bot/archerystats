<?php

namespace App\Http\Controllers;

use App\Models\Archer;
use App\Models\ArcherAchievement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AchievementController extends Controller
{
    public function store(Request $request, Archer $archer): RedirectResponse
    {
        $this->authorizeAccess($archer);

        $validated = $request->validate([
            'date'        => ['required', 'date'],
            'achievement' => ['required', 'string', 'max:255'],
            'team'        => ['nullable', 'string', 'max:100'],
            'tournament'  => ['nullable', 'string', 'max:255'],
        ]);

        $archer->achievements()->create($validated);

        return back()->with('achievement_success', 'Achievement added.');
    }

    public function destroy(Archer $archer, ArcherAchievement $achievement): RedirectResponse
    {
        $this->authorizeAccess($archer);

        abort_if($achievement->archer_id !== $archer->id, 403);

        $achievement->delete();

        return back()->with('achievement_success', 'Achievement removed.');
    }

    private function authorizeAccess(Archer $archer): void
    {
        $user = auth()->user();

        if (in_array($user->role, ['super_admin', 'club_admin'])) {
            return;
        }

        if ($user->role === 'archer' && $user->archer?->id === $archer->id) {
            return;
        }

        abort(403);
    }
}
