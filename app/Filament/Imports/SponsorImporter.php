<?php

namespace App\Filament\Imports;

use App\Models\Sponsor;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class SponsorImporter extends Importer
{
    protected static ?string $model = Sponsor::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->label('İsim (TR)')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:255']),
            ImportColumn::make('name_en')
                ->label('Name (EN) — opsiyonel')
                ->rules(['nullable', 'string', 'max:255']),
            ImportColumn::make('url')
                ->label('Web sitesi')
                ->rules(['nullable', 'url', 'max:255']),
            ImportColumn::make('tier')
                ->label('Seviye (platinum|gold|silver|bronze|destek)')
                ->requiredMapping()
                ->rules(['required', 'in:platinum,gold,silver,bronze,destek']),
            ImportColumn::make('order')
                ->label('Sıra')
                ->numeric()
                ->rules(['nullable', 'integer'])
                ->castStateUsing(fn ($state) => (int) ($state ?? 0)),
            ImportColumn::make('is_active')
                ->label('Aktif (1/0)')
                ->boolean()
                ->rules(['nullable', 'boolean'])
                ->castStateUsing(fn ($state) => filter_var($state ?? true, FILTER_VALIDATE_BOOLEAN)),
        ];
    }

    public function resolveRecord(): ?Sponsor
    {
        // Aynı TR isimli sponsor varsa güncelle; CSV yeniden import'unda duplicate olmasın.
        $sponsor = Sponsor::query()->where('name->tr', $this->data['name'])->first() ?? new Sponsor;
        $sponsor->setTranslation('name', 'tr', $this->data['name']);

        if (! empty($this->data['name_en'])) {
            $sponsor->setTranslation('name', 'en', $this->data['name_en']);
        }

        $sponsor->url = $this->data['url'] ?? null;
        $sponsor->tier = $this->data['tier'];
        $sponsor->order = $this->data['order'] ?? 0;
        $sponsor->is_active = $this->data['is_active'] ?? true;

        return $sponsor;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Sponsor importu tamamlandı: '.number_format($import->successful_rows).' kayıt eklendi.';

        if ($failed = $import->getFailedRowsCount()) {
            $body .= ' '.number_format($failed).' satır başarısız.';
        }

        return $body;
    }
}
