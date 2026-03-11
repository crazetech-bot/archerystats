<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Archer extends Model
{
    const MALAYSIAN_STATES = [
        'Johor', 'Kedah', 'Kelantan', 'Melaka', 'Negeri Sembilan',
        'Pahang', 'Perak', 'Perlis', 'Pulau Pinang', 'Sabah',
        'Sarawak', 'Selangor', 'Terengganu',
        'Kuala Lumpur', 'Labuan', 'Putrajaya',
    ];

    const DIVISIONS = ['Recurve', 'Compound', 'Barebow', 'Traditional'];

    protected $fillable = [
        'user_id', 'club_id',
        'ref_no',
        'date_of_birth', 'gender', 'phone',
        'team', 'state', 'country',
        'address_line', 'postcode', 'address_state',
        'bow_style', 'divisions',
        'classification', 'photo', 'active', 'notes',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'active'        => 'boolean',
        'divisions'     => 'array',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::created(function (Archer $archer) {
            $archer->updateQuietly([
                'ref_no' => 'ARCH-' . str_pad($archer->id, 5, '0', STR_PAD_LEFT),
            ]);
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function equipment(): HasMany
    {
        return $this->hasMany(Equipment::class);
    }

    public function currentEquipment(): HasOne
    {
        return $this->hasOne(Equipment::class)->where('current', true)->latestOfMany();
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(ArcherySession::class);
    }

    public function getAgeAttribute(): ?int
    {
        return $this->date_of_birth?->age;
    }

    public function getFullNameAttribute(): string
    {
        return $this->user->name;
    }

    public function getDivisionsLabelAttribute(): string
    {
        return !empty($this->divisions) ? implode(', ', $this->divisions) : '—';
    }

    public function getPhotoUrlAttribute(): string
    {
        return $this->photo
            ? asset('storage/' . $this->photo)
            : asset('images/default-archer.png');
    }
}
