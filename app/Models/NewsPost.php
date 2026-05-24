<?php

namespace App\Models;

use App\Concerns\LogsFillableActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

class NewsPost extends Model implements HasMedia
{
    use HasTranslations;
    use InteractsWithMedia;
    use LogsFillableActivity;

    public const CATEGORIES = [
        'duyuru' => 'Duyuru',
        'blog' => 'Blog',
        'basin' => 'Basında Biz',
    ];

    public array $translatable = ['title', 'excerpt', 'content'];

    protected $fillable = [
        'slug', 'title', 'excerpt', 'content',
        'published_at', 'category', 'is_published', 'order',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_published' => 'boolean',
        'order' => 'integer',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cover')->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->fit(Fit::Crop, 400, 225)
            ->format('webp')
            ->nonQueued();

        $this->addMediaConversion('web')
            ->fit(Fit::Crop, 1280, 720)
            ->format('webp')
            ->nonQueued();

        $this->addMediaConversion('og')
            ->fit(Fit::Crop, 1200, 630)
            ->format('jpg')
            ->nonQueued();
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('is_published', true)
            ->where(function ($q) {
                $q->whereNull('published_at')->orWhere('published_at', '<=', now());
            })
            ->orderByDesc('published_at');
    }

    public function getCoverUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('cover') ?: null;
    }

    public function getCoverThumbUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('cover', 'thumb') ?: $this->cover_url;
    }

    public function getCoverWebUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('cover', 'web') ?: $this->cover_url;
    }

    public function getOgImageUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('cover', 'og') ?: $this->cover_url;
    }

    public function getCategoryLabelAttribute(): string
    {
        return __('models.news.categories.'.$this->category, [], app()->getLocale())
            ?: ($this->category);
    }
}
