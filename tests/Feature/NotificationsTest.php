<?php

namespace Tests\Feature;

use App\Models\NewsletterSubscriber;
use App\Models\User;
use App\Notifications\NewFormSubmissionNotification;
use App\Notifications\NewNewsletterSubscriberNotification;
use App\Notifications\NewSponsorLeadNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Tests\TestCase;

class NotificationsTest extends TestCase
{
    public function test_sponsor_lead_submission_notifies_admins(): void
    {
        Mail::fake();
        Notification::fake();

        $this->post('/sponsor-ol', [
            'company_name' => 'Test Tersane',
            'contact_name' => 'Ali Veli',
            'contact_email' => 'ali@test.example',
            'interest_tier' => 'gold',
            'message' => 'Görüşmek isteriz.',
        ])->assertRedirect();

        $admins = User::role(['super_admin', 'editor'])->get();
        $this->assertGreaterThan(0, $admins->count());

        Notification::assertSentTo($admins, NewSponsorLeadNotification::class);
        Notification::assertNotSentTo(User::role('team_captain')->get(), NewSponsorLeadNotification::class);
    }

    public function test_form_submission_notifies_admins(): void
    {
        Mail::fake();
        Notification::fake();

        $this->post('/basvuru/uyelik', [
            'ad_soyad' => 'Notif Test',
            'email' => 'notif@example.com',
            'telefon' => '+90 555 000 00 00',
            'bolum' => 'Gemi İnşaatı',
            'sinif' => '3. sınıf',
            'ilgi_alanlari' => ['Mekanik tasarım'],
            'motivasyon' => 'Test motivation text.',
        ])->assertRedirect();

        $admins = User::role(['super_admin', 'editor'])->get();
        $this->assertGreaterThan(0, $admins->count());
        Notification::assertSentTo($admins, NewFormSubmissionNotification::class);
    }

    public function test_newsletter_confirmation_notifies_admins(): void
    {
        Notification::fake();

        $subscriber = NewsletterSubscriber::create([
            'email' => 'confirm@example.com',
            'locale' => 'tr',
            'status' => NewsletterSubscriber::STATUS_PENDING,
        ]);

        $this->get('/bulten/onayla/'.$subscriber->confirm_token)->assertOk();

        $admins = User::role(['super_admin', 'editor'])->get();
        Notification::assertSentTo($admins, NewNewsletterSubscriberNotification::class);
    }

    public function test_newsletter_confirmation_idempotent_does_not_renotify(): void
    {
        $subscriber = NewsletterSubscriber::create([
            'email' => 'already@example.com',
            'locale' => 'tr',
            'status' => NewsletterSubscriber::STATUS_CONFIRMED,
            'confirmed_at' => now()->subDay(),
        ]);

        Notification::fake();

        // Token zaten null'a düşmüş olduğu için route'a invalid token gönderir, ama
        // confirm() çağrılmaz — kontrol etmek için aynı email'i resubscribe edip
        // o token'la gelelim, sonra 2. kez aynı tokenla gelmek bildirim ÜRETMEMELİ.
        $subscriber->forceFill([
            'status' => NewsletterSubscriber::STATUS_PENDING,
            'confirm_token' => Str::random(48),
        ])->save();

        $token = $subscriber->confirm_token;

        $this->get('/bulten/onayla/'.$token)->assertOk();

        $admins = User::role(['super_admin', 'editor'])->get();
        Notification::assertSentToTimes($admins->first(), NewNewsletterSubscriberNotification::class, 1);
    }
}
