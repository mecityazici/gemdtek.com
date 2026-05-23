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
                'position'     => ['tr' => 'Başkan', 'en' => 'President'],
                'bio'          => [
                    'tr' => 'Gemi inşaatı mühendisliği bölümü 4. sınıf öğrencisi. Kulübün stratejik yönetiminden ve dış paydaş ilişkilerinden sorumlu.',
                    'en' => 'Senior naval architecture student. Responsible for the club\'s strategic direction and external stakeholder relations.',
                ],
                'linkedin_url' => 'https://www.linkedin.com/in/example',
                'order'        => 10,
            ],
            [
                'name'         => 'Elif Yıldız',
                'position'     => ['tr' => 'Başkan Yardımcısı', 'en' => 'Vice President'],
                'bio'          => [
                    'tr' => 'Deniz teknolojileri 3. sınıf. Ar-Ge takımları arasında koordinasyonu yürütür.',
                    'en' => 'Junior in marine technologies. Coordinates between the R&D teams.',
                ],
                'linkedin_url' => 'https://www.linkedin.com/in/example',
                'order'        => 20,
            ],
            [
                'name'         => 'Burak Aksoy',
                'position'     => ['tr' => 'Ar-Ge Koordinatörü', 'en' => 'R&D Coordinator'],
                'bio'          => [
                    'tr' => 'TEKNOFEST ROV takımı eski kaptanı. Otonom denizaltı sistemleri üzerine çalışıyor.',
                    'en' => 'Former captain of the TEKNOFEST ROV team. Focuses on autonomous underwater systems.',
                ],
                'linkedin_url' => 'https://www.linkedin.com/in/example',
                'order'        => 30,
            ],
            [
                'name'         => 'Selin Kara',
                'position'     => ['tr' => 'Sponsorluk & PR Sorumlusu', 'en' => 'Sponsorship & PR Lead'],
                'bio'          => [
                    'tr' => 'Endüstri sponsorlukları ve kurumsal iletişim süreçlerini yürütür.',
                    'en' => 'Runs industry sponsorships and corporate communications.',
                ],
                'linkedin_url' => 'https://www.linkedin.com/in/example',
                'order'        => 40,
            ],
            [
                'name'         => 'Can Özkan',
                'position'     => ['tr' => 'Etkinlik & Kariyer Komisyonu Başkanı', 'en' => 'Events & Career Commission Chair'],
                'bio'          => [
                    'tr' => 'Yıllık zirve organizasyonu ve kariyer günlerini koordine eder.',
                    'en' => 'Coordinates the annual summit and career days.',
                ],
                'linkedin_url' => 'https://www.linkedin.com/in/example',
                'order'        => 50,
            ],
        ];

        foreach ($team as $data) {
            TeamMember::firstOrCreate(['name' => $data['name']], $data);
        }

        $timeline = [
            [
                'year'        => 2014,
                'title'       => ['tr' => 'Kulüp kuruluşu',                     'en' => 'Club founded'],
                'description' => [
                    'tr' => 'Gemi inşaatı ve deniz teknolojileri öğrencilerinin inisiyatifiyle kurumsal olarak kuruldu.',
                    'en' => 'Officially founded through the initiative of naval architecture and marine technology students.',
                ],
            ],
            [
                'year'        => 2017,
                'title'       => ['tr' => 'İlk sektör zirvesi',                  'en' => 'First industry summit'],
                'description' => [
                    'tr' => 'Tersane ve klas kuruluşu temsilcilerinin katılımıyla ilk Denizcilik Kariyer Zirvesi düzenlendi.',
                    'en' => 'First Maritime Career Summit hosted shipyard and classification society representatives.',
                ],
            ],
            [
                'year'        => 2019,
                'title'       => ['tr' => 'TEKNOFEST katılımı',                  'en' => 'TEKNOFEST participation'],
                'description' => [
                    'tr' => 'İnsansız Sualtı Sistemleri yarışmasında ilk derecemizi aldık.',
                    'en' => 'Earned our first ranking in the Unmanned Underwater Systems competition.',
                ],
            ],
            [
                'year'        => 2022,
                'title'       => ['tr' => 'Alternatif enerjili gemi tasarımı',   'en' => 'Alternative-energy vessel design'],
                'description' => [
                    'tr' => 'Hidrojen yakıt hücreli kıyı gemisi konsept tasarımı uluslararası yarışmada finalist oldu.',
                    'en' => 'Hydrogen fuel-cell coastal vessel concept design reached the finals of an international competition.',
                ],
            ],
            [
                'year'        => 2024,
                'title'       => ['tr' => 'Endüstri ortaklığı protokolü',        'en' => 'Industry partnership protocol'],
                'description' => [
                    'tr' => 'Üç büyük tersane ile uzun vadeli mentorluk ve staj programı imzalandı.',
                    'en' => 'Signed long-term mentorship and internship program with three major shipyards.',
                ],
            ],
        ];

        foreach ($timeline as $data) {
            TimelineEvent::firstOrCreate(['year' => $data['year']], $data);
        }
    }
}
