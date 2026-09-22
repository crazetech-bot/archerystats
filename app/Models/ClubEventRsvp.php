<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClubEventRsvp extends Model
{
    public $timestamps = false;

    protected $fillable = ['club_event_id', 'user_id', 'response', 'responded_at'];

    protected $casts = ['responded_at' => 'datetime'];

    public const RESPONSES = ['going' => 'Going', 'maybe' => 'Maybe', 'not_going' => 'Not going'];

    public function event(): BelongsTo
    {
        return $this->belongsTo(ClubEvent::class, 'club_event_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
