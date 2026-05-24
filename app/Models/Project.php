<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class Project extends Model implements HasMedia
{
    use HasTranslations;
    use InteractsWithMedia;

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

    public function getStatusLabelAttribute(): string
    {
        return __('models.project.statuses.'.$this->status, [], app()->getLocale())
            ?: ($this->status);
    }
}
