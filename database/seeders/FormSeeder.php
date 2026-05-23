<?php

namespace Database\Seeders;

use App\Models\Form;
use Illuminate\Database\Seeder;

class FormSeeder extends Seeder
{
    public function run(): void
    {
        $blueprint = [
            [
                'slug'            => 'uyelik',
                'title'           => 'Kulüp Üyelik Başvurusu',
                'description'     => 'GEMDTEK\'e dönem üyesi olmak için doldurman gereken kısa form. Bilgilerin ekibimiz ile paylaşılır, mülakat gerekmez.',
                'is_active'       => true,
                'success_message' => 'Üyelik başvurun alındı. Birkaç gün içinde e-posta ile dönüş yapacağız.',
                'closed_message'  => 'Dönem üye alımları kapandı. Bir sonraki dönem için bizi takipte kal.',
                'fields' => [
                    ['type' => 'text',     'name' => 'ad_soyad',   'label' => 'Ad Soyad',    'is_required' => true],
                    ['type' => 'email',    'name' => 'email',      'label' => 'E-posta',     'is_required' => true],
                    ['type' => 'tel',      'name' => 'telefon',    'label' => 'Telefon',     'placeholder' => '+90 5__ ___ __ __'],
                    ['type' => 'text',     'name' => 'bolum',      'label' => 'Bölüm',       'is_required' => true],
                    ['type' => 'select',   'name' => 'sinif',      'label' => 'Sınıf',       'is_required' => true,
                        'options' => ['1. sınıf', '2. sınıf', '3. sınıf', '4. sınıf', 'Yüksek Lisans']],
                    ['type' => 'checkbox', 'name' => 'ilgi_alanlari', 'label' => 'İlgilendiğin alanlar',
                        'options' => ['Mekanik tasarım', 'Otonom yazılım', 'Elektronik donanım', 'Sosyal medya / PR', 'Etkinlik yönetimi', 'Sponsorluk']],
                    ['type' => 'textarea', 'name' => 'motivasyon', 'label' => 'Neden GEMDTEK\'e katılmak istiyorsun?',
                        'help_text' => 'Kısa tut — birkaç cümle yeterli.'],
                ],
            ],
            [
                'slug'            => 'komisyon',
                'title'           => 'Komisyon Başvurusu (PR / Kariyer / Sponsorluk / Etkinlik)',
                'description'     => 'Komisyonlarda görev almak istiyorsan bu formu doldur. Başvurun sonrası kısa bir online mülakat planlanır.',
                'is_active'       => true,
                'success_message' => 'Başvurun alındı. Mülakat takvimi için 1 hafta içinde dönüş yapacağız.',
                'closed_message'  => 'Komisyon başvuruları bu dönem için kapandı.',
                'fields' => [
                    ['type' => 'text',     'name' => 'ad_soyad',   'label' => 'Ad Soyad',  'is_required' => true],
                    ['type' => 'email',    'name' => 'email',      'label' => 'E-posta',   'is_required' => true],
                    ['type' => 'text',     'name' => 'bolum',      'label' => 'Bölüm',     'is_required' => true],
                    ['type' => 'select',   'name' => 'komisyon',   'label' => 'Hangi komisyon?', 'is_required' => true,
                        'options' => ['PR & İletişim', 'Kariyer', 'Sponsorluk', 'Etkinlik']],
                    ['type' => 'radio',    'name' => 'gecmis_deneyim', 'label' => 'Bu alanda daha önce yer aldın mı?',
                        'is_required' => true,
                        'options' => ['Evet', 'Hayır']],
                    ['type' => 'textarea', 'name' => 'deneyim',    'label' => 'Deneyim ve referansların',
                        'help_text' => 'Yer aldıysan organizasyon adı + rolün; almadıysan motivasyonunu yaz.'],
                    ['type' => 'url',      'name' => 'linkedin',   'label' => 'LinkedIn URL',
                        'placeholder' => 'https://linkedin.com/in/kullanici'],
                    ['type' => 'file',     'name' => 'cv',         'label' => 'CV (PDF, opsiyonel)',
                        'help_text' => 'En fazla 10 MB.'],
                ],
            ],
            [
                'slug'            => 'ar-ge-basvuru',
                'title'           => 'Ar-Ge Takım Başvurusu',
                'description'     => 'Aktif Ar-Ge takımlarımıza katılmak için. Teknik seviyene uygun bir takıma yönlendirilirsin.',
                'is_active'       => true,
                'success_message' => 'Teknik başvurun alındı. Takım kaptanı 1 hafta içinde seninle iletişime geçecek.',
                'closed_message'  => 'Ar-Ge alımları kapalı. Yeni proje açıldığında duyuracağız.',
                'fields' => [
                    ['type' => 'text',     'name' => 'ad_soyad',   'label' => 'Ad Soyad',  'is_required' => true],
                    ['type' => 'email',    'name' => 'email',      'label' => 'E-posta',   'is_required' => true],
                    ['type' => 'text',     'name' => 'bolum',      'label' => 'Bölüm',     'is_required' => true],
                    ['type' => 'checkbox', 'name' => 'alanlar',    'label' => 'İlgilendiğin teknik alanlar',
                        'is_required' => true,
                        'options' => ['Mekanik tasarım', 'Otonom yazılım', 'Elektronik donanım', 'Sensör entegrasyonu', 'Simülasyon / CFD', 'Kontrol sistemleri']],
                    ['type' => 'radio',    'name' => 'seviye',     'label' => 'Teknik seviyen',
                        'is_required' => true,
                        'options' => ['Başlangıç (öğrenmeye açık)', 'Orta (bağımsız çalışabilir)', 'İleri (lider olabilir)']],
                    ['type' => 'url',      'name' => 'github',     'label' => 'GitHub / portfolyo URL',
                        'placeholder' => 'https://github.com/kullanici'],
                    ['type' => 'textarea', 'name' => 'projeler',   'label' => 'Önceki projelerin',
                        'help_text' => 'Okul, hobi veya yarışma — kısaca anlat.'],
                    ['type' => 'file',     'name' => 'cv',         'label' => 'CV (PDF)',
                        'is_required' => true, 'help_text' => 'En fazla 10 MB.'],
                ],
            ],
        ];

        foreach ($blueprint as $data) {
            $fields = $data['fields'];
            unset($data['fields']);

            $form = Form::firstOrCreate(['slug' => $data['slug']], $data);

            if ($form->fields()->doesntExist()) {
                foreach ($fields as $i => $field) {
                    $field['order'] = $i * 10;
                    $form->fields()->create($field);
                }
            }
        }
    }
}
