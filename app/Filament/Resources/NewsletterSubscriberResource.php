<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NewsletterSubscriberResource\Pages;
use App\Models\NewsletterSubscriber;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class NewsletterSubscriberResource extends Resource
{
    protected static ?string $model = NewsletterSubscriber::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope-open';

    protected static ?string $navigationGroup = 'Bülten';

    protected static ?string $modelLabel = 'Abone';

    protected static ?string $pluralModelLabel = 'Aboneler';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Grid::make(2)->schema([
                Forms\Components\TextInput::make('email')->label('E-posta')->email()->required()->maxLength(160),
                Forms\Components\TextInput::make('name')->label('Ad')->maxLength(120),
            ]),
            Forms\Components\Grid::make(2)->schema([
                Forms\Components\Select::make('locale')->label('Dil')->options(['tr' => 'Türkçe', 'en' => 'English'])->default('tr'),
                Forms\Components\Select::make('status')->label('Durum')->options(NewsletterSubscriber::STATUSES)->default(NewsletterSubscriber::STATUS_PENDING),
            ]),
            Forms\Components\Grid::make(2)->schema([
                Forms\Components\DateTimePicker::make('confirmed_at')->label('Onaylandı'),
                Forms\Components\DateTimePicker::make('unsubscribed_at')->label('Çıktı'),
            ]),
            Forms\Components\TextInput::make('source')->label('Kaynak')->maxLength(80),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('email')->label('E-posta')->searchable()->copyable(),
                Tables\Columns\TextColumn::make('name')->label('Ad')->searchable()->placeholder('—'),
                Tables\Columns\TextColumn::make('locale')->label('Dil')->badge()->formatStateUsing(fn (string $state) => strtoupper($state)),
                Tables\Columns\TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => NewsletterSubscriber::STATUSES[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        NewsletterSubscriber::STATUS_CONFIRMED => 'success',
                        NewsletterSubscriber::STATUS_UNSUBSCRIBED => 'danger',
                        default => 'warning',
                    }),
                Tables\Columns\TextColumn::make('confirmed_at')->label('Onay')->dateTime('d M Y H:i')->sortable()->placeholder('—'),
                Tables\Columns\TextColumn::make('source')->label('Kaynak')->toggleable()->placeholder('—'),
                Tables\Columns\TextColumn::make('created_at')->label('Kayıt')->dateTime('d M Y')->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->label('Durum')->options(NewsletterSubscriber::STATUSES),
                Tables\Filters\SelectFilter::make('locale')->label('Dil')->options(['tr' => 'Türkçe', 'en' => 'English']),
            ])
            ->headerActions([
                Tables\Actions\Action::make('exportCsv')
                    ->label('CSV indir')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function () {
                        $filename = 'bulten-aboneleri-'.now()->format('Ymd-Hi').'.csv';

                        return response()->streamDownload(function () {
                            $out = fopen('php://output', 'w');
                            fputcsv($out, ['#', 'E-posta', 'Ad', 'Dil', 'Durum', 'Onay', 'Çıkış', 'Kaynak', 'Kayıt']);
                            NewsletterSubscriber::orderByDesc('created_at')->chunk(200, function ($rows) use ($out) {
                                foreach ($rows as $s) {
                                    fputcsv($out, [
                                        $s->id,
                                        $s->email,
                                        $s->name,
                                        $s->locale,
                                        NewsletterSubscriber::STATUSES[$s->status] ?? $s->status,
                                        $s->confirmed_at?->format('Y-m-d H:i'),
                                        $s->unsubscribed_at?->format('Y-m-d H:i'),
                                        $s->source,
                                        $s->created_at?->format('Y-m-d H:i'),
                                    ]);
                                }
                            });
                            fclose($out);
                        }, $filename, ['Content-Type' => 'text/csv; charset=utf-8']);
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNewsletterSubscribers::route('/'),
            'create' => Pages\CreateNewsletterSubscriber::route('/create'),
            'edit' => Pages\EditNewsletterSubscriber::route('/{record}/edit'),
        ];
    }
}
