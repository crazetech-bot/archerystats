<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coach extends Model
{
    const COACHING_LEVELS = [
        'None',
        'Kursus Asas Kejurulatihan',
        'Level 1',
        'Level 2',
        'Level 3',
    ];

    const SPORTS_SCIENCE_COURSES = [
        'Kursus Sains Sukan Tahap 1',
        'Kursus Sains Sukan Tahap 2',
        'Kursus Sains Sukan Tahap 3',
    ];

    const MALAYSIAN_STATES = [
        'Johor', 'Kedah', 'Kelantan', 'Melaka', 'Negeri Sembilan',
        'Pahang', 'Perak', 'Perlis', 'Pulau Pinang', 'Sabah',
        'Sarawak', 'Selangor', 'Terengganu',
        'Kuala Lumpur', 'Labuan', 'Putrajaya',
    ];

    protected $fillable = [
        'user_id', 'club_id', 'state_team_id',
        'ref_no',
        'date_of_birth', 'gender', 'phone',
        'team', 'national_team', 'coaching_level', 'sports_science_course', 'state', 'country',
        'address_line', 'postcode',
        'photo', 'active', 'notes',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'active'        => 'boolean',
        'national_team' => 'boolean',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::created(function (Coach $coach) {
            $coach->updateQuietly([
                'ref_no' => 'COACH-' . str_pad($coach->id, 5, '0', STR_PAD_LEFT),
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

    public function stateTeam(): BelongsTo
    {
        return $this->belongsTo(StateTeam::class);
    }

    public function archers(): BelongsToMany
    {
        return $this->belongsToMany(Archer::class, 'coach_archers')->withTimestamps();
    }

    public function trainingSessions(): HasMany
    {
        return $this->hasMany(TrainingSession::class);
    }

    public function clubArchers(): HasMany
    {
        return $this->hasMany(Archer::class, 'club_id', 'club_id');
    }

    public function getAgeAttribute(): ?int
    {
        return $this->date_of_birth?->age;
    }

    public function getFullNameAttribute(): string
    {
        return $this->user->name;
    }

    public function getPhotoUrlAttribute(): string
    {
        return $this->photo
            ? asset('storage/' . $this->photo)
            : asset('images/default-coach.svg');
    }

    public function isProfileComplete(): bool
    {
        return !empty($this->gender)
            && !empty($this->phone)
            && $this->date_of_birth !== null;
    }
}
