<?php

namespace Database\Seeders;

use App\Models\Sponsor;
use Illuminate\Database\Seeder;

class SponsorSeeder extends Seeder
{
    public function run(): void
    {
        $sponsors = [
            ['name' => ['tr' => 'Tersane İstanbul',     'en' => 'Tersane İstanbul'],      'tier' => 'platinum', 'url' => 'https://example.com/tersane-istanbul',    'order' => 10],
            ['name' => ['tr' => 'DenizMarin A.Ş.',      'en' => 'DenizMarin Inc.'],       'tier' => 'platinum', 'url' => 'https://example.com/denizmarin',          'order' => 20],
            ['name' => ['tr' => 'Yıldız Tasarım Ofisi', 'en' => 'Yıldız Design Office'],  'tier' => 'gold',     'url' => 'https://example.com/yildiz-tasarim',      'order' => 30],
            ['name' => ['tr' => 'Aegean Klas',          'en' => 'Aegean Class'],          'tier' => 'gold',     'url' => 'https://example.com/aegean-klas',         'order' => 40],
            ['name' => ['tr' => 'Boğaz Mühendislik',    'en' => 'Boğaz Engineering'],     'tier' => 'silver',   'url' => 'https://example.com/bogaz-muhendislik',   'order' => 50],
            ['name' => ['tr' => 'Karasu Teknik',        'en' => 'Karasu Technical'],      'tier' => 'silver',   'url' => 'https://example.com/karasu-teknik',       'order' => 60],
            ['name' => ['tr' => 'Marmara Sanayi',       'en' => 'Marmara Industry'],      'tier' => 'bronze',   'url' => 'https://example.com/marmara-sanayi',      'order' => 70],
            ['name' => ['tr' => 'Çelik Döküm A.Ş.',     'en' => 'Çelik Foundry Inc.'],    'tier' => 'destek',   'url' => 'https://example.com/celik-dokum',         'order' => 80],
        ];

        foreach ($sponsors as $data) {
            Sponsor::firstOrCreate(['url' => $data['url']], $data);
        }
    }
}
