<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Backup: shared hosting'de proc_open kapalı olduğu için Spatie laravel-backup
// kullanılmıyor. Yedek alma deploy/backup.sh (mysqldump + tar) + cPanel cron ile.
// Detay: DEPLOY.md §7
