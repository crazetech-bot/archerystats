<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class End extends Model
{
    protected $table = 'ends';

    protected $fillable = [
        'score_id', 'end_number', 'arrow_values', 'end_total',
    ];

    protected $casts = [
        'arrow_values' => 'array',
    ];

    public function score(): BelongsTo
    {
        return $this->belongsTo(Score::class);
    }

    public function calculateTotal(string $scoringSystem = 'standard'): int
    {
        $xPoints = ($scoringSystem === 'field') ? 6 : 10;
        $total   = 0;
        foreach ($this->arrow_values as $arrow) {
            if ($arrow === 'X') {
                $total += $xPoints;
            } elseif ($arrow !== null && $arrow !== 'M' && $arrow !== '') {
                $total += (int) $arrow;
            }
        }
        return $total;
    }
}
