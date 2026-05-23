<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Sponsor extends Model implements HasMedia
{
    use InteractsWithMedia;

    public const TIERS = [
        'platinum' => 'Platin',
        'gold'     => 'Altın',
        'silver'   => 'Gümüş',
        'bronze'   => 'Bronz',
        'destek'   => 'Destekleyen',
    ];

    protected $fillable = ['name', 'url', 'tier', 'is_active', 'order'];

    protected $casts = [
        'is_active' => 'boolean',
        'order'     => 'integer',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')->singleFile();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('order');
    }

    public function getLogoUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('logo') ?: null;
    }
}
