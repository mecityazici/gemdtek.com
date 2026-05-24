<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FormResource\Pages;
use App\Filament\Resources\FormResource\RelationManagers;
use App\Models\Form as FormModel;
use App\Models\FormField;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class FormResource extends Resource
{
    protected static ?string $model = FormModel::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Başvurular';

    protected static ?string $modelLabel = 'Form';

    protected static ?string $pluralModelLabel = 'Formlar';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make('Form Tabs')
                ->columnSpanFull()
                ->tabs([
                    Forms\Components\Tabs\Tab::make('Genel')
                        ->icon('heroicon-o-information-circle')
                        ->schema([
                            Forms\Components\Grid::make(2)->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('Form başlığı')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state ?? '')))
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('slug')
                                    ->label('URL slug')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->helperText('Public: /basvuru/{slug}')
                                    ->maxLength(255),
                            ]),
                            Forms\Components\Textarea::make('description')
                                ->label('Açıklama')
                                ->rows(3)
                                ->maxLength(1000),
                            Forms\Components\Textarea::make('success_message')
                                ->label('Başarı mesajı')
                                ->rows(2)
                                ->helperText('Form gönderildikten sonra başvurucuya gösterilir.'),
                            Forms\Components\Textarea::make('closed_message')
                                ->label('Kapalı mesajı')
                                ->rows(2)
                                ->helperText('Form kapalıyken ziyaretçilere gösterilir.'),
                        ]),

                    Forms\Components\Tabs\Tab::make('Yayın')
                        ->icon('heroicon-o-clock')
                        ->schema([
                            Forms\Components\Toggle::make('is_active')
                                ->label('Form aktif')
                                ->helperText('Aktif değilse form kapalıdır. Aktif ise tarih aralığı (varsa) kontrol edilir.')
                                ->default(false),
                            Forms\Components\Grid::make(2)->schema([
                                Forms\Components\DateTimePicker::make('starts_at')
                                    ->label('Açılış tarihi')
                                    ->seconds(false)
                                    ->helperText('Boş bırakılırsa anında açıktır.'),
                                Forms\Components\DateTimePicker::make('ends_at')
                                    ->label('Kapanış tarihi')
                                    ->seconds(false)
                                    ->helperText('Boş bırakılırsa süresiz açıktır.')
                                    ->after('starts_at'),
                            ]),
                        ]),

                    Forms\Components\Tabs\Tab::make('Alanlar')
                        ->icon('heroicon-o-list-bullet')
                        ->schema([
                            Forms\Components\Repeater::make('fields')
                                ->relationship()
                                ->label('Form alanları')
                                ->schema([
                                    Forms\Components\Grid::make(2)->schema([
                                        Forms\Components\Select::make('type')
                                            ->label('Tip')
                                            ->options(FormField::TYPES)
                                            ->required()
                                            ->live()
                                            ->default('text'),
                                        Forms\Components\TextInput::make('name')
                                            ->label('Alan adı (snake_case)')
                                            ->required()
                                            ->alphaDash()
                                            ->helperText('Veri tablosunda kullanılır — boşluksuz, küçük harf.')
                                            ->maxLength(60),
                                    ]),
                                    Forms\Components\TextInput::make('label')
                                        ->label('Etiket (görünen başlık)')
                                        ->required(),
                                    Forms\Components\Grid::make(2)->schema([
                                        Forms\Components\TextInput::make('placeholder')
                                            ->label('Placeholder'),
                                        Forms\Components\Toggle::make('is_required')
                                            ->label('Zorunlu')
                                            ->inline(false),
                                    ]),
                                    Forms\Components\Textarea::make('help_text')
                                        ->label('Yardım metni')
                                        ->rows(2),
                                    Forms\Components\TagsInput::make('options')
                                        ->label('Seçenekler')
                                        ->placeholder('Yeni seçenek ekle, Enter')
                                        ->helperText('Sadece select/radio/checkbox tipleri için.')
                                        ->visible(fn (Get $get): bool => in_array($get('type'), ['select', 'radio', 'checkbox'])),
                                ])
                                ->columns(1)
                                ->orderColumn('order')
                                ->collapsible()
                                ->itemLabel(fn (array $state): ?string => $state['label'] ?? null)
                                ->addActionLabel('Yeni alan ekle')
                                ->defaultItems(0),
                        ]),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->label('Form')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('slug')->label('Slug')->fontFamily('mono')->color('gray'),
                Tables\Columns\TextColumn::make('fields_count')->label('Alan')->counts('fields'),
                Tables\Columns\TextColumn::make('submissions_count')->label('Başvuru')->counts('submissions')->badge()->color('success'),
                Tables\Columns\IconColumn::make('is_active')->label('Aktif')->boolean(),
                Tables\Columns\TextColumn::make('starts_at')->label('Açılış')->dateTime('d M Y H:i')->placeholder('—'),
                Tables\Columns\TextColumn::make('ends_at')->label('Kapanış')->dateTime('d M Y H:i')->placeholder('—'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Aktif'),
            ])
            ->actions([
                Tables\Actions\Action::make('copyUrl')
                    ->label('URL kopyala')
                    ->icon('heroicon-o-link')
                    ->color('gray')
                    ->action(function (FormModel $record) {
                        $url = url('/basvuru/'.$record->slug);
                        Notification::make()
                            ->title('Public URL')
                            ->body($url)
                            ->success()
                            ->send();
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\SubmissionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListForms::route('/'),
            'create' => Pages\CreateForm::route('/create'),
            'edit' => Pages\EditForm::route('/{record}/edit'),
        ];
    }
}
