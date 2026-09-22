<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClubEvent extends Model
{
    protected $fillable = [
        'club_id', 'created_by_user_id', 'coach_id',
        'title', 'description', 'location', 'event_type',
        'starts_at', 'ends_at', 'audience', 'pinned',
        'send_email', 'emailed_at', 'email_count',
    ];

    protected $casts = [
        'starts_at'  => 'datetime',
        'ends_at'    => 'datetime',
        'pinned'     => 'boolean',
        'send_email' => 'boolean',
        'emailed_at' => 'datetime',
    ];

    public const TYPES = [
        'training'    => 'Training',
        'competition' => 'Competition',
        'meeting'     => 'Meeting',
        'social'      => 'Social',
        'other'       => 'Other',
    ];

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function coach(): BelongsTo
    {
        return $this->belongsTo(Coach::class)->withoutGlobalScopes();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function rsvps(): HasMany
    {
        return $this->hasMany(ClubEventRsvp::class);
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->event_type] ?? 'Event';
    }

    public function audienceLabel(): string
    {
        return match ($this->audience) {
            'archers'    => 'Archers',
            'coaches'    => 'Coaches',
            'my_archers' => "Coach's Archers",
            default      => 'Everyone',
        };
    }

    public function isPast(): bool
    {
        return ($this->ends_at ?? $this->starts_at)->isPast();
    }

    /**
     * Events the given user may see, by role, club memberships and coach
     * assignments. Null when the user has no event context. Mirrors
     * ClubAnnouncement::visibleTo so the two modules stay consistent.
     */
    public static function visibleTo(User $user, ?Club $adminClub = null): ?Builder
    {
        $query = static::query();

        if (in_array($user->role, ['super_admin', 'club_admin'])) {
            $club = $adminClub
                ?? (app()->has('currentClub') ? app('currentClub') : null)
                ?? $user->club;
            if (! $club) {
                return null;
            }
            return $query->where('club_id', $club->id);
        }

        if ($user->role === 'coach' && $user->coach) {
            $coach   = $user->coach;
            $clubIds = $coach->clubs()->pluck('clubs.id')
                ->when($coach->club_id, fn ($c) => $c->push($coach->club_id))
                ->unique()->values();

            return $query->where(function ($q) use ($clubIds, $coach) {
                $q->where(fn ($qq) => $qq->whereIn('club_id', $clubIds)
                                         ->whereIn('audience', ['all', 'coaches']))
                  ->orWhere('coach_id', $coach->id);
            });
        }

        if ($user->archer) {
            $archer   = $user->archer;
            $clubIds  = $archer->clubs()->pluck('clubs.id')
                ->when($archer->club_id, fn ($c) => $c->push($archer->club_id))
                ->unique()->values();
            $coachIds = $archer->coaches()->pluck('coaches.id');

            return $query->where(function ($q) use ($clubIds, $coachIds) {
                $q->where(fn ($qq) => $qq->whereIn('club_id', $clubIds)
                                         ->whereIn('audience', ['all', 'archers']))
                  ->orWhere(fn ($qq) => $qq->where('audience', 'my_archers')
                                           ->whereIn('coach_id', $coachIds));
            });
        }

        return null;
    }
}
