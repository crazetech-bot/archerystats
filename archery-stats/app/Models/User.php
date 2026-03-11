<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'club_id',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function archer(): HasOne
    {
        return $this->hasOne(Archer::class);
    }

    public function hasRole(string|array $roles): bool
    {
        return in_array($this->role, (array) $roles);
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
        return in_array($this->role, ['super_admin', 'club_admin', 'coach']);
    }

    public function isArcher(): bool
    {
        return $this->role === 'archer';
    }
}
