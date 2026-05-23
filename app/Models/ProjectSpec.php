<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class ProjectSpec extends Model
{
    use HasTranslations;

    public array $translatable = ['key', 'value'];

    protected $fillable = ['project_id', 'category', 'key', 'value', 'order'];

    protected $casts = [
        'order' => 'integer',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
