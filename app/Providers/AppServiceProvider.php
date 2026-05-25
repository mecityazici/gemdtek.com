<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Eski MySQL/MariaDB (innodb_default_row_format=COMPACT) için
        // utf8mb4 + VARCHAR(255) primary key 1071 hatası verir.
        // 191 karakter sınırı 767 byte'a sığar, evrensel çalışır.
        Schema::defaultStringLength(191);
    }
}
