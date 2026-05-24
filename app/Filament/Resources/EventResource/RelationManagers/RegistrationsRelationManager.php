<?php

namespace App\Filament\Resources\EventResource\RelationManagers;

use App\Models\EventRegistration;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class RegistrationsRelationManager extends RelationManager
{
    protected static string $relationship = 'registrations';

    protected static ?string $title = 'Kayıtlar';

    protected static ?string $modelLabel = 'Kayıt';

    protected static ?string $pluralModelLabel = 'Kayıtlar';

    public function form(Form $form): Form
    {
        return $form->schema([
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
                    ->default(EventRegistration::STATUS_PENDING)
                    ->required(),
                Forms\Components\DateTimePicker::make('confirmed_at')->label('Onay'),
                Forms\Components\DateTimePicker::make('cancelled_at')->label('İptal'),
            ]),
            Forms\Components\Textarea::make('notes')->label('Notlar')->rows(3)->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
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
                Tables\Columns\TextColumn::make('created_at')->label('Kayıt')->dateTime('d M Y H:i')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->label('Durum')->options(EventRegistration::STATUSES),
                Tables\Filters\SelectFilter::make('affiliation')->label('Bağlantı')->options(EventRegistration::AFFILIATIONS),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Manuel kayıt ekle')
                    ->icon('heroicon-o-user-plus'),
                Tables\Actions\Action::make('exportCsv')
                    ->label('CSV indir')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function () {
                        $event = $this->getOwnerRecord();
                        $filename = 'kayitlar-'.$event->slug.'-'.now()->format('Ymd-Hi').'.csv';

                        return response()->streamDownload(function () use ($event) {
                            $out = fopen('php://output', 'w');
                            fputcsv($out, ['#', 'Ad', 'E-posta', 'Telefon', 'Bağlantı', 'Durum', 'Onay', 'Kayıt', 'Notlar']);
                            $event->registrations()->orderByDesc('created_at')->chunk(200, function ($rows) use ($out) {
                                foreach ($rows as $r) {
                                    fputcsv($out, [
                                        $r->id,
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
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Henüz kayıt yok')
            ->emptyStateDescription('Public RSVP açıldıktan sonra kayıtlar burada listelenir.');
    }
}
