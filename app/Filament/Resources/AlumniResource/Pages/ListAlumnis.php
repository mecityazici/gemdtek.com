<?php

namespace App\Filament\Resources\AlumniResource\Pages;

use App\Filament\Imports\AlumniImporter;
use App\Filament\Resources\AlumniResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Concerns\Translatable;

class ListAlumnis extends ListRecords
{
    use Translatable;

    protected static string $resource = AlumniResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
            Actions\ImportAction::make()
                ->importer(AlumniImporter::class)
                ->label('CSV ile içe aktar')
                ->icon('heroicon-o-arrow-up-tray'),
            Actions\CreateAction::make(),
        ];
    }
}
