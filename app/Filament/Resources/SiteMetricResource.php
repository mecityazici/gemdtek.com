<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SiteMetricResource\Pages;
use App\Models\SiteMetric;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SiteMetricResource extends Resource
{
    use Translatable;

    protected static ?string $model = SiteMetric::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationGroup = 'Site Ayarları';

    protected static ?string $modelLabel = 'Sayaç';

    protected static ?string $pluralModelLabel = 'Sayaç Metrikleri';

    protected static ?int $navigationSort = 1;

    public static function getTranslatableLocales(): array
    {
        return ['tr', 'en'];
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Grid::make(2)->schema([
                Forms\Components\TextInput::make('key')
                    ->label('Teknik anahtar')
                    ->required()
                    ->alphaDash()
                    ->unique(ignoreRecord: true)
                    ->helperText('home view tarafından kullanılır — örn. members, projects.')
                    ->maxLength(60),
                Forms\Components\TextInput::make('value')
                    ->label('Sayı')
                    ->numeric()
                    ->required()
                    ->default(0),
            ]),
            Forms\Components\TextInput::make('label')
                ->label('Etiket (ana sayfada görünür)')
                ->required()
                ->maxLength(120),
            Forms\Components\Grid::make(2)->schema([
                Forms\Components\TextInput::make('order')->label('Sıra')->numeric()->default(0),
                Forms\Components\Toggle::make('is_active')->label('Aktif')->default(true),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')->label('Anahtar')->fontFamily('mono')->color('gray'),
                Tables\Columns\TextColumn::make('label')->label('Etiket')->searchable(),
                Tables\Columns\TextColumn::make('value')->label('Sayı')->numeric()->sortable()->weight('bold'),
                Tables\Columns\IconColumn::make('is_active')->label('Aktif')->boolean(),
                Tables\Columns\TextColumn::make('order')->label('Sıra')->sortable(),
            ])
            ->defaultSort('order')
            ->reorderable('order')
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
            'index' => Pages\ListSiteMetrics::route('/'),
            'create' => Pages\CreateSiteMetric::route('/create'),
            'edit' => Pages\EditSiteMetric::route('/{record}/edit'),
        ];
    }
}
