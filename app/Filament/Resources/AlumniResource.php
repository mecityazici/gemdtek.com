<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AlumniResource\Pages;
use App\Models\Alumni;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AlumniResource extends Resource
{
    use Translatable;

    protected static ?string $model = Alumni::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'Kurumsal';

    protected static ?string $modelLabel = 'Mezun';

    protected static ?string $pluralModelLabel = 'Mezunlar';

    protected static ?int $navigationSort = 40;

    public static function getTranslatableLocales(): array
    {
        return ['tr', 'en'];
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make('Alumni Tabs')
                ->columnSpanFull()
                ->tabs([
                    Forms\Components\Tabs\Tab::make('Genel')
                        ->icon('heroicon-o-user')
                        ->schema([
                            Forms\Components\Grid::make(2)->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Ad Soyad')
                                    ->required()
                                    ->maxLength(120),
                                Forms\Components\TextInput::make('graduation_year')
                                    ->label('Mezuniyet yılı')
                                    ->numeric()
                                    ->minValue(1980)
                                    ->maxValue(2100),
                            ]),
                            Forms\Components\TextInput::make('position')
                                ->label('Pozisyon')
                                ->required()
                                ->maxLength(160),
                            Forms\Components\Grid::make(2)->schema([
                                Forms\Components\TextInput::make('company')
                                    ->label('Şirket / Kurum')
                                    ->maxLength(160),
                                Forms\Components\Select::make('sector')
                                    ->label('Sektör')
                                    ->options(Alumni::SECTORS)
                                    ->required()
                                    ->default('diger'),
                            ]),
                            Forms\Components\Grid::make(2)->schema([
                                Forms\Components\TextInput::make('city')
                                    ->label('Şehir')
                                    ->maxLength(80),
                                Forms\Components\TextInput::make('linkedin_url')
                                    ->label('LinkedIn URL')
                                    ->url()
                                    ->maxLength(255),
                            ]),
                            Forms\Components\Grid::make(2)->schema([
                                Forms\Components\TextInput::make('order')->label('Sıra')->numeric()->default(0),
                                Forms\Components\Toggle::make('is_public')->label('Public listede göster')->default(true),
                            ]),
                        ]),

                    Forms\Components\Tabs\Tab::make('Biyografi')
                        ->icon('heroicon-o-document-text')
                        ->schema([
                            Forms\Components\Textarea::make('bio')
                                ->label('Kısa biyografi')
                                ->rows(5)
                                ->maxLength(1500),
                        ]),

                    Forms\Components\Tabs\Tab::make('Fotoğraf')
                        ->icon('heroicon-o-photo')
                        ->schema([
                            Forms\Components\SpatieMediaLibraryFileUpload::make('photo')
                                ->collection('photo')
                                ->label('Portre')
                                ->image()
                                ->imageEditor()
                                ->maxSize(3072),
                        ]),
                ]),
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
                Tables\Columns\TextColumn::make('position')->label('Pozisyon')->limit(40)->searchable(),
                Tables\Columns\TextColumn::make('company')->label('Şirket')->searchable()->placeholder('—'),
                Tables\Columns\TextColumn::make('sector')
                    ->label('Sektör')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Alumni::SECTORS[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'tersane'        => 'primary',
                        'klas'           => 'success',
                        'tasarim-ofisi'  => 'warning',
                        'armator'        => 'info',
                        'akademik'       => 'gray',
                        'yazilim'        => 'danger',
                        default          => 'gray',
                    }),
                Tables\Columns\TextColumn::make('graduation_year')->label('Yıl')->sortable(),
                Tables\Columns\IconColumn::make('is_public')->label('Public')->boolean(),
            ])
            ->defaultSort('graduation_year', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('sector')->options(Alumni::SECTORS),
                Tables\Filters\TernaryFilter::make('is_public')->label('Public'),
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
            'index'  => Pages\ListAlumnis::route('/'),
            'create' => Pages\CreateAlumni::route('/create'),
            'edit'   => Pages\EditAlumni::route('/{record}/edit'),
        ];
    }
}
