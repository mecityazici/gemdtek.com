<?php

namespace App\Mail;

use App\Models\EventRegistration;
use App\Support\IcsGenerator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EventRegistrationConfirmed extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public EventRegistration $registration) {}

    public function envelope(): Envelope
    {
        $title = $this->registration->event?->getTranslation('title', 'tr') ?? 'Etkinlik';

        return new Envelope(subject: 'Kaydın alındı: '.$title);
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.event-registration-confirmed',
            with: [
                'registration' => $this->registration,
                'event' => $this->registration->event,
                'cancelUrl' => route('events.registrations.cancel', ['token' => $this->registration->cancel_token]),
            ],
        );
    }

    public function attachments(): array
    {
        if (! $this->registration->event) {
            return [];
        }

        return [
            Attachment::fromData(
                fn () => IcsGenerator::forEvent($this->registration->event, $this->registration->email, $this->registration->name),
                'event.ics',
            )->withMime('text/calendar'),
        ];
    }
}
