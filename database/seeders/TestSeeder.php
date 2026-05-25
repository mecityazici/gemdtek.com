<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Test suite için seeder bundle: altyapı + demo içerik.
 *
 * Production DatabaseSeeder demo içeriği çağırmadığı için test'lere
 * gereken sponsor/etkinlik/mezun verisi burada tek seferde yüklenir.
 * tests/TestCase.php bu sınıfı çağırır.
 */
class TestSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesAndAdminSeeder::class,
            SiteSettingSeeder::class,
            EditorRolePermissionsSeeder::class,
            DemoContentSeeder::class,
        ]);
    }
}
