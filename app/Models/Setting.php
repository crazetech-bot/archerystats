<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'club_id'];

    // ── Platform-scoped helpers (club_id = NULL) ──────────────────────────

    public static function get(string $key, mixed $default = null, ?int $clubId = null): mixed
    {
        return static::where('key', $key)->where('club_id', $clubId)->value('value') ?? $default;
    }

    public static function set(string $key, mixed $value, ?int $clubId = null): void
    {
        static::updateOrCreate(
            ['key' => $key, 'club_id' => $clubId],
            ['value' => $value]
        );
        cache()->forget('site_settings_' . ($clubId ?? 'platform'));
    }

    public static function getAllCached(?int $clubId = null): array
    {
        $cacheKey = 'site_settings_' . ($clubId ?? 'platform');

        return cache()->remember($cacheKey, 3600, function () use ($clubId) {
            return static::where('club_id', $clubId)->pluck('value', 'key')->toArray();
        });
    }
}
