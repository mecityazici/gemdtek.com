<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\NewsPost;
use Illuminate\Database\Seeder;

class EventAndNewsSeeder extends Seeder
{
    public function run(): void
    {
        $events = [
            [
                'slug'             => 'denizcilik-kariyer-zirvesi-2026',
                'category'         => 'zirve',
                'event_date'       => '2026-10-25 10:00:00',
                'location'         => 'İstanbul Teknik Üniversitesi, Süleyman Demirel Kültür Merkezi',
                'registration_url' => 'https://example.com/register',
                'order'            => 10,
                'title' => [
                    'tr' => 'GEMDTEK Denizcilik Kariyer Zirvesi 2026',
                    'en' => 'GEMDTEK Maritime Career Summit 2026',
                ],
                'summary' => [
                    'tr' => 'Tersane mühendislerinden klas kuruluşu uzmanlarına oturumlar, panel ve kariyer fuarı.',
                    'en' => 'Sessions, panels, and a career fair with shipyard engineers and classification society experts.',
                ],
                'description' => [
                    'tr' => '<p>Türkiye\'nin en kapsamlı öğrenci odaklı denizcilik zirvesi. Üç ana sahne: kariyer paneli, teknik atölyeler ve mezun buluşması. Etkinlik boyunca tersane standları açık olacak.</p>',
                    'en' => '<p>Turkey\'s most comprehensive student-focused maritime summit. Three main stages: career panel, technical workshops, and alumni reunion. Shipyard booths open throughout the day.</p>',
                ],
            ],
            [
                'slug'       => 'cfd-atolyesi',
                'category'   => 'atolye',
                'event_date' => '2026-06-12 14:00:00',
                'location'   => 'Online (Zoom)',
                'order'      => 20,
                'title'      => ['tr' => 'CFD Atölyesi: OpenFOAM ile Tekne Gövde Analizi', 'en' => 'CFD Workshop: Hull Analysis with OpenFOAM'],
                'summary'    => ['tr' => 'Açık kaynak CFD ile temel hidrodinamik analiz — başlangıç seviyesi.', 'en' => 'Basic hydrodynamic analysis with open-source CFD — entry level.'],
                'description'=> ['tr' => '<p>Sıfırdan OpenFOAM kurulumu, basit bir tekne gövdesi için akış simülasyonu ve sonuç yorumlama. Ön bilgi gerekmez, laptop yeterli.</p>', 'en' => '<p>OpenFOAM installation from scratch, flow simulation for a simple hull, and result interpretation. No prior experience needed — just a laptop.</p>'],
            ],
            [
                'slug'       => 'alternatif-yakitlar-paneli',
                'category'   => 'panel',
                'event_date' => '2026-08-08 18:00:00',
                'location'   => 'İTÜ Maslak Kampüsü, KKM Salon B',
                'order'      => 30,
                'title'      => ['tr' => 'Alternatif Yakıtlar Paneli', 'en' => 'Alternative Fuels Panel'],
                'summary'    => ['tr' => 'Hidrojen, amonyak ve metanol — Türk denizciliği için sektör görüşü.', 'en' => 'Hydrogen, ammonia, and methanol — industry outlook for Turkish maritime.'],
                'description'=> ['tr' => '<p>Üç sektör uzmanı, gemi inşaatında alternatif yakıt geçişinin teknik ve ekonomik gerçeklerini tartışacak.</p>', 'en' => '<p>Three industry experts will discuss the technical and economic realities of alternative fuel transition in shipbuilding.</p>'],
            ],
            [
                'slug'       => 'kariyer-gunu-2025',
                'category'   => 'kariyer-gunu',
                'event_date' => '2025-11-15 09:30:00',
                'location'   => 'YTÜ Davutpaşa, Kongre Merkezi',
                'order'      => 40,
                'title'      => ['tr' => 'Denizcilik Kariyer Günü 2025', 'en' => 'Maritime Career Day 2025'],
                'summary'    => ['tr' => '12 tersane temsilcisi, 400+ öğrenci, 80\'i aşkın staj görüşmesi.', 'en' => '12 shipyard reps, 400+ students, 80+ internship interviews.'],
                'description'=> ['tr' => '<p>Geçen yılın etkinliği için katılımcı geri bildirim oranı %92. 2026 etkinliğimizden önce arşivimizden okuyabilirsiniz.</p>', 'en' => '<p>Last year\'s event received a 92% positive feedback rate. Read the archive before our 2026 edition.</p>'],
            ],
        ];

        foreach ($events as $data) {
            Event::firstOrCreate(['slug' => $data['slug']], $data);
        }

        $news = [
            [
                'slug'         => 'rov-takimi-teknofest-finalist',
                'category'     => 'duyuru',
                'published_at' => '2026-04-18 10:00:00',
                'is_published' => true,
                'order'        => 10,
                'title'        => ['tr' => 'ROV Takımımız TEKNOFEST Finalist!', 'en' => 'Our ROV Team is a TEKNOFEST Finalist!'],
                'excerpt'      => ['tr' => 'Sualtı sistemleri kategorisinde, 240 takım arasından ilk 20\'ye girdik.', 'en' => 'In the underwater systems category, we made it to the top 20 among 240 teams.'],
                'content'      => ['tr' => '<p>İstanbul\'da yapılan ön elemelerde gösterdiğimiz performans sayesinde Eylül ayında Karadeniz Ereğli\'de yapılacak finale katılma hakkını elde ettik.</p>', 'en' => '<p>Thanks to our performance in the Istanbul preliminaries, we earned the right to compete in the final to be held in Karadeniz Ereğli in September.</p>'],
            ],
            [
                'slug'         => 'tersane-istanbul-sponsorlugu',
                'category'     => 'duyuru',
                'published_at' => '2026-03-22 14:30:00',
                'is_published' => true,
                'order'        => 20,
                'title'        => ['tr' => 'Tersane İstanbul Platin Sponsorumuz Oldu', 'en' => 'Tersane İstanbul Becomes Our Platinum Sponsor'],
                'excerpt'      => ['tr' => 'Üç yıllık stratejik sponsorluk protokolü imzalandı.', 'en' => 'Three-year strategic sponsorship protocol signed.'],
                'content'      => ['tr' => '<p>Mart ayında imzalanan protokol kapsamında üyelerimize tersane ziyaretleri, mentörlük ve öncelikli staj kanalı açılacak.</p>', 'en' => '<p>Under the protocol signed in March, shipyard visits, mentoring, and a priority internship channel will be opened for our members.</p>'],
            ],
            [
                'slug'         => 'hidrojen-yakit-hucresi-makalesi',
                'category'     => 'blog',
                'published_at' => '2026-02-10 09:00:00',
                'is_published' => true,
                'order'        => 30,
                'title'        => ['tr' => 'Kıyı Gemilerinde Hidrojen Yakıt Hücresi: Nereden Başlamalı?', 'en' => 'Hydrogen Fuel Cells in Coastal Vessels: Where to Start?'],
                'excerpt'      => ['tr' => 'PEM teknolojisinin temellerinden tasarım kararlarına 8 dakikalık özet.', 'en' => 'From PEM fundamentals to design decisions in an 8-minute summary.'],
                'content'      => ['tr' => '<p>Hidrojen kıyı gemisi tasarımı projemizden öğrendiklerimizi paylaşıyoruz.</p>', 'en' => '<p>Sharing what we learned from our hydrogen coastal vessel design project.</p>'],
            ],
            [
                'slug'         => 'gemi-muhendislik-dergisi-roportaj',
                'category'     => 'basin',
                'published_at' => '2026-01-15 11:00:00',
                'is_published' => true,
                'order'        => 40,
                'title'        => ['tr' => 'Gemi Mühendisliği Dergisi: GEMDTEK Röportajı', 'en' => 'Naval Engineering Magazine: GEMDTEK Interview'],
                'excerpt'      => ['tr' => 'Başkanımız ile dijital denizcilik öğrenciliği üzerine.', 'en' => 'A conversation with our president on digital maritime studentship.'],
                'content'      => ['tr' => '<p>Tam metin dergi arşivinde mevcut. Burada öne çıkan üç paragrafı paylaşıyoruz.</p>', 'en' => '<p>Full text is in the magazine archive. We share three highlighted paragraphs here.</p>'],
            ],
            [
                'slug'         => 'yeni-donem-uye-alimi',
                'category'     => 'duyuru',
                'published_at' => '2026-05-01 08:00:00',
                'is_published' => true,
                'order'        => 50,
                'title'        => ['tr' => '2026-2027 Dönem Üye Alımları Başladı', 'en' => '2026-2027 Term Membership Applications Open'],
                'excerpt'      => ['tr' => 'Başvurular 30 Mayıs\'a kadar açık.', 'en' => 'Applications are open until May 30.'],
                'content'      => ['tr' => '<p>Üyelik formunu doldurman yeterli; mülakat aşaması yok.</p>', 'en' => '<p>Just fill in the membership form — no interview stage.</p>'],
            ],
        ];

        foreach ($news as $data) {
            NewsPost::firstOrCreate(['slug' => $data['slug']], $data);
        }
    }
}
