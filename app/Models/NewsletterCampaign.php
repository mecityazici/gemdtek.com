<?php

namespace App\Models;

use App\Concerns\LogsFillableActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class NewsletterCampaign extends Model
{
    use HasTranslations;
    use LogsFillableActivity;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_SENDING = 'sending';

    public const STATUS_SENT = 'sent';

    public const STATUSES = [
        self::STATUS_DRAFT => 'Taslak',
        self::STATUS_SENDING => 'Gönderiliyor',
        self::STATUS_SENT => 'Gönderildi',
    ];

    public array $translatable = ['subject', 'body'];

    protected $fillable = [
        'subject', 'body', 'audience_locale', 'status',
        'scheduled_for', 'sent_at', 'recipients_count', 'sent_by',
    ];

    protected $casts = [
        'scheduled_for' => 'datetime',
        'sent_at' => 'datetime',
        'recipients_count' => 'integer',
    ];

    public function sentBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    public function scopeSent(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_SENT);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function isSendable(): bool
    {
        return in_array($this->status, [self::STATUS_DRAFT], true);
    }
}
