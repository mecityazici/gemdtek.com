<?php

namespace App\Filament\Widgets;

use App\Models\Event;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class UpcomingEvents extends BaseWidget
{
    protected static ?int $sort = 3;

    protected static ?string $heading = 'Yaklaşan Etkinlikler';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Event::active()->upcoming()->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Başlık')
                    ->formatStateUsing(fn ($state, $record) => $record->getTranslation('title', 'tr'))
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Event::CATEGORIES[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'zirve' => 'primary',
                        'kariyer-gunu' => 'success',
                        'atolye' => 'warning',
                        'panel' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('event_date')
                    ->label('Tarih')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('location')
                    ->label('Lokasyon')
                    ->placeholder('—')
                    ->limit(40),
                Tables\Columns\TextColumn::make('days_until')
                    ->label('Kalan')
                    ->getStateUsing(fn ($record) => $record->event_date?->diffForHumans(['parts' => 1]) ?? '—'),
            ])
            ->paginated(false)
            ->emptyStateHeading('Yaklaşan etkinlik yok')
            ->emptyStateDescription('Etkinlikler oluşturun, dashboard\'da otomatik görünür.');
    }
}
