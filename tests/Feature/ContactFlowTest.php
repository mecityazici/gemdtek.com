<?php

namespace Tests\Feature;

use App\Mail\ContactMessageReceived;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class ContactFlowTest extends TestCase
{
    public function test_contact_page_reflects_admin_site_settings(): void
    {
        // Admin "Site Ayarları"ndan kaydedilen değerler iletişim sayfasına yansımalı.
        SiteSetting::set('contact.email', 'yeni-iletisim@gemdtek.com', 'contact', 'email');
        SiteSetting::set('contact.campus', 'Yeni Kampüs Adresi 42', 'contact', 'textarea');
        SiteSetting::set('contact.response_note', 'Aynı gün dönüyoruz.', 'contact', 'textarea');
        SiteSetting::set('social.linkedin', 'https://linkedin.com/company/yeni-gemdtek', 'social', 'url');

        $this->get('/iletisim')
            ->assertOk()
            ->assertSee('yeni-iletisim@gemdtek.com')
            ->assertSee('mailto:yeni-iletisim@gemdtek.com')
            ->assertSee('Yeni Kampüs Adresi 42')
            ->assertSee('Aynı gün dönüyoruz.')
            ->assertSee('https://linkedin.com/company/yeni-gemdtek');
    }

    public function test_contact_page_falls_back_to_defaults_without_settings(): void
    {
        // Hiç ayar yokken mevcut (lang/hardcoded varsayılan) içerik bozulmadan görünmeli.
        SiteSetting::query()->delete();
        Cache::flush();

        $this->get('/iletisim')
            ->assertOk()
            ->assertSee('info@gemdtek.com')                          // hardcoded e-posta fallback
            ->assertSee(__('pages.contact.info.campus_value'))       // lang fallback
            ->assertSee('https://linkedin.com/company/gemdtek');     // hardcoded sosyal fallback
    }

    public function test_valid_contact_message_queues_mail(): void
    {
        Mail::fake();

        $response = $this->post('/iletisim', [
            'name' => 'Endüstri Temsilcisi',
            'email' => 'temsilci@example.com',
            'subject' => 'Sponsorluk hakkında',
            'message' => 'Merhaba, sponsorluk paketleriniz hakkında bilgi almak isterim.',
        ]);

        $response->assertRedirect('/iletisim')
            ->assertSessionHas('contact_sent', true);

        Mail::assertQueued(ContactMessageReceived::class, function ($mail) {
            return $mail->name === 'Endüstri Temsilcisi'
                && $mail->email === 'temsilci@example.com'
                && $mail->messageSubject === 'Sponsorluk hakkında';
        });
    }

    public function test_contact_form_validation_fails_on_missing_fields(): void
    {
        $response = $this->from('/iletisim')->post('/iletisim', [
            'name' => 'Only Name',
        ]);

        $response->assertRedirect('/iletisim')
            ->assertSessionHasErrors(['email', 'subject', 'message']);
    }

    public function test_contact_honeypot_blocks_spam(): void
    {
        $this->withoutExceptionHandling();
        $this->expectException(HttpException::class);

        $this->post('/iletisim', [
            'website' => 'spam-payload',
            'name' => 'X',
            'email' => 'x@x.com',
            'subject' => 'X',
            'message' => 'X',
        ]);
    }
}
