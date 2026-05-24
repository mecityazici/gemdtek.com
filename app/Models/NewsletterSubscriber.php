<?php

namespace App\Models;

use App\Concerns\LogsFillableActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class NewsletterSubscriber extends Model
{
    use LogsFillableActivity;

    public const STATUS_PENDING = 'pending';

    public const STATUS_CONFIRMED = 'confirmed';

    public const STATUS_UNSUBSCRIBED = 'unsubscribed';

    public const STATUSES = [
        self::STATUS_PENDING => 'Beklemede',
        self::STATUS_CONFIRMED => 'Onaylı',
        self::STATUS_UNSUBSCRIBED => 'Abonelikten çıktı',
    ];

    protected $fillable = [
        'email', 'name', 'locale', 'status', 'confirm_token',
        'unsubscribe_token', 'confirmed_at', 'unsubscribed_at', 'source',
    ];

    protected $casts = [
        'confirmed_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $subscriber) {
            $subscriber->unsubscribe_token ??= Str::random(48);
            if ($subscriber->status === self::STATUS_PENDING && ! $subscriber->confirm_token) {
                $subscriber->confirm_token = Str::random(48);
            }
        });
    }

    public function scopeConfirmed(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_CONFIRMED);
    }

    public function scopeForLocale(Builder $query, ?string $locale): Builder
    {
        return $locale ? $query->where('locale', $locale) : $query;
    }

    public function confirm(): void
    {
        $this->forceFill([
            'status' => self::STATUS_CONFIRMED,
            'confirmed_at' => now(),
            'confirm_token' => null,
        ])->save();
    }

    public function unsubscribe(): void
    {
        $this->forceFill([
            'status' => self::STATUS_UNSUBSCRIBED,
            'unsubscribed_at' => now(),
        ])->save();
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }
}
