<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndAdminSeeder::class,
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
