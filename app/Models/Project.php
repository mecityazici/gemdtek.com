<?php

namespace App\Models;

use App\Concerns\LogsFillableActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

class Project extends Model implements HasMedia
{
    use HasTranslations;
    use InteractsWithMedia;
    use LogsFillableActivity;

    public array $translatable = ['name', 'summary', 'description', 'problem_statement'];

    public const STATUSES = [
        'active' => 'Aktif',
        'completed' => 'Tamamlandı',
        'upcoming' => 'Yaklaşan',
    ];

    public const SPEC_CATEGORIES = [
        'genel' => 'Genel',
        'mekanik' => 'Mekanik',
        'yazilim' => 'Yazılım',
        'elektronik' => 'Elektronik',
        'performans' => 'Performans',
    ];

    protected $fillable = [
        'slug', 'name', 'summary', 'description', 'problem_statement',
        'year', 'status', 'captain_user_id', 'is_active', 'order',
    ];

    protected $casts = [
        'year' => 'integer',
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('hero')->singleFile();
        $this->addMediaCollection('gallery');
        $this->addMediaCollection('documents')
            ->acceptsMimeTypes(['application/pdf']);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $collection = $media?->collection_name;

        if ($collection === 'documents') {
            return;
        }

        $this->addMediaConversion('thumb')
            ->fit(Fit::Crop, 400, 225)
            ->format('webp')
            ->nonQueued();

        $this->addMediaConversion('web')
            ->fit(Fit::Crop, 1280, 720)
            ->format('webp')
            ->nonQueued();

        if ($collection === 'hero') {
            $this->addMediaConversion('og')
                ->fit(Fit::Crop, 1200, 630)
                ->format('jpg')
                ->nonQueued();
        }
    }

    public function specs(): HasMany
    {
        return $this->hasMany(ProjectSpec::class)->orderBy('category')->orderBy('order');
    }

    public function members(): HasMany
    {
        return $this->hasMany(ProjectMember::class)->orderBy('is_captain', 'desc')->orderBy('order');
    }

    public function captainUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'captain_user_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('order');
    }

    public function getHeroUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('hero') ?: null;
    }

    public function getHeroThumbUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('hero', 'thumb') ?: $this->hero_url;
    }

    public function getHeroWebUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('hero', 'web') ?: $this->hero_url;
    }

    public function getOgImageUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('hero', 'og') ?: $this->hero_url;
    }

    public function getStatusLabelAttribute(): string
    {
        return __('models.project.statuses.'.$this->status, [], app()->getLocale())
            ?: ($this->status);
    }
}
