<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NewsPostResource\Pages;
use App\Models\NewsPost;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class NewsPostResource extends Resource
{
    use Translatable;

    protected static ?string $model = NewsPost::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    protected static ?string $navigationGroup = 'İçerik';

    protected static ?string $modelLabel = 'Haber';

    protected static ?string $pluralModelLabel = 'Haberler';

    protected static ?int $navigationSort = 2;

    public static function getTranslatableLocales(): array
    {
        return ['tr', 'en'];
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make('News Tabs')
                ->columnSpanFull()
                ->tabs([
                    Forms\Components\Tabs\Tab::make('İçerik')
                        ->icon('heroicon-o-document-text')
                        ->schema([
                            Forms\Components\Grid::make(2)->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('Başlık')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state ?? '')))
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('slug')
                                    ->label('URL slug')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->helperText('/haberler/{slug}')
                                    ->maxLength(255),
                            ]),
                            Forms\Components\Textarea::make('excerpt')
                                ->label('Özet')
                                ->rows(2)
                                ->maxLength(500),
                            Forms\Components\RichEditor::make('content')
                                ->label('İçerik')
                                ->columnSpanFull(),
                            Forms\Components\SpatieMediaLibraryFileUpload::make('cover')
                                ->collection('cover')
                                ->label('Kapak görseli')
                                ->image()
                                ->imageEditor()
                                ->maxSize(4096),
                        ]),

                    Forms\Components\Tabs\Tab::make('Yayın')
                        ->icon('heroicon-o-megaphone')
                        ->schema([
                            Forms\Components\Grid::make(2)->schema([
                                Forms\Components\Select::make('category')
                                    ->label('Kategori')
                                    ->options(NewsPost::CATEGORIES)
                                    ->required()
                                    ->default('duyuru'),
                                Forms\Components\DateTimePicker::make('published_at')
                                    ->label('Yayın tarihi')
                                    ->seconds(false)
                                    ->default(now())
                                    ->native(false),
                            ]),
                            Forms\Components\Grid::make(2)->schema([
                                Forms\Components\TextInput::make('order')->label('Sıra')->numeric()->default(0),
                                Forms\Components\Toggle::make('is_published')->label('Yayında')->default(true),
                            ]),
                        ]),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('cover')
                    ->collection('cover')
                    ->label('Kapak')
                    ->square()
                    ->height(40),
                Tables\Columns\TextColumn::make('title')->label('Başlık')->searchable()->limit(50),
                Tables\Columns\TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => NewsPost::CATEGORIES[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'duyuru' => 'primary',
                        'blog' => 'success',
                        'basin' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('published_at')->label('Yayın')->dateTime('d M Y H:i')->placeholder('—')->sortable(),
                Tables\Columns\IconColumn::make('is_published')->label('Yayında')->boolean(),
            ])
            ->defaultSort('published_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('category')->options(NewsPost::CATEGORIES),
                Tables\Filters\TernaryFilter::make('is_published')->label('Yayında'),
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
            'index' => Pages\ListNewsPosts::route('/'),
            'create' => Pages\CreateNewsPost::route('/create'),
            'edit' => Pages\EditNewsPost::route('/{record}/edit'),
        ];
    }
}
