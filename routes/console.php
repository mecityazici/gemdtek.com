<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// ============================================================================
// Spatie Laravel Backup — günlük yedek + temizlik + monitör
// Cron: cPanel'de tek satır lazım:
//   * * * * * cd ~/public_html && php artisan schedule:run >> /dev/null 2>&1
// ============================================================================
Schedule::command('backup:clean')
    ->daily()
    ->at('01:30')
    ->onOneServer();

Schedule::command('backup:run')
    ->daily()
    ->at('02:00')
    ->onOneServer();

Schedule::command('backup:monitor')
    ->daily()
    ->at('03:00')
    ->onOneServer();
