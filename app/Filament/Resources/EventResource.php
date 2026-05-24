<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventResource\Pages;
use App\Models\Event;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class EventResource extends Resource
{
    use Translatable;

    protected static ?string $model = Event::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'İçerik';

    protected static ?string $modelLabel = 'Etkinlik';

    protected static ?string $pluralModelLabel = 'Etkinlikler';

    protected static ?int $navigationSort = 1;

    public static function getTranslatableLocales(): array
    {
        return ['tr', 'en'];
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make('Event Tabs')
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
                                    ->helperText('/etkinlikler/{slug}')
                                    ->maxLength(255),
                            ]),
                            Forms\Components\Textarea::make('summary')
                                ->label('Kısa özet')
                                ->rows(2)
                                ->maxLength(500),
                            Forms\Components\RichEditor::make('description')
                                ->label('Detaylı açıklama')
                                ->columnSpanFull(),
                            Forms\Components\SpatieMediaLibraryFileUpload::make('cover')
                                ->collection('cover')
                                ->label('Kapak görseli')
                                ->image()
                                ->imageEditor()
                                ->maxSize(4096),
                        ]),

                    Forms\Components\Tabs\Tab::make('Detaylar')
                        ->icon('heroicon-o-clock')
                        ->schema([
                            Forms\Components\Grid::make(2)->schema([
                                Forms\Components\DateTimePicker::make('event_date')
                                    ->label('Etkinlik tarihi')
                                    ->required()
                                    ->seconds(false)
                                    ->native(false),
                                Forms\Components\Select::make('category')
                                    ->label('Kategori')
                                    ->options(Event::CATEGORIES)
                                    ->required()
                                    ->default('etkinlik'),
                            ]),
                            Forms\Components\TextInput::make('location')
                                ->label('Lokasyon')
                                ->maxLength(255),
                            Forms\Components\TextInput::make('registration_url')
                                ->label('Harici kayıt URL')
                                ->url()
                                ->helperText('Site-dışı bir kayıt sayfası kullanılacaksa buraya yaz. Boşsa dahili RSVP açılabilir.'),
                            Forms\Components\Section::make('Site içi RSVP')
                                ->description('Açıkken ziyaretçiler etkinlik sayfasından doğrudan kayıt olabilir.')
                                ->schema([
                                    Forms\Components\Grid::make(3)->schema([
                                        Forms\Components\Toggle::make('registration_enabled')->label('RSVP aç')->default(false),
                                        Forms\Components\TextInput::make('capacity')
                                            ->label('Kapasite')
                                            ->numeric()
                                            ->minValue(1)
                                            ->helperText('Boş = sınırsız'),
                                        Forms\Components\DateTimePicker::make('registration_deadline')
                                            ->label('Son kayıt tarihi')
                                            ->seconds(false)
                                            ->native(false),
                                    ]),
                                ])
                                ->collapsed(fn ($record) => ! ($record?->registration_enabled)),
                            Forms\Components\Grid::make(2)->schema([
                                Forms\Components\TextInput::make('order')->label('Sıra')->numeric()->default(0),
                                Forms\Components\Toggle::make('is_active')->label('Aktif')->default(true),
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
                Tables\Columns\TextColumn::make('event_date')->label('Tarih')->dateTime('d M Y H:i')->sortable(),
                Tables\Columns\TextColumn::make('location')->label('Lokasyon')->placeholder('—')->limit(30),
                Tables\Columns\TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Event::CATEGORIES[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'zirve' => 'primary',
                        'kariyer-gunu' => 'success',
                        'atolye' => 'warning',
                        'panel' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\IconColumn::make('is_active')->label('Aktif')->boolean(),
            ])
            ->defaultSort('event_date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('category')->options(Event::CATEGORIES),
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
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }
}
