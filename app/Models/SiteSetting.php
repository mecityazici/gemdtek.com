<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class SiteSetting extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = ['key', 'value', 'group', 'type', 'label'];

    public const CACHE_TTL = 3600;

    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember("setting:{$key}", self::CACHE_TTL, function () use ($key, $default) {
            $row = static::where('key', $key)->first();

            if (! $row) {
                return $default;
            }

            // Image type → public URL from media library
            if ($row->type === 'image') {
                return $row->getFirstMediaUrl('image') ?: $default;
            }

            return $row->value ?? $default;
        });
    }

    public static function set(string $key, mixed $value, string $group = 'general', string $type = 'text'): self
    {
        $row = static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group, 'type' => $type],
        );
        Cache::forget("setting:{$key}");

        return $row;
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')->singleFile();
    }
}
