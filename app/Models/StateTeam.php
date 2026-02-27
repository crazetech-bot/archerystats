<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StateTeam extends Model
{
    protected $fillable = [
        'name', 'state', 'description', 'contact_email', 'contact_phone',
        'website', 'address', 'founded_year', 'registration_number',
        'logo', 'active',
    ];

    protected $casts = [
        'active'       => 'boolean',
        'founded_year' => 'integer',
    ];

    public function archers(): HasMany
    {
        return $this->hasMany(Archer::class);
    }

    public function coaches(): HasMany
    {
        return $this->hasMany(Coach::class);
    }

    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo ? asset('storage/' . $this->logo) : null;
    }
}
