<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClubAnnouncementRead extends Model
{
    public $timestamps = false;

    protected $fillable = ['club_announcement_id', 'user_id', 'read_at'];

    protected $casts = ['read_at' => 'datetime'];

    public function announcement(): BelongsTo
    {
        return $this->belongsTo(ClubAnnouncement::class, 'club_announcement_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
