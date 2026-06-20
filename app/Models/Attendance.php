<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    /** Marking states. "present" and "late" both count as attended for rate purposes. */
    public const STATUSES = ['present', 'late', 'absent', 'excused'];

    protected $fillable = [
        'training_session_id', 'archer_id', 'status',
    ];

    public function trainingSession(): BelongsTo
    {
        return $this->belongsTo(TrainingSession::class);
    }

    public function archer(): BelongsTo
    {
        return $this->belongsTo(Archer::class);
    }
}
