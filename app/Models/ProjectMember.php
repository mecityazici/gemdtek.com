<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class ProjectMember extends Model
{
    use HasTranslations;

    public array $translatable = ['role'];

    protected $fillable = ['project_id', 'name', 'role', 'linkedin_url', 'is_captain', 'order'];

    protected $casts = [
        'is_captain' => 'boolean',
        'order' => 'integer',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
