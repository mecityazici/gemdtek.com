<?php

namespace Database\Seeders;

use App\Models\TeamMember;
use App\Models\TimelineEvent;
use Illuminate\Database\Seeder;

class TeamAndTimelineSeeder extends Seeder
{
    public function run(): void
    {
        $team = [
            [
                'name'         => 'Mehmet Demir',
                'position'     => 'Başkan',
                'bio'          => 'Gemi inşaatı mühendisliği bölümü 4. sınıf öğrencisi. Kulübün stratejik yönetiminden ve dış paydaş ilişkilerinden sorumlu.',
                'linkedin_url' => 'https://www.linkedin.com/in/example',
                'order'        => 10,
            ],
            [
                'name'         => 'Elif Yıldız',
                'position'     => 'Başkan Yardımcısı',
                'bio'          => 'Deniz teknolojileri 3. sınıf. Ar-Ge takımları arasında koordinasyonu yürütür.',
                'linkedin_url' => 'https://www.linkedin.com/in/example',
                'order'        => 20,
            ],
            [
                'name'         => 'Burak Aksoy',
                'position'     => 'Ar-Ge Koordinatörü',
                'bio'          => 'TEKNOFEST ROV takımı eski kaptanı. Otonom denizaltı sistemleri üzerine çalışıyor.',
                'linkedin_url' => 'https://www.linkedin.com/in/example',
                'order'        => 30,
            ],
            [
                'name'         => 'Selin Kara',
                'position'     => 'Sponsorluk & PR Sorumlusu',
                'bio'          => 'Endüstri sponsorlukları ve kurumsal iletişim süreçlerini yürütür.',
                'linkedin_url' => 'https://www.linkedin.com/in/example',
                'order'        => 40,
            ],
            [
                'name'         => 'Can Özkan',
                'position'     => 'Etkinlik & Kariyer Komisyonu Başkanı',
                'bio'          => 'Yıllık zirve organizasyonu ve kariyer günlerini koordine eder.',
                'linkedin_url' => 'https://www.linkedin.com/in/example',
                'order'        => 50,
            ],
        ];

        foreach ($team as $data) {
            TeamMember::firstOrCreate(['name' => $data['name']], $data);
        }

        $timeline = [
            ['year' => 2014, 'title' => 'Kulüp kuruluşu',                    'description' => 'Gemi inşaatı ve deniz teknolojileri öğrencilerinin inisiyatifiyle kurumsal olarak kuruldu.'],
            ['year' => 2017, 'title' => 'İlk sektör zirvesi',                 'description' => 'Tersane ve klas kuruluşu temsilcilerinin katılımıyla ilk Denizcilik Kariyer Zirvesi düzenlendi.'],
            ['year' => 2019, 'title' => 'TEKNOFEST katılımı',                 'description' => 'İnsansız Sualtı Sistemleri yarışmasında ilk derecemizi aldık.'],
            ['year' => 2022, 'title' => 'Alternatif yakıtlı gemi tasarımı',   'description' => 'Hidrojen yakıt hücreli kıyı gemisi konsept tasarımı uluslararası yarışmada finalist oldu.'],
            ['year' => 2024, 'title' => 'Endüstri ortaklığı protokolü',       'description' => 'Üç büyük tersane ile uzun vadeli mentorluk ve staj programı imzalandı.'],
        ];

        foreach ($timeline as $data) {
            TimelineEvent::firstOrCreate(
                ['year' => $data['year'], 'title' => $data['title']],
                $data,
            );
        }
    }
}
