<?php

namespace Tests\Feature;

use App\Filament\Resources\NewsletterCampaignResource;
use App\Mail\NewsletterCampaignMessage;
use App\Mail\NewsletterConfirmation;
use App\Models\NewsletterCampaign;
use App\Models\NewsletterSubscriber;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class NewsletterTest extends TestCase
{
    public function test_bulten_page_renders(): void
    {
        $this->get('/bulten')
            ->assertOk()
            ->assertSee('GEMDTEK');
    }

    public function test_subscribe_creates_pending_subscriber_and_queues_confirmation(): void
    {
        Mail::fake();

        $this->post('/bulten', [
            'email' => 'yeni@example.com',
            'name' => 'Test Kullanıcı',
        ])->assertRedirect();

        $subscriber = NewsletterSubscriber::where('email', 'yeni@example.com')->first();
        $this->assertNotNull($subscriber);
        $this->assertSame(NewsletterSubscriber::STATUS_PENDING, $subscriber->status);
        $this->assertNotNull($subscriber->confirm_token);
        $this->assertNotNull($subscriber->unsubscribe_token);

        Mail::assertQueued(NewsletterConfirmation::class, fn ($mail) => $mail->subscriber->is($subscriber));
    }

    public function test_honeypot_blocks_spam_signup(): void
    {
        Mail::fake();

        $this->post('/bulten', [
            'email' => 'spam@example.com',
            'website' => 'http://spam.example',
        ])->assertStatus(422);

        $this->assertDatabaseMissing('newsletter_subscribers', ['email' => 'spam@example.com']);
        Mail::assertNothingQueued();
    }

    public function test_resubscribe_after_unsubscribe_resets_status(): void
    {
        Mail::fake();

        $subscriber = NewsletterSubscriber::create([
            'email' => 'leaver@example.com',
            'locale' => 'tr',
            'status' => NewsletterSubscriber::STATUS_UNSUBSCRIBED,
            'unsubscribed_at' => now()->subDay(),
        ]);

        $this->post('/bulten', ['email' => 'leaver@example.com'])->assertRedirect();

        $subscriber->refresh();
        $this->assertSame(NewsletterSubscriber::STATUS_PENDING, $subscriber->status);
        $this->assertNotNull($subscriber->confirm_token);
        Mail::assertQueued(NewsletterConfirmation::class);
    }

    public function test_already_confirmed_email_shows_notice_without_resending(): void
    {
        Mail::fake();

        NewsletterSubscriber::create([
            'email' => 'confirmed@example.com',
            'locale' => 'tr',
            'status' => NewsletterSubscriber::STATUS_CONFIRMED,
            'confirmed_at' => now(),
        ]);

        $this->post('/bulten', ['email' => 'confirmed@example.com'])
            ->assertRedirect()
            ->assertSessionHas('newsletter_status', 'already_confirmed');

        Mail::assertNothingQueued();
    }

    public function test_confirm_link_marks_subscriber_confirmed(): void
    {
        $subscriber = NewsletterSubscriber::create([
            'email' => 'pending@example.com',
            'locale' => 'tr',
            'status' => NewsletterSubscriber::STATUS_PENDING,
        ]);

        $this->get('/bulten/onayla/'.$subscriber->confirm_token)
            ->assertOk();

        $subscriber->refresh();
        $this->assertSame(NewsletterSubscriber::STATUS_CONFIRMED, $subscriber->status);
        $this->assertNotNull($subscriber->confirmed_at);
        $this->assertNull($subscriber->confirm_token);
    }

    public function test_invalid_confirm_token_returns_feedback(): void
    {
        $invalidToken = str_repeat('z', 48);
        $this->get('/bulten/onayla/'.$invalidToken)
            ->assertOk()
            ->assertSeeText('Geçersiz');
    }

    public function test_unsubscribe_link_marks_subscriber_inactive(): void
    {
        $subscriber = NewsletterSubscriber::create([
            'email' => 'active@example.com',
            'locale' => 'tr',
            'status' => NewsletterSubscriber::STATUS_CONFIRMED,
            'confirmed_at' => now(),
        ]);

        $this->get('/bulten/cikis/'.$subscriber->unsubscribe_token)
            ->assertOk();

        $subscriber->refresh();
        $this->assertSame(NewsletterSubscriber::STATUS_UNSUBSCRIBED, $subscriber->status);
        $this->assertNotNull($subscriber->unsubscribed_at);
    }

    public function test_campaign_dispatch_queues_message_for_confirmed_subscribers_only(): void
    {
        Mail::fake();

        NewsletterSubscriber::create(['email' => 'tr1@example.com', 'locale' => 'tr', 'status' => NewsletterSubscriber::STATUS_CONFIRMED, 'confirmed_at' => now()]);
        NewsletterSubscriber::create(['email' => 'tr2@example.com', 'locale' => 'tr', 'status' => NewsletterSubscriber::STATUS_CONFIRMED, 'confirmed_at' => now()]);
        NewsletterSubscriber::create(['email' => 'en1@example.com', 'locale' => 'en', 'status' => NewsletterSubscriber::STATUS_CONFIRMED, 'confirmed_at' => now()]);
        NewsletterSubscriber::create(['email' => 'pending@example.com', 'locale' => 'tr', 'status' => NewsletterSubscriber::STATUS_PENDING]);

        $campaign = new NewsletterCampaign;
        $campaign->setTranslation('subject', 'tr', 'Mayıs Bülteni');
        $campaign->setTranslation('subject', 'en', 'May Newsletter');
        $campaign->setTranslation('body', 'tr', '# Merhaba\n\nBu ay öne çıkanlar...');
        $campaign->setTranslation('body', 'en', '# Hi\n\nThis month highlights...');
        $campaign->status = NewsletterCampaign::STATUS_DRAFT;
        $campaign->save();

        $count = NewsletterCampaignResource::dispatchCampaign($campaign);

        $this->assertSame(3, $count);

        $campaign->refresh();
        $this->assertSame(NewsletterCampaign::STATUS_SENT, $campaign->status);
        $this->assertSame(3, $campaign->recipients_count);
        $this->assertNotNull($campaign->sent_at);

        Mail::assertQueued(NewsletterCampaignMessage::class, 3);
    }

    public function test_campaign_locale_filter_restricts_audience(): void
    {
        Mail::fake();

        NewsletterSubscriber::create(['email' => 'a-tr@example.com', 'locale' => 'tr', 'status' => NewsletterSubscriber::STATUS_CONFIRMED, 'confirmed_at' => now()]);
        NewsletterSubscriber::create(['email' => 'a-en@example.com', 'locale' => 'en', 'status' => NewsletterSubscriber::STATUS_CONFIRMED, 'confirmed_at' => now()]);

        $campaign = new NewsletterCampaign;
        $campaign->setTranslation('subject', 'tr', 'TR-only');
        $campaign->setTranslation('body', 'tr', 'TR body');
        $campaign->audience_locale = 'tr';
        $campaign->status = NewsletterCampaign::STATUS_DRAFT;
        $campaign->save();

        $count = NewsletterCampaignResource::dispatchCampaign($campaign);

        $this->assertSame(1, $count);
        Mail::assertQueued(NewsletterCampaignMessage::class, 1);
    }
}
