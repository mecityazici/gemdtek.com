<?php

namespace Database\Seeders;

use App\Models\Sponsor;
use Illuminate\Database\Seeder;

class SponsorSeeder extends Seeder
{
    public function run(): void
    {
        $sponsors = [
            ['name' => 'Tersane İstanbul',      'tier' => 'platinum', 'url' => 'https://example.com', 'order' => 10],
            ['name' => 'DenizMarin A.Ş.',       'tier' => 'platinum', 'url' => 'https://example.com', 'order' => 20],
            ['name' => 'Yıldız Tasarım Ofisi',  'tier' => 'gold',     'url' => 'https://example.com', 'order' => 30],
            ['name' => 'Aegean Klas',           'tier' => 'gold',     'url' => 'https://example.com', 'order' => 40],
            ['name' => 'Boğaz Mühendislik',     'tier' => 'silver',   'url' => 'https://example.com', 'order' => 50],
            ['name' => 'Karasu Teknik',         'tier' => 'silver',   'url' => 'https://example.com', 'order' => 60],
            ['name' => 'Marmara Sanayi',        'tier' => 'bronze',   'url' => 'https://example.com', 'order' => 70],
            ['name' => 'Çelik Döküm A.Ş.',      'tier' => 'destek',   'url' => 'https://example.com', 'order' => 80],
        ];

        foreach ($sponsors as $data) {
            Sponsor::firstOrCreate(['name' => $data['name']], $data);
        }
    }
}
