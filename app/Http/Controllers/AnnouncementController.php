<?php

namespace App\Http\Controllers;

use App\Mail\ClubAnnouncementMail;
use App\Models\Club;
use App\Models\ClubAnnouncement;
use App\Models\ClubAnnouncementRead;
use App\Models\Scopes\ClubScope;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    /** Role-aware announcement feed (pinned first) with read tracking. */
    public function index(): View|RedirectResponse
    {
        $user  = auth()->user();
        $query = ClubAnnouncement::visibleTo($user);

        $composeAudiences = null;   // null = cannot post
        $coachMode        = false;

        if (in_array($user->role, ['super_admin', 'club_admin'])) {
            if (! $query) {
                return redirect()->route('admin.clubs.index')
                    ->with('info', 'Open a club to view its announcements.');
            }
            $composeAudiences = ['all' => 'Everyone', 'archers' => 'Archers only', 'coaches' => 'Coaches only'];
        } elseif ($user->role === 'coach' && $user->coach) {
            $composeAudiences = ['my_archers' => 'My assigned archers'];
            $coachMode        = true;
        }

        $announcements = ($query ?? ClubAnnouncement::whereRaw('1 = 0'))
            ->with(['sender', 'coach.user', 'club'])
            ->withCount('reads')
            ->orderByDesc('pinned')
            ->orderByDesc('created_at')
            ->paginate(20);

        // Which of this page's posts had the viewer already read (drives "New" pills)…
        $pageIds = collect($announcements->items())->pluck('id');
        $readIds = ClubAnnouncementRead::where('user_id', $user->id)
            ->whereIn('club_announcement_id', $pageIds)->pluck('club_announcement_id');

        // …then mark everything on this page as read.
        $now  = now();
        $rows = $pageIds->diff($readIds)->map(fn ($id) => [
            'club_announcement_id' => $id,
            'user_id'              => $user->id,
            'read_at'              => $now,
        ])->values()->all();
        if ($rows) {
            ClubAnnouncementRead::insertOrIgnore($rows);
        }

        return view('announcements.index', compact('announcements', 'composeAudiences', 'coachMode', 'readIds'));
    }

    /** Post an announcement (club admins: club-wide; coaches: their archers). */
    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $validated = $request->validate([
            'title'      => ['required', 'string', 'max:150'],
            'body'       => ['required', 'string', 'max:5000'],
            'audience'   => ['nullable', 'in:all,archers,coaches,my_archers'],
            'send_email' => ['nullable', 'boolean'],
        ]);

        if ($user->role === 'coach') {
            $coach = $user->coach;
            if (! $coach || ! $coach->club_id) {
                return back()->with('error', 'Your coach profile has no club — cannot post announcements.');
            }
            $clubId   = $coach->club_id;
            $audience = 'my_archers';
            $coachId  = $coach->id;
        } else {
            $club = $this->resolveClub();
            if (! $club) {
                return back()->with('error', 'No club context — open this page from your club.');
            }
            $clubId   = $club->id;
            $audience = in_array($validated['audience'] ?? 'all', ['all', 'archers', 'coaches'])
                ? ($validated['audience'] ?? 'all') : 'all';
            $coachId  = null;
        }

        $announcement = ClubAnnouncement::create([
            'club_id'        => $clubId,
            'sender_user_id' => $user->id,
            'coach_id'       => $coachId,
            'title'          => $validated['title'],
            'body'           => $validated['body'],
            'audience'       => $audience,
            'send_email'     => (bool) ($validated['send_email'] ?? false),
        ]);

        $emailNote = '';
        if ($announcement->send_email) {
            $sent = $this->emailAudience($announcement);
            $announcement->update(['emailed_at' => now(), 'email_count' => $sent]);
            $emailNote = " Emailed to {$sent} member" . ($sent === 1 ? '' : 's') . '.';
        }

        return back()->with('success', 'Announcement posted.' . $emailNote);
    }

    public function destroy(ClubAnnouncement $announcement): RedirectResponse
    {
        $this->authorizeManage($announcement);

        $announcement->delete();

        return back()->with('success', 'Announcement deleted.');
    }

    /** Pin/unpin: pinned posts sort first and banner on every page until read. */
    public function togglePin(ClubAnnouncement $announcement): RedirectResponse
    {
        $this->authorizeManage($announcement);

        $announcement->update(['pinned' => ! $announcement->pinned]);

        return back()->with('success', $announcement->pinned
            ? 'Announcement pinned — members will see it on their dashboard until they read it.'
            : 'Announcement unpinned.');
    }

    private function authorizeManage(ClubAnnouncement $announcement): void
    {
        $user = auth()->user();

        $allowed = $user->role === 'super_admin'
            || ($user->role === 'club_admin' && (int) $user->club_id === (int) $announcement->club_id)
            || ($user->role === 'coach' && $user->coach && $announcement->coach_id === $user->coach->id);

        abort_unless($allowed, 403);
    }

    // ─────────────────────────────────────────────────────────────

    /** Email everyone in the announcement's audience; returns the sent count. */
    private function emailAudience(ClubAnnouncement $announcement): int
    {
        $emails = collect();

        if ($announcement->audience === 'my_archers' && $announcement->coach) {
            $members = $announcement->coach->archers()
                ->withoutGlobalScope(ClubScope::class)->with('user')->get();
            $emails  = $members->map(fn ($m) => $m->user?->email);
        } else {
            $club = $announcement->club;
            if (in_array($announcement->audience, ['all', 'archers'])) {
                $emails = $emails->merge(
                    $club->archers()->withoutGlobalScope(ClubScope::class)->with('user')->get()
                        ->map(fn ($m) => $m->user?->email)
                );
            }
            if (in_array($announcement->audience, ['all', 'coaches'])) {
                $emails = $emails->merge(
                    $club->coaches()->withoutGlobalScope(ClubScope::class)->with('user')->get()
                        ->map(fn ($m) => $m->user?->email)
                );
            }
        }

        $emails = $emails->filter()
            ->unique()
            ->reject(fn ($e) => strcasecmp($e, auth()->user()->email) === 0)
            ->values();

        $sent = 0;
        foreach ($emails as $email) {
            try {
                Mail::to($email)->send(new ClubAnnouncementMail($announcement));
                $sent++;
            } catch (\Throwable $e) {
                Log::error('Failed to send announcement email: ' . $e->getMessage());
            }
        }

        return $sent;
    }

    private function resolveClub(): ?Club
    {
        if (app()->has('currentClub')) {
            return app('currentClub');
        }
        return auth()->user()?->club;
    }
}
