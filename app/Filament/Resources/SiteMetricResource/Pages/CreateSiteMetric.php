<?php

namespace App\Filament\Resources\SiteMetricResource\Pages;

use App\Filament\Resources\SiteMetricResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\Translatable;

class CreateSiteMetric extends CreateRecord
{
    use Translatable;

    protected static string $resource = SiteMetricResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\LocaleSwitcher::make()];
    }
}
