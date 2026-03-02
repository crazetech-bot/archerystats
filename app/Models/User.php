<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'is_coach', 'club_id', 'status',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'is_coach'          => 'boolean',
    ];

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function archer(): HasOne
    {
        return $this->hasOne(Archer::class);
    }

    public function coach(): HasOne
    {
        return $this->hasOne(Coach::class);
    }

    public function managedStateTeam(): HasOne
    {
        return $this->hasOne(\App\Models\StateTeam::class, 'admin_user_id');
    }

    public function hasRole(string|array $roles): bool
    {
        $roles = (array) $roles;

        if (in_array($this->role, $roles)) {
            return true;
        }

        // A user with is_coach=true retains coach-level access even after promotion
        if ($this->is_coach && in_array('coach', $roles)) {
            return true;
        }

        return false;
    }

    public function isAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isClubAdmin(): bool
    {
        return in_array($this->role, ['super_admin', 'club_admin']);
    }

    public function isCoach(): bool
    {
        return $this->is_coach || in_array($this->role, ['super_admin', 'club_admin', 'coach']);
    }

    public function isArcher(): bool
    {
        return $this->role === 'archer';
    }

    public function isStateAdmin(): bool
    {
        return in_array($this->role, ['super_admin', 'state_admin'])
            || $this->managedStateTeam()->exists();
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }
}
