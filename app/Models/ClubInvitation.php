<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClubInvitation extends Model
{
    protected $fillable = [
        'club_id', 'invitable_type', 'invitable_id',
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
        return match ($this->invitable_type) {
            'archer' => Archer::with('user')->find($this->invitable_id),
            'coach'  => Coach::with('user')->find($this->invitable_id),
            default  => null,
        };
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
