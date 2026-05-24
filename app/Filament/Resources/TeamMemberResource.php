<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeamMemberResource\Pages;
use App\Models\TeamMember;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TeamMemberResource extends Resource
{
    use Translatable;

    protected static ?string $model = TeamMember::class;

    public static function getTranslatableLocales(): array
    {
        return ['tr', 'en'];
    }

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Kurumsal';

    protected static ?string $modelLabel = 'Yönetim Üyesi';

    protected static ?string $pluralModelLabel = 'Yönetim Kurulu';

    protected static ?int $navigationSort = 20;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('İsim Soyisim')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('position')
                ->label('Pozisyon / Görev')
                ->required()
                ->maxLength(255),
            Forms\Components\Textarea::make('bio')
                ->label('Kısa biyografi')
                ->rows(4)
                ->maxLength(1000),
            Forms\Components\TextInput::make('linkedin_url')
                ->label('LinkedIn URL')
                ->url()
                ->maxLength(255),
            Forms\Components\SpatieMediaLibraryFileUpload::make('photo')
                ->collection('photo')
                ->label('Portre Fotoğrafı')
                ->image()
                ->imageEditor()
                ->maxSize(3072)
                ->helperText('Kare oranda, yüksek çözünürlüklü öneriyoruz.'),
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
                Tables\Columns\SpatieMediaLibraryImageColumn::make('photo')
                    ->collection('photo')
                    ->circular()
                    ->label('Foto'),
                Tables\Columns\TextColumn::make('name')->label('İsim')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('position')->label('Görev')->searchable(),
                Tables\Columns\IconColumn::make('is_active')->label('Aktif')->boolean(),
                Tables\Columns\TextColumn::make('order')->label('Sıra')->sortable(),
            ])
            ->defaultSort('order')
            ->reorderable('order')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Aktif'),
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
            'index' => Pages\ListTeamMembers::route('/'),
            'create' => Pages\CreateTeamMember::route('/create'),
            'edit' => Pages\EditTeamMember::route('/{record}/edit'),
        ];
    }
}
