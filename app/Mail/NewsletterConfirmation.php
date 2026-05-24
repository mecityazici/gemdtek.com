<?php

namespace App\Mail;

use App\Models\NewsletterSubscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewsletterConfirmation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public NewsletterSubscriber $subscriber) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subscriber->locale === 'en'
                ? 'Confirm your GEMDTEK newsletter subscription'
                : 'GEMDTEK bülten aboneliğini onaylayın',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.newsletter-confirmation',
            with: [
                'subscriber' => $this->subscriber,
                'confirmUrl' => route('newsletter.confirm', ['token' => $this->subscriber->confirm_token]),
                'unsubscribeUrl' => route('newsletter.unsubscribe', ['token' => $this->subscriber->unsubscribe_token]),
                'locale' => $this->subscriber->locale,
            ],
        );
    }
}
