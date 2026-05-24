<?php

namespace App\Filament\Resources\SponsorResource\Pages;

use App\Filament\Imports\SponsorImporter;
use App\Filament\Resources\SponsorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Concerns\Translatable;

class ListSponsors extends ListRecords
{
    use Translatable;

    protected static string $resource = SponsorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
            Actions\ImportAction::make()
                ->importer(SponsorImporter::class)
                ->label('CSV ile içe aktar')
                ->icon('heroicon-o-arrow-up-tray'),
            Actions\CreateAction::make(),
        ];
    }
}
