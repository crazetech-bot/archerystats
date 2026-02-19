<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function get(string $key, mixed $default = null): mixed
    {
        return static::where('key', $key)->value('value') ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        cache()->forget('site_settings');
    }

    public static function getAllCached(): array
    {
        return cache()->remember('site_settings', 3600, function () {
            return static::pluck('value', 'key')->toArray();
        });
    }
}
