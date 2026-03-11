<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Equipment extends Model
{
    protected $fillable = [
        'archer_id', 'bow_brand', 'bow_model', 'bow_type', 'draw_weight', 'draw_length',
        'arrow_brand', 'arrow_model', 'arrow_spine', 'arrow_length', 'arrow_weight',
        'sight', 'stabilizer', 'arrow_rest', 'release_aid', 'notes', 'current',
    ];

    protected $casts = [
        'current'      => 'boolean',
        'draw_weight'  => 'decimal:1',
        'draw_length'  => 'decimal:1',
        'arrow_length' => 'decimal:1',
        'arrow_weight' => 'decimal:1',
    ];

    public function archer(): BelongsTo
    {
        return $this->belongsTo(Archer::class);
    }
}
