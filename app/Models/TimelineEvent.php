<?php

namespace App\Models;

use App\Concerns\LogsFillableActivity;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class TimelineEvent extends Model
{
    use HasTranslations;
    use LogsFillableActivity;

    public array $translatable = ['title', 'description'];

    protected $fillable = ['year', 'title', 'description', 'order'];

    protected $casts = [
        'year' => 'integer',
        'order' => 'integer',
    ];
}
