<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class SiteMetric extends Model
{
    use HasTranslations;

    public array $translatable = ['label'];

    protected $fillable = ['key', 'label', 'value', 'is_active', 'order'];

    protected $casts = [
        'value'     => 'integer',
        'is_active' => 'boolean',
        'order'     => 'integer',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('order');
    }
}
