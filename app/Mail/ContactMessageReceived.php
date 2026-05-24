<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactMessageReceived extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $name,
        public string $email,
        public string $messageSubject,
        public string $body,
        public string $ip,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[GEMDTEK İletişim] ' . $this->messageSubject,
            replyTo: [new Address($this->email, $this->name)],
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.contact',
            with: [
                'name'    => $this->name,
                'email'   => $this->email,
                'subject' => $this->messageSubject,
                'body'    => $this->body,
                'ip'      => $this->ip,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
