<?php

namespace App\Mail;

use App\Models\FormSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FormSubmissionReceived extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public FormSubmission $submission)
    {
        $this->submission->loadMissing('form.fields');
    }

    public function envelope(): Envelope
    {
        $formTitle = $this->submission->form?->getTranslation('title', 'tr') ?? 'Form';

        return new Envelope(
            subject: "[GEMDTEK Başvuru] {$formTitle} — #{$this->submission->id}",
        );
    }

    public function content(): Content
    {
        $form = $this->submission->form;
        $fields = $form?->fields ?? collect();
        $data = $this->submission->data ?? [];

        $rows = $fields->map(function ($field) use ($data) {
            $value = $data[$field->name] ?? null;
            if (is_array($value)) {
                $value = implode(', ', $value);
            }

            return [
                'label' => $field->getTranslation('label', 'tr'),
                'value' => $value !== null && $value !== '' ? $value : '—',
            ];
        });

        $attachments = $this->submission->getMedia('attachments')->map(fn ($m) => [
            'field' => $m->getCustomProperty('field_name') ?? 'attachment',
            'name' => $m->file_name,
            'size' => round($m->size / 1024, 1).' KB',
        ]);

        return new Content(
            markdown: 'emails.form-submission',
            with: [
                'formTitle' => $form?->getTranslation('title', 'tr') ?? 'Form',
                'submission' => $this->submission,
                'rows' => $rows,
                'attachments' => $attachments,
                'adminUrl' => url('/admin/forms/'.$form?->id.'/edit'),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
