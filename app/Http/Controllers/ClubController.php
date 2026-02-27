<?php

namespace App\Http\Controllers;

use App\Mail\ClubInvitationMail;
use App\Models\Archer;
use App\Models\ArcherySession;
use App\Models\Club;
use App\Models\ClubInvitation;
use App\Models\Coach;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ClubController extends Controller
{
    // ── Guard ──────────────────────────────────────────────────────────────────

    private function authorizeClub(Club $club): void
    {
        $user = auth()->user();
        if ($user->role === 'club_admin' && $user->club_id !== $club->id) {
            abort(403, 'You can only manage your own club.');
        }
    }

    // ── Index (super_admin only) ───────────────────────────────────────────────

    public function index(): View
    {
        $clubs = Club::withCount(['archers', 'coaches'])
            ->orderBy('name')
            ->paginate(20);

        $totalClubs   = Club::count();
        $activeClubs  = Club::where('active', true)->count();
        $totalArchers = Archer::count();
        $totalCoaches = Coach::count();

        return view('clubs.index', compact('clubs', 'totalClubs', 'activeClubs', 'totalArchers', 'totalCoaches'));
    }

    // ── Create / Store (super_admin only) ─────────────────────────────────────

    public function create(): View
    {
        return view('clubs.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'                => ['required', 'string', 'max:150', 'unique:clubs,name'],
            'description'         => ['nullable', 'string'],
            'registration_number' => ['nullable', 'string', 'max:50'],
            'founded_year'        => ['nullable', 'integer', 'min:1900', 'max:' . date('Y')],
            'location'            => ['nullable', 'string', 'max:200'],
            'address'             => ['nullable', 'string', 'max:300'],
            'state'               => ['nullable', 'string', 'max:100'],
            'contact_email'       => ['nullable', 'email', 'max:150'],
            'contact_phone'       => ['nullable', 'string', 'max:30'],
            'website'             => ['nullable', 'url', 'max:200'],
            'logo'                => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'active'              => ['nullable', 'boolean'],
        ]);

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('clubs', 'public');
        }

        $club = Club::create(array_merge($validated, [
            'logo'   => $logoPath,
            'active' => (bool) ($validated['active'] ?? true),
        ]));

        return redirect()->route('clubs.show', $club)
            ->with('success', 'Club created successfully.');
    }

    // ── Show ──────────────────────────────────────────────────────────────────

    public function show(Club $club): View
    {
        $this->authorizeClub($club);

        $club->loadCount(['archers', 'coaches']);
        $club->load([
            'coaches' => fn($q) => $q->with('user')->orderBy('id'),
            'archers' => fn($q) => $q->with('user')->orderBy('id'),
        ]);

        $sessionsThisMonth = ArcherySession::whereIn('archer_id', $club->archers->pluck('id'))
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->count();

        return view('clubs.show', compact('club', 'sessionsThisMonth'));
    }

    // ── Edit / Update ─────────────────────────────────────────────────────────

    public function edit(Club $club): View
    {
        $this->authorizeClub($club);
        return view('clubs.edit', compact('club'));
    }

    public function update(Request $request, Club $club): RedirectResponse
    {
        $this->authorizeClub($club);

        $validated = $request->validate([
            'name'                => ['required', 'string', 'max:150', 'unique:clubs,name,' . $club->id],
            'description'         => ['nullable', 'string'],
            'registration_number' => ['nullable', 'string', 'max:50'],
            'founded_year'        => ['nullable', 'integer', 'min:1900', 'max:' . date('Y')],
            'location'            => ['nullable', 'string', 'max:200'],
            'address'             => ['nullable', 'string', 'max:300'],
            'state'               => ['nullable', 'string', 'max:100'],
            'contact_email'       => ['nullable', 'email', 'max:150'],
            'contact_phone'       => ['nullable', 'string', 'max:30'],
            'website'             => ['nullable', 'url', 'max:200'],
            'logo'                => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'active'              => ['nullable', 'boolean'],
        ]);

        $logoPath = $club->logo;
        if ($request->hasFile('logo')) {
            if ($club->logo) {
                Storage::disk('public')->delete($club->logo);
            }
            $logoPath = $request->file('logo')->store('clubs', 'public');
        }

        $club->update(array_merge($validated, [
            'logo'   => $logoPath,
            'active' => $request->boolean('active'),
        ]));

        return redirect()->route('clubs.show', $club)
            ->with('success', 'Club updated successfully.');
    }

    // ── Dashboard ─────────────────────────────────────────────────────────────

    public function dashboard(Club $club, Request $request): View
    {
        $this->authorizeClub($club);

        $club->load('archers.user');

        // Date range
        $range = $request->input('range', 'last30days');
        [$from, $to] = $this->resolveDateRange($range, $request);

        $archerIds      = $club->archers->pluck('id');
        $archerFilter   = $request->input('archer_id');

        // All sessions in period (used for charts + leaderboard)
        $sessions = ArcherySession::with(['archer.user', 'roundType', 'score'])
            ->whereIn('archer_id', $archerIds)
            ->whereHas('score')
            ->whereBetween('date', [$from, $to])
            ->orderBy('date')
            ->get();

        // Filtered sessions for the results table
        $resultSessions = $archerFilter
            ? $sessions->where('archer_id', $archerFilter)->sortByDesc('date')->values()
            : $sessions->sortByDesc('date')->values();

        // Summary stats
        $totalArchers  = $club->archers->count();
        $activeArchers = $sessions->pluck('archer_id')->unique()->count();
        $bestScore     = $sessions->max(fn($s) => $s->score?->total_score ?? 0) ?: 0;
        $clubAvg       = $sessions->count() > 0
            ? round($sessions->avg(fn($s) => $s->score?->total_score ?? 0), 1)
            : 0;

        // Per-archer leaderboard stats
        $archerStats = $club->archers->map(function ($archer) use ($sessions) {
            $archerSessions = $sessions->where('archer_id', $archer->id);
            $totalHits      = $archerSessions->sum(fn($s) => $s->score?->hit_count ?? 0);
            $totalMisses    = $archerSessions->sum(fn($s) => $s->score?->miss_count ?? 0);
            $totalArrows    = $totalHits + $totalMisses;
            return [
                'archer'   => $archer,
                'sessions' => $archerSessions->count(),
                'best'     => $archerSessions->max(fn($s) => $s->score?->total_score ?? 0) ?: 0,
                'avg'      => $archerSessions->count() > 0
                    ? round($archerSessions->avg(fn($s) => $s->score?->total_score ?? 0), 1) : 0,
                'hit_rate' => $totalArrows > 0 ? round($totalHits / $totalArrows * 100, 1) : 0,
            ];
        })->sortByDesc('avg')->values();

        // Chart 1: Club avg score trend per date
        $grouped     = $sessions->groupBy(fn($s) => $s->date->format('d M Y'));
        $trendLabels = $grouped->keys()->toArray();
        $trendData   = $grouped->map(fn($g) => round($g->avg(fn($s) => $s->score?->total_score ?? 0), 1))->values()->toArray();

        // Chart 2: Top 8 archers leaderboard (horizontal bar)
        $leaderboard = $archerStats->take(8);

        return view('clubs.dashboard', compact(
            'club', 'range', 'from', 'to', 'archerFilter',
            'totalArchers', 'activeArchers', 'bestScore', 'clubAvg',
            'archerStats', 'trendLabels', 'trendData',
            'leaderboard', 'resultSessions',
        ));
    }

    // ── Members ───────────────────────────────────────────────────────────────

    public function members(Club $club): View
    {
        $this->authorizeClub($club);

        $club->load(['archers.user', 'coaches.user']);

        // Pending invitations
        $pendingInvitations = ClubInvitation::where('club_id', $club->id)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->with('club')
            ->get()
            ->map(fn($inv) => array_merge(['invitation' => $inv], ['invitable' => $inv->invitable_model]));

        // Archers not in this club (for invite dropdown)
        $availableArchers = Archer::with('user', 'club')
            ->whereDoesntHave('user', fn($q) => $q->whereNull('id')) // ensure user exists
            ->whereHas('user')
            ->orderBy('id')
            ->get()
            ->filter(fn($a) => $a->club_id !== $club->id);

        // Coaches not in this club
        $availableCoaches = Coach::with('user', 'club')
            ->whereHas('user')
            ->orderBy('id')
            ->get()
            ->filter(fn($c) => $c->club_id !== $club->id);

        return view('clubs.members', compact(
            'club', 'pendingInvitations', 'availableArchers', 'availableCoaches'
        ));
    }

    // ── Invite / Add Archer ───────────────────────────────────────────────────

    public function inviteArcher(Club $club, Archer $archer, Request $request): RedirectResponse
    {
        $this->authorizeClub($club);

        if (! $archer->user) {
            return back()->with('error', 'This archer has no user account to send an invitation to.');
        }

        // Cancel any existing pending invitation for this archer to this club
        ClubInvitation::where('club_id', $club->id)
            ->where('invitable_type', 'archer')
            ->where('invitable_id', $archer->id)
            ->where('status', 'pending')
            ->update(['status' => 'declined', 'responded_at' => now()]);

        $token = Str::uuid()->toString();
        $invitation = ClubInvitation::create([
            'club_id'        => $club->id,
            'invitable_type' => 'archer',
            'invitable_id'   => $archer->id,
            'token'          => $token,
            'status'         => 'pending',
            'invited_at'     => now(),
            'expires_at'     => now()->addHours(72),
        ]);

        $acceptUrl  = route('club-invitations.accept',  $token);
        $declineUrl = route('club-invitations.decline', $token);

        Mail::to($archer->user->email)->send(
            new ClubInvitationMail($invitation, $archer->full_name, $acceptUrl, $declineUrl)
        );

        return back()->with('success', 'Invitation sent to ' . $archer->full_name . '.');
    }

    public function removeArcher(Club $club, Archer $archer): RedirectResponse
    {
        $this->authorizeClub($club);

        if ($archer->club_id === $club->id) {
            $archer->update(['club_id' => null]);
            $archer->user?->update(['club_id' => null]);
        }

        return back()->with('success', $archer->full_name . ' removed from the club.');
    }

    // ── Invite / Add Coach ────────────────────────────────────────────────────

    public function inviteCoach(Club $club, Coach $coach): RedirectResponse
    {
        $this->authorizeClub($club);

        if (! $coach->user) {
            return back()->with('error', 'This coach has no user account to send an invitation to.');
        }

        ClubInvitation::where('club_id', $club->id)
            ->where('invitable_type', 'coach')
            ->where('invitable_id', $coach->id)
            ->where('status', 'pending')
            ->update(['status' => 'declined', 'responded_at' => now()]);

        $token = Str::uuid()->toString();
        $invitation = ClubInvitation::create([
            'club_id'        => $club->id,
            'invitable_type' => 'coach',
            'invitable_id'   => $coach->id,
            'token'          => $token,
            'status'         => 'pending',
            'invited_at'     => now(),
            'expires_at'     => now()->addHours(72),
        ]);

        $acceptUrl  = route('club-invitations.accept',  $token);
        $declineUrl = route('club-invitations.decline', $token);

        Mail::to($coach->user->email)->send(
            new ClubInvitationMail($invitation, $coach->full_name, $acceptUrl, $declineUrl)
        );

        return back()->with('success', 'Invitation sent to ' . $coach->full_name . '.');
    }

    public function removeCoach(Club $club, Coach $coach): RedirectResponse
    {
        $this->authorizeClub($club);

        if ($coach->club_id === $club->id) {
            $coach->update(['club_id' => null]);
            $coach->user?->update(['club_id' => null]);
        }

        return back()->with('success', $coach->full_name . ' removed from the club.');
    }

    // ── Destroy (super_admin only) ────────────────────────────────────────────

    public function destroy(Club $club): RedirectResponse
    {
        if ($club->logo) {
            Storage::disk('public')->delete($club->logo);
        }
        $club->delete();

        return redirect()->route('clubs.index')
            ->with('success', 'Club deleted.');
    }

    // ── CSV Import ────────────────────────────────────────────────────────────

    public function importForm(): View
    {
        return view('clubs.import');
    }

    public function importTemplate(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $columns = ['name', 'state', 'location', 'address', 'registration_number',
                    'founded_year', 'contact_email', 'contact_phone', 'website', 'active'];

        return response()->stream(function () use ($columns) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $columns);
            // Example row
            fputcsv($out, ['Selangor Archery Club', 'Selangor', 'Shah Alam', '123 Jalan Utama', 'REG-001', '2010', 'info@sac.com', '0123456789', 'https://sac.com', '1']);
            fclose($out);
        }, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="clubs_template.csv"',
        ]);
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
        ]);

        $handle  = fopen($request->file('csv_file')->getRealPath(), 'r');
        $headers = array_map('trim', fgetcsv($handle));

        $imported = 0;
        $skipped  = 0;
        $errors   = [];
        $row      = 1;

        while (($data = fgetcsv($handle)) !== false) {
            $row++;

            if (count($data) !== count($headers)) {
                $errors[] = "Row {$row}: column count mismatch — skipped.";
                $skipped++;
                continue;
            }

            $record = array_combine($headers, array_map('trim', $data));
            $name   = $record['name'] ?? '';

            if ($name === '') {
                $errors[] = "Row {$row}: name is required — skipped.";
                $skipped++;
                continue;
            }

            if (Club::where('name', $name)->exists()) {
                $errors[] = "Row {$row}: \"{$name}\" already exists — skipped.";
                $skipped++;
                continue;
            }

            $foundedYear = !empty($record['founded_year']) ? (int) $record['founded_year'] : null;
            if ($foundedYear && ($foundedYear < 1900 || $foundedYear > (int) date('Y'))) {
                $foundedYear = null;
            }

            Club::create([
                'name'                => $name,
                'state'               => $record['state'] ?: null,
                'location'            => $record['location'] ?: null,
                'address'             => $record['address'] ?: null,
                'registration_number' => $record['registration_number'] ?: null,
                'founded_year'        => $foundedYear,
                'contact_email'       => $record['contact_email'] ?: null,
                'contact_phone'       => $record['contact_phone'] ?: null,
                'website'             => $record['website'] ?: null,
                'active'              => isset($record['active']) && $record['active'] === '0' ? false : true,
            ]);

            $imported++;
        }

        fclose($handle);

        return redirect()->route('clubs.index')
            ->with('import_result', compact('imported', 'skipped', 'errors'));
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function resolveDateRange(string $range, Request $request): array
    {
        $today = Carbon::today();
        return match ($range) {
            'last7days'  => [$today->copy()->subDays(6)->toDateString(), $today->toDateString()],
            'this_year'  => [Carbon::create($today->year, 1, 1)->toDateString(), $today->toDateString()],
            'last_year'  => [
                Carbon::create($today->year - 1, 1, 1)->toDateString(),
                Carbon::create($today->year - 1, 12, 31)->toDateString(),
            ],
            'custom'     => [
                $request->input('from', $today->copy()->subDays(29)->toDateString()),
                $request->input('to',   $today->toDateString()),
            ],
            default      => [$today->copy()->subDays(29)->toDateString(), $today->toDateString()], // last30days
        };
    }
}
