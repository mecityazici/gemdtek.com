<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SponsorResource\Pages;
use App\Models\Sponsor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SponsorResource extends Resource
{
    protected static ?string $model = Sponsor::class;

    protected static ?string $navigationIcon = 'heroicon-o-trophy';

    protected static ?string $navigationGroup = 'Kurumsal';

    protected static ?string $modelLabel = 'Sponsor';

    protected static ?string $pluralModelLabel = 'Sponsorlar';

    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('İsim')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('url')
                ->label('Web sitesi')
                ->url()
                ->maxLength(255),
            Forms\Components\Select::make('tier')
                ->label('Seviye')
                ->options(Sponsor::TIERS)
                ->required()
                ->default('destek'),
            Forms\Components\SpatieMediaLibraryFileUpload::make('logo')
                ->collection('logo')
                ->label('Logo')
                ->image()
                ->maxSize(2048)
                ->helperText('PNG/SVG önerilir, en fazla 2 MB.'),
            Forms\Components\TextInput::make('order')
                ->label('Sıralama')
                ->numeric()
                ->default(0),
            Forms\Components\Toggle::make('is_active')
                ->label('Aktif')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('logo')
                    ->collection('logo')
                    ->label('Logo')
                    ->height(40),
                Tables\Columns\TextColumn::make('name')
                    ->label('İsim')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tier')
                    ->label('Seviye')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Sponsor::TIERS[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'platinum' => 'primary',
                        'gold'     => 'warning',
                        'silver'   => 'gray',
                        'bronze'   => 'danger',
                        default    => 'info',
                    }),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                Tables\Columns\TextColumn::make('order')
                    ->label('Sıra')
                    ->sortable(),
            ])
            ->defaultSort('order')
            ->reorderable('order')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Aktif'),
                Tables\Filters\SelectFilter::make('tier')->label('Seviye')->options(Sponsor::TIERS),
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
            'index'  => Pages\ListSponsors::route('/'),
            'create' => Pages\CreateSponsor::route('/create'),
            'edit'   => Pages\EditSponsor::route('/{record}/edit'),
        ];
    }
}
