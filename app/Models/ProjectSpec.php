<?php

namespace App\Models;

use App\Concerns\LogsFillableActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class ProjectSpec extends Model
{
    use HasTranslations;
    use LogsFillableActivity;

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
