<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Club extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'tagline', 'location',
        'contact_email', 'contact_phone',
        'website', 'address', 'state', 'founded_year', 'registration_number',
        'facebook_url', 'instagram_url', 'whatsapp_number',
        'logo', 'active',
    ];

    protected static function booted(): void
    {
        static::creating(function (Club $club) {
            if (empty($club->slug)) {
                $club->slug = static::uniqueSlug($club->name);
            }
        });
    }

    public static function uniqueSlug(string $name, ?int $excludeId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i    = 2;
        while (static::where('slug', $slug)->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))->exists()) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }

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
