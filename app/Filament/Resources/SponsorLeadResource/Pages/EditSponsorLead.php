<?php

namespace App\Filament\Resources\SponsorLeadResource\Pages;

use App\Filament\Resources\SponsorLeadResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSponsorLead extends EditRecord
{
    protected static string $resource = SponsorLeadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
