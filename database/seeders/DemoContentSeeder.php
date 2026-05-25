<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

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
 *   - Test kullanıcı: editor@gemdtek.com (Editor!2026)
 *     [kaptan@gemdtek.com Captain!2026 — ProjectSeeder içinde]
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

        // Test editor user — sadece local geliştirme/test için.
        // Production'da editor user'ları admin paneli üzerinden yaratılır.
        $editor = User::firstOrCreate(
            ['email' => 'editor@gemdtek.com'],
            [
                'name' => 'Editor Test',
                'password' => Hash::make('Editor!2026'),
                'email_verified_at' => now(),
            ],
        );

        if (! $editor->hasRole('editor')) {
            $editor->assignRole('editor');
        }

        $this->command->line('Test editor (local-only): editor@gemdtek.com / Editor!2026');
    }
}
