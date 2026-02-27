<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CoachArcherInvitation extends Model
{
    protected $fillable = [
        'coach_id', 'archer_id', 'token', 'status', 'expires_at', 'responded_at',
    ];

    protected $casts = [
        'expires_at'   => 'datetime',
        'responded_at' => 'datetime',
    ];

    public function coach(): BelongsTo
    {
        return $this->belongsTo(Coach::class);
    }

    public function archer(): BelongsTo
    {
        return $this->belongsTo(Archer::class);
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
