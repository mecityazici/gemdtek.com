<?php

namespace App\Filament\Imports;

use App\Models\Alumni;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class AlumniImporter extends Importer
{
    protected static ?string $model = Alumni::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->label('Ad Soyad')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:120']),
            ImportColumn::make('position')
                ->label('Pozisyon (TR)')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:160']),
            ImportColumn::make('position_en')
                ->label('Position (EN) — opsiyonel')
                ->rules(['nullable', 'string', 'max:160']),
            ImportColumn::make('graduation_year')
                ->label('Mezuniyet yılı')
                ->numeric()
                ->rules(['nullable', 'integer', 'between:1980,2100']),
            ImportColumn::make('sector')
                ->label('Sektör (tersane|klas|tasarim-ofisi|armator|akademik|yazilim|diger)')
                ->requiredMapping()
                ->rules(['required', 'in:tersane,klas,tasarim-ofisi,armator,akademik,yazilim,diger']),
            ImportColumn::make('company')
                ->label('Şirket')
                ->rules(['nullable', 'string', 'max:160']),
            ImportColumn::make('city')
                ->label('Şehir')
                ->rules(['nullable', 'string', 'max:80']),
            ImportColumn::make('linkedin_url')
                ->label('LinkedIn URL')
                ->rules(['nullable', 'url', 'max:255']),
            ImportColumn::make('order')
                ->label('Sıra')
                ->numeric()
                ->rules(['nullable', 'integer'])
                ->castStateUsing(fn ($state) => (int) ($state ?? 0)),
            ImportColumn::make('is_public')
                ->label('Public (1/0)')
                ->boolean()
                ->rules(['nullable', 'boolean'])
                ->castStateUsing(fn ($state) => filter_var($state ?? true, FILTER_VALIDATE_BOOLEAN)),
        ];
    }

    public function resolveRecord(): ?Alumni
    {
        // Doğal anahtarla eşleştir; CSV yeniden import'unda duplicate olmasın:
        // önce linkedin_url, yoksa ad + mezuniyet yılı.
        $alumni = (! empty($this->data['linkedin_url'])
            ? Alumni::query()->where('linkedin_url', $this->data['linkedin_url'])->first()
            : Alumni::query()
                ->where('name', $this->data['name'])
                ->where('graduation_year', $this->data['graduation_year'] ?? null)
                ->first())
            ?? new Alumni;
        $alumni->name = $this->data['name'];
        $alumni->setTranslation('position', 'tr', $this->data['position']);

        if (! empty($this->data['position_en'])) {
            $alumni->setTranslation('position', 'en', $this->data['position_en']);
        }

        $alumni->graduation_year = $this->data['graduation_year'] ?? null;
        $alumni->sector = $this->data['sector'];
        $alumni->company = $this->data['company'] ?? null;
        $alumni->city = $this->data['city'] ?? null;
        $alumni->linkedin_url = $this->data['linkedin_url'] ?? null;
        $alumni->order = $this->data['order'] ?? 0;
        $alumni->is_public = $this->data['is_public'] ?? true;

        return $alumni;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Mezun importu tamamlandı: '.number_format($import->successful_rows).' kayıt eklendi.';

        if ($failed = $import->getFailedRowsCount()) {
            $body .= ' '.number_format($failed).' satır başarısız.';
        }

        return $body;
    }
}
