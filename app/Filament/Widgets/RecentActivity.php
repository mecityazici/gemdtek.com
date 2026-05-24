<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Spatie\Activitylog\Models\Activity;

class RecentActivity extends BaseWidget
{
    protected static ?int $sort = 4;

    protected static ?string $heading = 'Son Aktiviteler';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(Activity::query()->latest()->limit(10))
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Zaman')
                    ->since()
                    ->tooltip(fn (Activity $record) => $record->created_at?->format('d M Y H:i:s')),
                Tables\Columns\TextColumn::make('event')
                    ->label('Olay')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('subject_label')
                    ->label('Kayıt')
                    ->getStateUsing(fn (Activity $record) => ($record->subject_type ? class_basename($record->subject_type) : '—').' #'.$record->subject_id)
                    ->fontFamily('mono')
                    ->size('xs'),
                Tables\Columns\TextColumn::make('causer_label')
                    ->label('Kim')
                    ->getStateUsing(fn (Activity $record) => $record->causer?->name ?? 'Sistem'),
            ])
            ->paginated(false)
            ->emptyStateHeading('Henüz aktivite yok')
            ->emptyStateDescription('Admin değişiklikleri burada listelenecek.');
    }
}
