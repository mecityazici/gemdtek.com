<?php

namespace App\Notifications;

use App\Filament\Resources\SponsorLeadResource;
use App\Models\SponsorLead;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Notifications\Notification;

class NewSponsorLeadNotification extends Notification
{
    public function __construct(public SponsorLead $lead) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return FilamentNotification::make()
            ->title('Yeni sponsor başvurusu')
            ->body($this->lead->company_name.' — '.$this->lead->contact_name.' ('.$this->lead->contact_email.')')
            ->icon('heroicon-o-briefcase')
            ->iconColor('warning')
            ->actions([
                Action::make('view')
                    ->label('Detay')
                    ->url(SponsorLeadResource::getUrl('edit', ['record' => $this->lead])),
            ])
            ->getDatabaseMessage();
    }
}
