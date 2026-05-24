<?php

namespace App\Models;

use App\Concerns\LogsFillableActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class EventRegistration extends Model
{
    use LogsFillableActivity;

    public const STATUS_PENDING = 'pending';

    public const STATUS_CONFIRMED = 'confirmed';

    public const STATUS_CANCELLED = 'cancelled';

    public const STATUS_WAITLIST = 'waitlist';

    public const STATUSES = [
        self::STATUS_PENDING => 'Onay bekliyor',
        self::STATUS_CONFIRMED => 'Onaylandı',
        self::STATUS_CANCELLED => 'İptal edildi',
        self::STATUS_WAITLIST => 'Yedek listesi',
    ];

    public const AFFILIATIONS = [
        'ogrenci' => 'Öğrenci',
        'mezun' => 'Mezun',
        'akademisyen' => 'Akademisyen',
        'sektor' => 'Sektör',
        'diger' => 'Diğer',
    ];

    protected $fillable = [
        'event_id', 'name', 'email', 'phone', 'affiliation', 'status',
        'confirm_token', 'cancel_token', 'confirmed_at', 'cancelled_at',
        'source', 'notes', 'ip_address',
    ];

    protected $casts = [
        'confirmed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $reg) {
            $reg->cancel_token ??= Str::random(48);
            if ($reg->status === self::STATUS_PENDING && ! $reg->confirm_token) {
                $reg->confirm_token = Str::random(48);
            }
        });
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function scopeConfirmed(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_CONFIRMED);
    }

    public function confirm(): void
    {
        $this->forceFill([
            'status' => self::STATUS_CONFIRMED,
            'confirmed_at' => now(),
            'confirm_token' => null,
        ])->save();
    }

    public function cancel(): void
    {
        $this->forceFill([
            'status' => self::STATUS_CANCELLED,
            'cancelled_at' => now(),
        ])->save();
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getAffiliationLabelAttribute(): ?string
    {
        return $this->affiliation ? (self::AFFILIATIONS[$this->affiliation] ?? $this->affiliation) : null;
    }
}
