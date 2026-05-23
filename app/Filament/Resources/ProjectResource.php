<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Models\Project;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-beaker';

    protected static ?string $navigationGroup = 'Ar-Ge';

    protected static ?string $modelLabel = 'Proje';

    protected static ?string $pluralModelLabel = 'Projeler';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make('Project Tabs')
                ->columnSpanFull()
                ->tabs([
                    Forms\Components\Tabs\Tab::make('Genel')
                        ->icon('heroicon-o-information-circle')
                        ->schema([
                            Forms\Components\Grid::make(2)->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Proje adı')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state ?? '')))
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('slug')
                                    ->label('URL slug')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->helperText('/ar-ge/{slug} olarak yayınlanır.')
                                    ->maxLength(255),
                            ]),
                            Forms\Components\Textarea::make('summary')
                                ->label('Kısa özet')
                                ->required()
                                ->rows(2)
                                ->maxLength(500)
                                ->helperText('Kart ve liste sayfalarında görünür.'),
                            Forms\Components\RichEditor::make('description')
                                ->label('Teknik açıklama')
                                ->columnSpanFull(),
                            Forms\Components\Textarea::make('problem_statement')
                                ->label('Çözdüğü sektörel problem')
                                ->rows(3)
                                ->maxLength(1000),
                            Forms\Components\Grid::make(3)->schema([
                                Forms\Components\TextInput::make('year')
                                    ->label('Yıl')
                                    ->numeric()
                                    ->minValue(2000)
                                    ->maxValue(2100),
                                Forms\Components\Select::make('status')
                                    ->label('Durum')
                                    ->options(Project::STATUSES)
                                    ->required()
                                    ->default('active'),
                                Forms\Components\Select::make('captain_user_id')
                                    ->label('Takım Kaptanı (kullanıcı)')
                                    ->options(fn () => User::whereHas('roles', fn ($q) => $q->where('name', 'team_captain'))->pluck('name', 'id'))
                                    ->searchable()
                                    ->helperText('Bu projeyi düzenleme yetkisi verilecek team_captain kullanıcısı.'),
                            ]),
                            Forms\Components\Grid::make(2)->schema([
                                Forms\Components\TextInput::make('order')
                                    ->label('Sıralama')
                                    ->numeric()
                                    ->default(0),
                                Forms\Components\Toggle::make('is_active')
                                    ->label('Aktif')
                                    ->default(true),
                            ]),
                        ]),

                    Forms\Components\Tabs\Tab::make('Teknik Spec')
                        ->icon('heroicon-o-table-cells')
                        ->schema([
                            Forms\Components\Repeater::make('specs')
                                ->relationship()
                                ->label('Teknik özellikler')
                                ->schema([
                                    Forms\Components\Select::make('category')
                                        ->label('Kategori')
                                        ->options(Project::SPEC_CATEGORIES)
                                        ->required()
                                        ->default('genel'),
                                    Forms\Components\TextInput::make('key')
                                        ->label('Özellik')
                                        ->required(),
                                    Forms\Components\TextInput::make('value')
                                        ->label('Değer')
                                        ->required(),
                                ])
                                ->columns(3)
                                ->orderColumn('order')
                                ->collapsible()
                                ->itemLabel(fn (array $state): ?string => $state['key'] ?? null)
                                ->addActionLabel('Yeni özellik ekle')
                                ->defaultItems(0),
                        ]),

                    Forms\Components\Tabs\Tab::make('Takım')
                        ->icon('heroicon-o-user-group')
                        ->schema([
                            Forms\Components\Repeater::make('members')
                                ->relationship()
                                ->label('Takım üyeleri')
                                ->schema([
                                    Forms\Components\TextInput::make('name')->label('İsim')->required(),
                                    Forms\Components\TextInput::make('role')->label('Görev')->required(),
                                    Forms\Components\TextInput::make('linkedin_url')->label('LinkedIn')->url(),
                                    Forms\Components\Toggle::make('is_captain')->label('Takım kaptanı')->default(false),
                                ])
                                ->columns(2)
                                ->orderColumn('order')
                                ->collapsible()
                                ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                                ->addActionLabel('Yeni üye ekle')
                                ->defaultItems(0),
                        ]),

                    Forms\Components\Tabs\Tab::make('Medya')
                        ->icon('heroicon-o-photo')
                        ->schema([
                            Forms\Components\SpatieMediaLibraryFileUpload::make('hero')
                                ->collection('hero')
                                ->label('Hero görseli (tek)')
                                ->image()
                                ->imageEditor()
                                ->maxSize(4096),
                            Forms\Components\SpatieMediaLibraryFileUpload::make('gallery')
                                ->collection('gallery')
                                ->label('Galeri (çoklu)')
                                ->image()
                                ->multiple()
                                ->reorderable()
                                ->maxSize(4096)
                                ->panelLayout('grid'),
                            Forms\Components\SpatieMediaLibraryFileUpload::make('documents')
                                ->collection('documents')
                                ->label('Teknik raporlar (PDF)')
                                ->acceptedFileTypes(['application/pdf'])
                                ->multiple()
                                ->maxSize(10240)
                                ->helperText('En fazla 10 MB PDF.'),
                        ]),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('hero')
                    ->collection('hero')
                    ->label('Görsel')
                    ->square()
                    ->height(50),
                Tables\Columns\TextColumn::make('name')->label('Proje')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('year')->label('Yıl')->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Project::STATUSES[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'active'    => 'success',
                        'completed' => 'gray',
                        'upcoming'  => 'warning',
                        default     => 'info',
                    }),
                Tables\Columns\TextColumn::make('captainUser.name')->label('Kaptan')->placeholder('—'),
                Tables\Columns\IconColumn::make('is_active')->label('Aktif')->boolean(),
                Tables\Columns\TextColumn::make('order')->label('Sıra')->sortable(),
            ])
            ->defaultSort('order')
            ->reorderable('order')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(Project::STATUSES),
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

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        $user = auth()->user();
        if ($user && $user->hasRole('team_captain') && ! $user->hasRole('super_admin')) {
            $query->where('captain_user_id', $user->id);
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit'   => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
