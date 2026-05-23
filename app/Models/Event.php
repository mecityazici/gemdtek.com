<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class Event extends Model implements HasMedia
{
    use HasTranslations;
    use InteractsWithMedia;

    public const CATEGORIES = [
        'zirve'         => 'Zirve',
        'kariyer-gunu'  => 'Kariyer Günü',
        'atolye'        => 'Atölye',
        'panel'         => 'Panel',
        'etkinlik'      => 'Etkinlik',
    ];

    public array $translatable = ['title', 'summary', 'description'];

    protected $fillable = [
        'slug', 'title', 'summary', 'description', 'event_date',
        'location', 'category', 'registration_url', 'is_active', 'order',
    ];

    protected $casts = [
        'event_date' => 'datetime',
        'is_active'  => 'boolean',
        'order'      => 'integer',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cover')->singleFile();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('event_date', '>=', now())->orderBy('event_date');
    }

    public function scopePast(Builder $query): Builder
    {
        return $query->where('event_date', '<', now())->orderByDesc('event_date');
    }

    public function getCoverUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('cover') ?: null;
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }

    public function getIsUpcomingAttribute(): bool
    {
        return $this->event_date?->isFuture() ?? false;
    }
}
