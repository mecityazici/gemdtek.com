<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class TimelineEvent extends Model
{
    use HasTranslations;

    public array $translatable = ['title', 'description'];

    protected $fillable = ['year', 'title', 'description', 'order'];

    protected $casts = [
        'year' => 'integer',
        'order' => 'integer',
    ];
}
