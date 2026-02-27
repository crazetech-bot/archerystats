<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Club extends Model
{
    protected $fillable = [
        'name', 'description', 'location', 'contact_email', 'contact_phone',
        'website', 'address', 'state', 'founded_year', 'registration_number',
        'logo', 'active',
    ];

    protected $casts = [
        'active'       => 'boolean',
        'founded_year' => 'integer',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function archers(): HasMany
    {
        return $this->hasMany(Archer::class);
    }

    public function coaches(): HasMany
    {
        return $this->hasMany(Coach::class);
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(ClubInvitation::class);
    }

    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo ? asset('storage/' . $this->logo) : null;
    }
}
