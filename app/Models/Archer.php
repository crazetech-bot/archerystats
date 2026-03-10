<?php

namespace App\Models;

use App\Models\Scopes\ClubScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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

    const STATE_TEAM_OPTIONS = [
        'Johor', 'Kedah', 'Kelantan', 'Melaka', 'Negeri Sembilan',
        'Pahang', 'Perak', 'Perlis', 'Pulau Pinang', 'Sabah',
        'Sarawak', 'Selangor', 'Terengganu',
        'Kuala Lumpur', 'Labuan', 'Putrajaya',
        'Polis DiRaja Malaysia (PDRM)',
        'Angkatan Tentera Malaysia (ATM)',
        'Majlis Sukan Universiti Malaysia (MASUM)',
    ];

    const DIVISIONS = ['Recurve', 'Compound', 'Barebow', 'Traditional'];

    const NATIONAL_TEAM_OPTIONS = ['No', 'Podium', 'Pelapis Kebangsaan', 'PARA'];

    const STATUS_OPTIONS = [
        'active'           => 'Active',
        'no_longer_active' => 'No Longer Active',
        'injury'           => 'Injury',
    ];

    protected $fillable = [
        'user_id', 'club_id', 'state_team_id',
        'ref_no',
        'mareos_id', 'wareos_id', 'division', 'para_archery', 'wheelchair',
        'state_team', 'national_team',
        'date_of_birth', 'nric', 'passport_number', 'passport_expiry_date', 'place_of_birth', 'gender', 'phone',
        'team', 'hand', 'state', 'country',
        'address_line', 'postcode', 'address_state',
        'bow_style', 'divisions',
        'classification', 'photo', 'active', 'notes',
        'status', 'injury_date', 'injury_type', 'injury_return_date',
        'next_of_kin_name', 'next_of_kin_relationship', 'next_of_kin_email', 'next_of_kin_phone',
        'school', 'school_address', 'school_postcode', 'school_state',
        'arrow_type', 'arrow_size', 'arrow_length',
        'limb_type', 'limb_length', 'limb_poundage', 'actual_poundage',
        'pb_unofficial_36_score', 'pb_unofficial_36_date',
        'pb_unofficial_72_score', 'pb_unofficial_72_date',
        'pb_official_36_score',   'pb_official_36_date',   'pb_official_36_tournament',
        'pb_official_72_score',   'pb_official_72_date',   'pb_official_72_tournament',
    ];

    protected $casts = [
        'date_of_birth'           => 'date',
        'active'                  => 'boolean',
        'para_archery'            => 'boolean',
        'wheelchair'              => 'boolean',
        'divisions'               => 'array',
        'pb_unofficial_36_date'   => 'date',
        'pb_unofficial_72_date'   => 'date',
        'pb_official_36_date'     => 'date',
        'pb_official_72_date'     => 'date',
        'injury_date'             => 'date',
        'injury_return_date'      => 'date',
        'passport_expiry_date'    => 'date',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new ClubScope());

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

    public function stateTeam(): BelongsTo
    {
        return $this->belongsTo(StateTeam::class);
    }

    public function equipment(): HasMany
    {
        return $this->hasMany(Equipment::class);
    }

    public function currentEquipment(): HasOne
    {
        return $this->hasOne(Equipment::class)->where('current', true)->latestOfMany();
    }

    public function achievements(): HasMany
    {
        return $this->hasMany(ArcherAchievement::class)->orderByDesc('date');
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(ArcherySession::class);
    }

    public function clubs(): BelongsToMany
    {
        return $this->belongsToMany(Club::class, 'archer_clubs')
                    ->withPivot('primary_club', 'joined_at')
                    ->withTimestamps();
    }

    public function coaches(): BelongsToMany
    {
        return $this->belongsToMany(Coach::class, 'coach_archers')->withTimestamps();
    }

    public function trainingSessions(): BelongsToMany
    {
        return $this->belongsToMany(TrainingSession::class, 'training_session_archer')
                    ->withPivot('attended');
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
            : asset('images/default-archer.svg');
    }

    public function isProfileComplete(): bool
    {
        return !empty($this->divisions)
            && !empty($this->classification)
            && $this->date_of_birth !== null
            && !empty($this->nric)
            && !empty($this->gender)
            && !empty($this->phone);
    }
}
