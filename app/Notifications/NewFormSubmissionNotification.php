<?php

namespace App\Notifications;

use App\Filament\Resources\FormResource;
use App\Models\FormSubmission;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Notifications\Notification;

class NewFormSubmissionNotification extends Notification
{
    public function __construct(public FormSubmission $submission) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $formTitle = $this->submission->form?->title ?? 'Form';

        return FilamentNotification::make()
            ->title('Yeni form gönderimi: '.$formTitle)
            ->body($this->summary())
            ->icon('heroicon-o-document-text')
            ->iconColor('success')
            ->actions([
                Action::make('view')
                    ->label('Forma git')
                    ->url(FormResource::getUrl('edit', ['record' => $this->submission->form_id])),
            ])
            ->getDatabaseMessage();
    }

    private function summary(): string
    {
        $data = $this->submission->data ?? [];
        $name = $data['ad_soyad'] ?? $data['name'] ?? $data['isim'] ?? null;
        $email = $data['eposta'] ?? $data['email'] ?? null;

        return collect([$name, $email])->filter()->implode(' — ') ?: '#'.$this->submission->id;
    }
}
