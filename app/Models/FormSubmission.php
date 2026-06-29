<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class FormSubmission extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = ['form_id', 'data', 'ip_address', 'user_agent'];

    protected $casts = [
        'data' => 'array',
    ];

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    public function registerMediaCollections(): void
    {
        // Private disk: başvuru ekleri (CV vb. kişisel veri) web'den doğrudan
        // erişilemez; yalnızca FormAttachmentController üzerinden, panel girişi
        // olan kullanıcılara sunulur (KVKK).
        $this->addMediaCollection('attachments')->useDisk('local');
    }

    public function attachmentFor(string $fieldName): ?Media
    {
        return $this->getMedia('attachments')
            ->firstWhere(fn ($m) => ($m->getCustomProperty('field_name') ?? null) === $fieldName);
    }
}
