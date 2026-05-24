<?php

namespace App\Mail;

use App\Models\EventRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EventRegistrationConfirmation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public EventRegistration $registration) {}

    public function envelope(): Envelope
    {
        $title = $this->registration->event?->getTranslation('title', 'tr') ?? 'Etkinlik';

        return new Envelope(subject: 'GEMDTEK kaydını onayla: '.$title);
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.event-registration-confirmation',
            with: [
                'registration' => $this->registration,
                'event' => $this->registration->event,
                'confirmUrl' => route('events.registrations.confirm', ['token' => $this->registration->confirm_token]),
                'cancelUrl' => route('events.registrations.cancel', ['token' => $this->registration->cancel_token]),
            ],
        );
    }
}
