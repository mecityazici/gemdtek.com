<?php

namespace Database\Seeders;

use App\Models\Alumni;
use Illuminate\Database\Seeder;

class AlumniSeeder extends Seeder
{
    public function run(): void
    {
        $alumni = [
            [
                'name' => 'Cem Aydoğdu', 'graduation_year' => 2014, 'sector' => 'tersane', 'company' => 'Tersane İstanbul', 'city' => 'İstanbul', 'linkedin_url' => 'https://www.linkedin.com/in/example',
                'position' => ['tr' => 'Üretim Mühendisi', 'en' => 'Production Engineer'],
                'order' => 10,
            ],
            [
                'name' => 'Selin Akın', 'graduation_year' => 2015, 'sector' => 'klas', 'company' => 'Türk Loydu', 'city' => 'İstanbul', 'linkedin_url' => 'https://www.linkedin.com/in/example',
                'position' => ['tr' => 'Sörveyör', 'en' => 'Surveyor'],
                'order' => 20,
            ],
            [
                'name' => 'Eray Kütük', 'graduation_year' => 2016, 'sector' => 'tasarim-ofisi', 'company' => 'Delta Marin', 'city' => 'İstanbul', 'linkedin_url' => 'https://www.linkedin.com/in/example',
                'position' => ['tr' => 'Senior Naval Architect', 'en' => 'Senior Naval Architect'],
                'order' => 30,
            ],
            [
                'name' => 'Aslı Bener', 'graduation_year' => 2017, 'sector' => 'armator', 'company' => 'Arkas Line', 'city' => 'İzmir', 'linkedin_url' => 'https://www.linkedin.com/in/example',
                'position' => ['tr' => 'Filo Operasyon Sorumlusu', 'en' => 'Fleet Operations Lead'],
                'order' => 40,
            ],
            [
                'name' => 'Kerem Yıldız', 'graduation_year' => 2017, 'sector' => 'akademik', 'company' => 'İTÜ', 'city' => 'İstanbul', 'linkedin_url' => 'https://www.linkedin.com/in/example',
                'position' => ['tr' => 'Araştırma Görevlisi', 'en' => 'Research Assistant'],
                'order' => 50,
            ],
            [
                'name' => 'Pınar Demirhan', 'graduation_year' => 2018, 'sector' => 'tersane', 'company' => 'Sefine Tersanesi', 'city' => 'Yalova', 'linkedin_url' => 'https://www.linkedin.com/in/example',
                'position' => ['tr' => 'Proje Mühendisi', 'en' => 'Project Engineer'],
                'order' => 60,
            ],
            [
                'name' => 'Mert Özkaya', 'graduation_year' => 2019, 'sector' => 'yazilim', 'company' => 'Sea Robotics', 'city' => 'Ankara', 'linkedin_url' => 'https://www.linkedin.com/in/example',
                'position' => ['tr' => 'Otonom Sistemler Geliştirici', 'en' => 'Autonomous Systems Developer'],
                'order' => 70,
            ],
            [
                'name' => 'Ayşe Çelik', 'graduation_year' => 2019, 'sector' => 'klas', 'company' => 'DNV', 'city' => 'İstanbul', 'linkedin_url' => 'https://www.linkedin.com/in/example',
                'position' => ['tr' => 'Klas Sörveyörü', 'en' => 'Classification Surveyor'],
                'order' => 80,
            ],
            [
                'name' => 'Onur Tekin', 'graduation_year' => 2020, 'sector' => 'tasarim-ofisi', 'company' => 'Mar-Yap', 'city' => 'İstanbul', 'linkedin_url' => 'https://www.linkedin.com/in/example',
                'position' => ['tr' => 'Yapısal Tasarım Mühendisi', 'en' => 'Structural Design Engineer'],
                'order' => 90,
            ],
            [
                'name' => 'Damla Şen', 'graduation_year' => 2021, 'sector' => 'armator', 'company' => 'Yıldırım Group', 'city' => 'İstanbul', 'linkedin_url' => 'https://www.linkedin.com/in/example',
                'position' => ['tr' => 'Teknik Süperintendant', 'en' => 'Technical Superintendent'],
                'order' => 100,
            ],
            [
                'name' => 'Furkan Akar', 'graduation_year' => 2022, 'sector' => 'yazilim', 'company' => 'Marindeq', 'city' => 'İstanbul', 'linkedin_url' => 'https://www.linkedin.com/in/example',
                'position' => ['tr' => 'Backend Developer', 'en' => 'Backend Developer'],
                'order' => 110,
            ],
            [
                'name' => 'Zeynep Akpınar', 'graduation_year' => 2023, 'sector' => 'akademik', 'company' => 'YTÜ', 'city' => 'İstanbul', 'linkedin_url' => 'https://www.linkedin.com/in/example',
                'position' => ['tr' => 'Yüksek Lisans Öğrencisi', 'en' => 'MSc Student'],
                'order' => 120,
            ],
        ];

        foreach ($alumni as $data) {
            Alumni::firstOrCreate(['name' => $data['name'], 'graduation_year' => $data['graduation_year']], $data);
        }
    }
}
