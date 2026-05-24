<?php

namespace App\Concerns;

use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Tek satırla bir modele activity log ekler:
 * - Tüm fillable alanları izler
 * - Yalnızca değişen alanları kaydeder
 * - Hiçbir alan değişmediyse log yazmaz
 *
 * Translatable JSON alanları için log değeri raw JSON olur; admin
 * panelinde görüntülerken bu yeterince anlamlı (locale değiştiyse
 * anahtar farkı görünür).
 */
trait LogsFillableActivity
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
