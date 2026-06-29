<?php

namespace App\Models;

use App\Concerns\LogsFillableActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectMember extends Model
{
    use LogsFillableActivity;

    // role tek-dilli (TR) — translatable repeater'da düzgün düzenlenemiyordu.
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
