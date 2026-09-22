<?php

namespace App\Models;

use App\Models\Scopes\ClubScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClubInvitation extends Model
{
    protected $fillable = [
        'club_id', 'invitable_type', 'invitable_id', 'email',
        'token', 'status', 'invited_at', 'responded_at', 'expires_at',
    ];

    protected $casts = [
        'invited_at'   => 'datetime',
        'responded_at' => 'datetime',
        'expires_at'   => 'datetime',
    ];

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function invitable(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        // manual morph — invitable_type is 'archer' or 'coach'
        return $this->morphTo();
    }

    public function getInvitableModelAttribute(): Archer|Coach|null
    {
        if (! $this->invitable_id) {
            return null; // email-only invitation (no profile yet)
        }

        // Bypass ClubScope: in tenant context the invitee is (by definition)
        // not yet a member of the current club, so scoped find() would hide them.
        return match ($this->invitable_type) {
            'archer' => Archer::withoutGlobalScope(ClubScope::class)->with('user')->find($this->invitable_id),
            'coach'  => Coach::withoutGlobalScope(ClubScope::class)->with('user')->find($this->invitable_id),
            default  => null,
        };
    }

    /** Name to show in lists/emails: profile name, else the invited address. */
    public function displayName(): string
    {
        return $this->invitable_model?->user?->name
            ?? $this->email
            ?? 'Unknown';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending' && $this->expires_at->isFuture();
    }

    public function isExpired(): bool
    {
        return $this->status === 'pending' && $this->expires_at->isPast();
    }
}
