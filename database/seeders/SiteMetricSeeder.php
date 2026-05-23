<?php

namespace Database\Seeders;

use App\Models\SiteMetric;
use Illuminate\Database\Seeder;

class SiteMetricSeeder extends Seeder
{
    public function run(): void
    {
        $metrics = [
            ['key' => 'members',  'value' => 120, 'order' => 10, 'label' => ['tr' => 'Aktif üye',                'en' => 'Active members']],
            ['key' => 'projects', 'value' =>  28, 'order' => 20, 'label' => ['tr' => 'Tamamlanan teknik proje',  'en' => 'Completed projects']],
            ['key' => 'events',   'value' =>  14, 'order' => 30, 'label' => ['tr' => 'Sektörel etkinlik',         'en' => 'Industry events']],
            ['key' => 'partners', 'value' =>  22, 'order' => 40, 'label' => ['tr' => 'Kurumsal partner',          'en' => 'Corporate partners']],
        ];

        foreach ($metrics as $data) {
            SiteMetric::firstOrCreate(['key' => $data['key']], $data);
        }
    }
}
