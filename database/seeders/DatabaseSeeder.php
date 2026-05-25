<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Production-safe seed: sadece altyapı, hiç demo içerik yok.
     *
     * Çağrılan seeder'lar:
     *   - RolesAndAdminSeeder         → 3 rol + admin user (.env'den)
     *   - SiteSettingSeeder            → site genel ayarları default değerleri
     *   - EditorRolePermissionsSeeder  → editor rolüne content permissions
     *
     * Demo içerik (sponsor, takım, proje, etkinlik, haber, mezun, başvuru formu,
     * sayaç metrikleri) için: php artisan db:seed --class=DemoContentSeeder
     * Bu komut sadece local/staging'de çağrılmalı, production'da admin paneli
     * üzerinden içerik girilir.
     */
    public function run(): void
    {
        $this->call([
            RolesAndAdminSeeder::class,
            SiteSettingSeeder::class,
            EditorRolePermissionsSeeder::class,
        ]);
    }
}
