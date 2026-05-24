<?php

namespace App\Mail;

use App\Models\NewsletterCampaign;
use App\Models\NewsletterSubscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewsletterCampaignMessage extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public NewsletterCampaign $campaign,
        public NewsletterSubscriber $subscriber,
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->campaign->getTranslation('subject', $this->subscriber->locale, false)
            ?: $this->campaign->getTranslation('subject', 'tr');

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        $body = $this->campaign->getTranslation('body', $this->subscriber->locale, false)
            ?: $this->campaign->getTranslation('body', 'tr');

        return new Content(
            markdown: 'emails.newsletter-campaign',
            with: [
                'body' => $body,
                'subscriber' => $this->subscriber,
                'unsubscribeUrl' => route('newsletter.unsubscribe', ['token' => $this->subscriber->unsubscribe_token]),
            ],
        );
    }
}
