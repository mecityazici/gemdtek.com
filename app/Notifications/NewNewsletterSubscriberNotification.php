<?php

namespace App\Notifications;

use App\Filament\Resources\NewsletterSubscriberResource;
use App\Models\NewsletterSubscriber;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Notifications\Notification;

class NewNewsletterSubscriberNotification extends Notification
{
    public function __construct(public NewsletterSubscriber $subscriber) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return FilamentNotification::make()
            ->title('Yeni bülten abonesi onaylandı')
            ->body($this->subscriber->email.' ('.strtoupper($this->subscriber->locale).')')
            ->icon('heroicon-o-envelope-open')
            ->iconColor('info')
            ->actions([
                Action::make('view')
                    ->label('Aboneyi gör')
                    ->url(NewsletterSubscriberResource::getUrl('edit', ['record' => $this->subscriber])),
            ])
            ->getDatabaseMessage();
    }
}
