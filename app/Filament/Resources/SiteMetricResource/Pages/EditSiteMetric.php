<?php

namespace App\Filament\Resources\SiteMetricResource\Pages;

use App\Filament\Resources\SiteMetricResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\EditRecord\Concerns\Translatable;

class EditSiteMetric extends EditRecord
{
    use Translatable;

    protected static string $resource = SiteMetricResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
