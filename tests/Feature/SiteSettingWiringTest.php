<?php

namespace Tests\Feature;

use App\Mail\ContactMessageReceived;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SiteSettingWiringTest extends TestCase
{
    public function test_form_notifications_go_to_admin_notification_email(): void
    {
        // Admin "Bildirimler" sekmesindeki e-posta artık form bildirimlerine gider
        // (önceden sadece .env FORM_NOTIFICATION_EMAIL kullanılıyordu).
        Mail::fake();
        SiteSetting::set('notifications.email', 'bildirim@gemdtek.com', 'notifications', 'email');

        $this->post('/iletisim', [
            'name' => 'Test',
            'email' => 'gonderen@example.com',
            'subject' => 'Konu',
            'message' => 'Mesaj metni.',
        ])->assertRedirect('/iletisim');

        Mail::assertQueued(
            ContactMessageReceived::class,
            fn ($mail) => $mail->hasTo('bildirim@gemdtek.com')
        );
    }

    public function test_layout_head_reflects_site_and_seo_settings(): void
    {
        // Ana sayfa kendi title/meta_description'ını set etmez → layout fallback'leri kullanılır.
        SiteSetting::set('site.name', 'TESTMARKA', 'general');
        SiteSetting::set('site.description', 'Özel site açıklaması.', 'general', 'textarea');
        SiteSetting::set('seo.keywords', 'gemi, deniz, test', 'seo', 'textarea');
        SiteSetting::set('seo.author', 'Test Yazar', 'seo');

        $res = $this->get('/')->assertOk();
        $res->assertSee('TESTMARKA');                                            // og:site_name + nav/footer
        $res->assertSee('Özel site açıklaması.');                                // meta description fallback
        $res->assertSee('name="keywords" content="gemi, deniz, test"', false);  // seo.keywords meta
        $res->assertSee('name="author" content="Test Yazar"', false);           // seo.author meta
    }
}
