<?php

namespace App\Notifications;

use App\Filament\Resources\EventResource;
use App\Models\EventRegistration;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Notifications\Notification;

class NewEventRegistrationNotification extends Notification
{
    public function __construct(public EventRegistration $registration) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $eventTitle = $this->registration->event?->getTranslation('title', 'tr') ?? 'Etkinlik';

        return FilamentNotification::make()
            ->title('Yeni etkinlik kaydı onaylandı')
            ->body($this->registration->name.' ('.$this->registration->email.') — '.$eventTitle)
            ->icon('heroicon-o-ticket')
            ->iconColor('success')
            ->actions([
                Action::make('view')
                    ->label('Etkinliği gör')
                    ->url(EventResource::getUrl('edit', ['record' => $this->registration->event_id])),
            ])
            ->getDatabaseMessage();
    }
}
