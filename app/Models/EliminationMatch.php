<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EliminationMatch extends Model
{
    protected $fillable = [
        'training_session_id',
        'archer_a_id', 'archer_a_name', 'archer_b_id', 'archer_b_name',
        'category', 'format', 'distance_m', 'date', 'location',
        'competition_name', 'status', 'winner_id', 'shoot_off',
        'shoot_off_winner_id', 'arrow_values', 'shoot_off_a', 'shoot_off_b', 'nearest_to_center',
    ];

    protected $casts = [
        'date'         => 'date',
        'shoot_off'    => 'boolean',
        'arrow_values' => 'array',
    ];

    public function archerA(): BelongsTo
    {
        return $this->belongsTo(Archer::class, 'archer_a_id');
    }

    public function archerB(): BelongsTo
    {
        return $this->belongsTo(Archer::class, 'archer_b_id');
    }

    public function winner(): BelongsTo
    {
        return $this->belongsTo(Archer::class, 'winner_id');
    }

    public function shootOffWinner(): BelongsTo
    {
        return $this->belongsTo(Archer::class, 'shoot_off_winner_id');
    }

    public function trainingSession(): BelongsTo
    {
        return $this->belongsTo(TrainingSession::class);
    }
}
