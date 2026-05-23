<?php

namespace App\Filament\Resources\SiteMetricResource\Pages;

use App\Filament\Resources\SiteMetricResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Concerns\Translatable;

class ListSiteMetrics extends ListRecords
{
    use Translatable;

    protected static string $resource = SiteMetricResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
            Actions\CreateAction::make(),
        ];
    }
}
