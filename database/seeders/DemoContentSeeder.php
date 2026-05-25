<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Local + staging için örnek içerik. PRODUCTION'da çağırma.
 *
 * Kullanım:
 *   php artisan db:seed --class=DemoContentSeeder
 *
 * İçerik:
 *   - 4 site metric (üye/proje/etkinlik/partner sayaçları)
 *   - 8 sponsor (tier dağılımıyla)
 *   - Takım üyeleri + timeline
 *   - 3 Ar-Ge projesi + specs + members
 *   - 3 başvuru formu (üyelik, komisyon, Ar-Ge takım)
 *   - 4 etkinlik + 4-5 haber
 *   - 8-12 mezun
 */
class DemoContentSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SiteMetricSeeder::class,
            SponsorSeeder::class,
            TeamAndTimelineSeeder::class,
            ProjectSeeder::class,
            FormSeeder::class,
            EventAndNewsSeeder::class,
            AlumniSeeder::class,
        ]);
    }
}
