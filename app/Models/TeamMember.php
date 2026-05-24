<?php

namespace App\Models;

use App\Concerns\LogsFillableActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class TeamMember extends Model implements HasMedia
{
    use HasTranslations;
    use InteractsWithMedia;
    use LogsFillableActivity;

    public array $translatable = ['position', 'bio'];

    protected $fillable = ['name', 'position', 'bio', 'linkedin_url', 'is_active', 'order'];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('photo')->singleFile();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('order');
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('photo') ?: null;
    }
}
