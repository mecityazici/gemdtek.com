<?php

namespace App\Mail;

use App\Models\SponsorLead;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SponsorLeadReceived extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public SponsorLead $lead)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[GEMDTEK Sponsor Lead] ' . $this->lead->company_name,
            replyTo: [new Address($this->lead->contact_email, $this->lead->contact_name)],
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.sponsor-lead',
            with: [
                'lead'    => $this->lead,
                'tierLabel' => $this->lead->interest_tier
                    ? (SponsorLead::TIERS[$this->lead->interest_tier] ?? $this->lead->interest_tier)
                    : '—',
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
