<?php

namespace App\Models;

use App\Concerns\LogsFillableActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectSpec extends Model
{
    use LogsFillableActivity;

    // key/value tek-dilli (TR). Translatable repeater'da düzgün düzenlenemediği için
    // Form/FormField gibi tek dile çekildi. Bkz. 2026_06_30 flatten migration'ı.
    protected $fillable = ['project_id', 'category', 'key', 'value', 'order'];

    protected $casts = [
        'order' => 'integer',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
