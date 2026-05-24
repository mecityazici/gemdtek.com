<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventRegistrationResource\Pages;
use App\Models\Event;
use App\Models\EventRegistration;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EventRegistrationResource extends Resource
{
    protected static ?string $model = EventRegistration::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationGroup = 'İçerik';

    protected static ?string $modelLabel = 'Etkinlik Kaydı';

    protected static ?string $pluralModelLabel = 'Etkinlik Kayıtları';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('event_id')
                ->label('Etkinlik')
                ->relationship('event', 'slug')
                ->getOptionLabelFromRecordUsing(fn (Event $record): string => $record->getTranslation('title', 'tr'))
                ->required()
                ->searchable()
                ->preload(),
            Forms\Components\Grid::make(2)->schema([
                Forms\Components\TextInput::make('name')->label('Ad Soyad')->required()->maxLength(120),
                Forms\Components\TextInput::make('email')->label('E-posta')->email()->required()->maxLength(160),
            ]),
            Forms\Components\Grid::make(2)->schema([
                Forms\Components\TextInput::make('phone')->label('Telefon')->maxLength(40),
                Forms\Components\Select::make('affiliation')
                    ->label('Bağlantı')
                    ->options(EventRegistration::AFFILIATIONS)
                    ->placeholder('—'),
            ]),
            Forms\Components\Grid::make(3)->schema([
                Forms\Components\Select::make('status')
                    ->label('Durum')
                    ->options(EventRegistration::STATUSES)
                    ->required(),
                Forms\Components\DateTimePicker::make('confirmed_at')->label('Onay'),
                Forms\Components\DateTimePicker::make('cancelled_at')->label('İptal'),
            ]),
            Forms\Components\Textarea::make('notes')->label('Notlar')->rows(3)->columnSpanFull(),
            Forms\Components\TextInput::make('source')->label('Kaynak')->disabled(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('event.title')
                    ->label('Etkinlik')
                    ->formatStateUsing(fn ($state, EventRegistration $record) => $record->event?->getTranslation('title', 'tr') ?? '—')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')->label('Ad')->searchable(),
                Tables\Columns\TextColumn::make('email')->label('E-posta')->searchable()->copyable(),
                Tables\Columns\TextColumn::make('phone')->label('Telefon')->toggleable()->placeholder('—'),
                Tables\Columns\TextColumn::make('affiliation')
                    ->label('Bağlantı')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => $state ? (EventRegistration::AFFILIATIONS[$state] ?? $state) : '—'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => EventRegistration::STATUSES[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        EventRegistration::STATUS_CONFIRMED => 'success',
                        EventRegistration::STATUS_CANCELLED => 'danger',
                        EventRegistration::STATUS_WAITLIST => 'info',
                        default => 'warning',
                    }),
                Tables\Columns\TextColumn::make('confirmed_at')->label('Onay')->dateTime('d M Y H:i')->placeholder('—')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label('Kayıt')->dateTime('d M Y H:i')->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->label('Durum')->options(EventRegistration::STATUSES),
                Tables\Filters\SelectFilter::make('event_id')
                    ->label('Etkinlik')
                    ->relationship('event', 'slug')
                    ->getOptionLabelFromRecordUsing(fn (Event $record): string => $record->getTranslation('title', 'tr'))
                    ->searchable()
                    ->preload(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('exportCsv')
                    ->label('CSV indir')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function () {
                        $filename = 'etkinlik-kayitlari-'.now()->format('Ymd-Hi').'.csv';

                        return response()->streamDownload(function () {
                            $out = fopen('php://output', 'w');
                            fputcsv($out, ['#', 'Etkinlik', 'Ad', 'E-posta', 'Telefon', 'Bağlantı', 'Durum', 'Onay', 'Kayıt', 'Notlar']);
                            EventRegistration::with('event')->orderByDesc('created_at')->chunk(200, function ($rows) use ($out) {
                                foreach ($rows as $r) {
                                    fputcsv($out, [
                                        $r->id,
                                        $r->event?->getTranslation('title', 'tr'),
                                        $r->name,
                                        $r->email,
                                        $r->phone,
                                        EventRegistration::AFFILIATIONS[$r->affiliation] ?? $r->affiliation,
                                        EventRegistration::STATUSES[$r->status] ?? $r->status,
                                        $r->confirmed_at?->format('Y-m-d H:i'),
                                        $r->created_at?->format('Y-m-d H:i'),
                                        $r->notes,
                                    ]);
                                }
                            });
                            fclose($out);
                        }, $filename, ['Content-Type' => 'text/csv; charset=utf-8']);
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('confirm')
                    ->label('Onayla')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (EventRegistration $r) => $r->status !== EventRegistration::STATUS_CONFIRMED && $r->status !== EventRegistration::STATUS_CANCELLED)
                    ->action(fn (EventRegistration $r) => $r->confirm()),
                Tables\Actions\Action::make('cancel')
                    ->label('İptal')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (EventRegistration $r) => $r->status !== EventRegistration::STATUS_CANCELLED)
                    ->requiresConfirmation()
                    ->action(fn (EventRegistration $r) => $r->cancel()),
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListEventRegistrations::route('/'),
            'create' => Pages\CreateEventRegistration::route('/create'),
            'edit' => Pages\EditEventRegistration::route('/{record}/edit'),
        ];
    }
}
