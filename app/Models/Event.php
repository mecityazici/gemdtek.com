<?php

namespace App\Models;

use App\Concerns\LogsFillableActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class Event extends Model implements HasMedia
{
    use HasTranslations;
    use InteractsWithMedia;
    use LogsFillableActivity;

    public const CATEGORIES = [
        'zirve' => 'Zirve',
        'kariyer-gunu' => 'Kariyer Günü',
        'atolye' => 'Atölye',
        'panel' => 'Panel',
        'etkinlik' => 'Etkinlik',
    ];

    public array $translatable = ['title', 'summary', 'description'];

    protected $fillable = [
        'slug', 'title', 'summary', 'description', 'event_date',
        'location', 'category', 'registration_url', 'is_active', 'order',
        'capacity', 'registration_enabled', 'registration_deadline',
    ];

    protected $casts = [
        'event_date' => 'datetime',
        'is_active' => 'boolean',
        'order' => 'integer',
        'capacity' => 'integer',
        'registration_enabled' => 'boolean',
        'registration_deadline' => 'datetime',
    ];

    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function confirmedRegistrationsCount(): int
    {
        return $this->registrations()->where('status', EventRegistration::STATUS_CONFIRMED)->count();
    }

    public function pendingOrConfirmedCount(): int
    {
        return $this->registrations()->whereIn('status', [
            EventRegistration::STATUS_PENDING,
            EventRegistration::STATUS_CONFIRMED,
        ])->count();
    }

    public function isRegistrationOpen(): bool
    {
        if (! $this->registration_enabled) {
            return false;
        }

        if ($this->registration_deadline && now()->greaterThan($this->registration_deadline)) {
            return false;
        }

        return $this->event_date === null || $this->event_date->isFuture();
    }

    public function isFull(): bool
    {
        return $this->capacity !== null && $this->pendingOrConfirmedCount() >= $this->capacity;
    }

    public function remainingSeats(): ?int
    {
        if ($this->capacity === null) {
            return null;
        }

        return max(0, $this->capacity - $this->pendingOrConfirmedCount());
    }

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
        return __('models.event.categories.'.$this->category, [], app()->getLocale())
            ?: ($this->category);
    }

    public function getIsUpcomingAttribute(): bool
    {
        return $this->event_date?->isFuture() ?? false;
    }
}
