<?php

namespace App\Filament\Widgets;

use App\Models\FormSubmission;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentSubmissions extends BaseWidget
{
    protected static ?int $sort = 2;

    protected static ?string $heading = 'Son Başvurular';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                FormSubmission::query()
                    ->with('form')
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('#')->size('xs')->color('gray'),
                Tables\Columns\TextColumn::make('form.title')
                    ->label('Form')
                    ->formatStateUsing(fn ($state, $record) => $record->form?->title ?? '—')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('email')
                    ->label('E-posta')
                    ->getStateUsing(fn ($record) => $record->data['email'] ?? '—')
                    ->copyable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Gönderim')
                    ->since()
                    ->tooltip(fn ($record) => $record->created_at?->format('d M Y H:i')),
            ])
            ->paginated(false)
            ->emptyStateHeading('Henüz başvuru yok')
            ->emptyStateDescription('Yeni formlar açıldıkça burada listelenecek.');
    }
}
