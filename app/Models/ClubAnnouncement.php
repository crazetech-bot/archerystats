<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClubAnnouncement extends Model
{
    protected $fillable = [
        'club_id', 'sender_user_id', 'coach_id',
        'title', 'body', 'audience', 'pinned',
        'send_email', 'emailed_at', 'email_count',
    ];

    protected $casts = [
        'send_email' => 'boolean',
        'pinned'     => 'boolean',
        'emailed_at' => 'datetime',
    ];

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_user_id');
    }

    public function coach(): BelongsTo
    {
        return $this->belongsTo(Coach::class)->withoutGlobalScopes();
    }

    public function reads(): HasMany
    {
        return $this->hasMany(ClubAnnouncementRead::class);
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

    /**
     * Announcements the given user is allowed to see, based on role, club
     * memberships and coach assignments. Returns null when the user has no
     * announcement context (e.g. super admin on the root domain with no club).
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
