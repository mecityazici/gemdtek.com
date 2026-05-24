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

class Alumni extends Model implements HasMedia
{
    use HasTranslations;
    use InteractsWithMedia;
    use LogsFillableActivity;

    protected $table = 'alumni';

    public const SECTORS = [
        'tersane' => 'Tersane',
        'klas' => 'Klas Kuruluşu',
        'tasarim-ofisi' => 'Tasarım Ofisi',
        'armator' => 'Armatör',
        'akademik' => 'Akademik',
        'yazilim' => 'Yazılım / Otomasyon',
        'diger' => 'Diğer',
    ];

    public array $translatable = ['position', 'bio'];

    protected $fillable = [
        'name', 'position', 'bio', 'graduation_year', 'sector',
        'company', 'city', 'linkedin_url', 'is_public', 'order',
    ];

    protected $casts = [
        'graduation_year' => 'integer',
        'is_public' => 'boolean',
        'order' => 'integer',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('photo')->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->fit(Fit::Crop, 160, 160)
            ->format('webp')
            ->nonQueued();

        $this->addMediaConversion('web')
            ->fit(Fit::Crop, 320, 320)
            ->format('webp')
            ->nonQueued();
    }

    public function scopePublic(Builder $query): Builder
    {
        return $query->where('is_public', true);
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('photo') ?: null;
    }

    public function getPhotoThumbUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('photo', 'thumb') ?: $this->photo_url;
    }

    public function getPhotoWebUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('photo', 'web') ?: $this->photo_url;
    }

    public function getSectorLabelAttribute(): string
    {
        return __('models.alumni.sectors.'.$this->sector, [], app()->getLocale())
            ?: $this->sector;
    }
}
