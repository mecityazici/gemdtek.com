<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Sistem';

    protected static ?string $modelLabel = 'Kullanıcı';

    protected static ?string $pluralModelLabel = 'Kullanıcılar';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Kullanıcı bilgileri')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Ad Soyad')
                        ->required()
                        ->maxLength(120),
                    Forms\Components\TextInput::make('email')
                        ->label('E-posta')
                        ->email()
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(160),
                    Forms\Components\TextInput::make('password')
                        ->label('Şifre')
                        ->password()
                        ->revealable()
                        ->required(fn (string $context): bool => $context === 'create')
                        ->minLength(8)
                        ->maxLength(120)
                        ->dehydrated(fn (?string $state): bool => filled($state))
                        ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                        ->helperText(fn (string $context): string => $context === 'edit'
                            ? 'Boş bırakırsan mevcut şifre korunur.'
                            : 'En az 8 karakter.'),
                    Forms\Components\DateTimePicker::make('email_verified_at')
                        ->label('E-posta doğrulandı')
                        ->default(now())
                        ->helperText('Yeni kullanıcılar için boş = doğrulanmamış.'),
                ]),

            Forms\Components\Section::make('Yetki / Rol')
                ->description('Bir kullanıcıya birden fazla rol verilebilir; izinler birleşir.')
                ->schema([
                    Forms\Components\Select::make('roles')
                        ->label('Roller')
                        ->relationship('roles', 'name')
                        ->multiple()
                        ->preload()
                        ->searchable()
                        ->options(fn () => Role::query()->pluck('name', 'id')->mapWithKeys(fn ($name, $id) => [
                            $id => match ($name) {
                                'super_admin' => 'Süper Admin (tüm yetkiler)',
                                'editor' => 'Editör (içerik yönetimi)',
                                'team_captain' => 'Takım Kaptanı (kendi projesi)',
                                default => $name,
                            },
                        ])),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Ad')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('email')
                    ->label('E-posta')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Kopyalandı'),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Roller')
                    ->badge()
                    ->separator(',')
                    ->color(fn (string $state): string => match ($state) {
                        'super_admin' => 'danger',
                        'editor' => 'success',
                        'team_captain' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'super_admin' => 'Süper Admin',
                        'editor' => 'Editör',
                        'team_captain' => 'Kaptan',
                        default => $state,
                    }),
                Tables\Columns\IconColumn::make('email_verified_at')
                    ->label('Doğrulandı')
                    ->boolean()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Kayıt')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('roles')
                    ->label('Rol')
                    ->relationship('roles', 'name')
                    ->multiple(),
                Tables\Filters\TernaryFilter::make('email_verified_at')
                    ->label('Doğrulanmış')
                    ->nullable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('resetPassword')
                    ->label('Şifre sıfırla')
                    ->icon('heroicon-o-key')
                    ->color('warning')
                    ->form([
                        Forms\Components\TextInput::make('new_password')
                            ->label('Yeni şifre')
                            ->password()
                            ->revealable()
                            ->required()
                            ->minLength(8)
                            ->maxLength(120),
                    ])
                    ->action(function (User $record, array $data): void {
                        $record->update(['password' => Hash::make($data['new_password'])]);
                        Notification::make()
                            ->title('Şifre güncellendi')
                            ->body("{$record->email} için yeni şifre kaydedildi.")
                            ->success()
                            ->send();
                    }),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn (User $record) => ! $record->hasRole('super_admin') || User::role('super_admin')->count() > 1)
                    ->modalDescription('Bu kullanıcı sistemden kaldırılacak. Süper admin silmek için en az 1 başka süper admin olmalı.'),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
