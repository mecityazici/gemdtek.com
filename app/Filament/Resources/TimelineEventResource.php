<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TimelineEventResource\Pages;
use App\Models\TimelineEvent;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TimelineEventResource extends Resource
{
    protected static ?string $model = TimelineEvent::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationGroup = 'Kurumsal';

    protected static ?string $modelLabel = 'Kurumsal Hafıza';

    protected static ?string $pluralModelLabel = 'Kurumsal Hafıza';

    protected static ?int $navigationSort = 30;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('year')
                ->label('Yıl')
                ->numeric()
                ->required()
                ->minValue(1900)
                ->maxValue(2100),
            Forms\Components\TextInput::make('title')
                ->label('Başlık')
                ->required()
                ->maxLength(255),
            Forms\Components\Textarea::make('description')
                ->label('Açıklama')
                ->rows(4)
                ->maxLength(1000),
            Forms\Components\TextInput::make('order')
                ->label('Aynı yıl içinde sıra')
                ->numeric()
                ->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('year')->label('Yıl')->sortable()->weight('bold'),
                Tables\Columns\TextColumn::make('title')->label('Başlık')->searchable(),
                Tables\Columns\TextColumn::make('description')->label('Açıklama')->limit(60),
            ])
            ->defaultSort('year', 'desc')
            ->filters([])
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
            'index'  => Pages\ListTimelineEvents::route('/'),
            'create' => Pages\CreateTimelineEvent::route('/create'),
            'edit'   => Pages\EditTimelineEvent::route('/{record}/edit'),
        ];
    }
}
