<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            // Genel
            ['site.name', 'GEMDTEK', 'general', 'text'],
            ['site.tagline', 'Gemi İnşaatı ve Deniz Teknolojileri Kulübü', 'general', 'text'],
            ['site.description', 'Üniversite ile denizcilik endüstrisi arasında mühendislik köprüsü.', 'general', 'textarea'],

            // İletişim
            ['contact.email', 'info@gemdtek.com', 'contact', 'email'],
            ['contact.campus', 'İTÜ Maslak Kampüsü, İnşaat Fakültesi', 'contact', 'text'],
            ['contact.response_note', 'İletişim formundan gelen mesajlara 48 saat içinde dönüyoruz.', 'contact', 'textarea'],

            // Sosyal Medya
            ['social.linkedin', 'https://linkedin.com/company/gemdtek', 'social', 'url'],
            ['social.instagram', 'https://instagram.com/gemdtek', 'social', 'url'],
            ['social.twitter', 'https://x.com/gemdtek', 'social', 'url'],

            // SEO
            ['seo.keywords', 'gemi inşaatı, deniz teknolojileri, kulüp, ITU, Ar-Ge', 'seo', 'textarea'],
            ['seo.author', 'GEMDTEK', 'seo', 'text'],

            // Bildirimler
            ['notifications.email', 'info@gemdtek.com', 'notifications', 'email'],
        ];

        foreach ($defaults as [$key, $value, $group, $type]) {
            SiteSetting::firstOrCreate(
                ['key' => $key],
                ['value' => $value, 'group' => $group, 'type' => $type],
            );
        }
    }
}
