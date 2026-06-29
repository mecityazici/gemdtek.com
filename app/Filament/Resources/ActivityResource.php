<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityResource\Pages;
use App\Models\User;
use Filament\Forms\Form;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;

class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Site Ayarları';

    protected static ?string $modelLabel = 'Aktivite Kaydı';

    protected static ?string $pluralModelLabel = 'Aktivite Logları';

    protected static ?int $navigationSort = 50;

    /** Read-only resource — system writes, admins only view. */
    public static function canCreate(): bool
    {
        return false;
    }

    /** Audit log hassastır — yalnızca super_admin görür (editor/team_captain değil). */
    public static function canViewAny(): bool
    {
        return auth()->user()?->hasRole('super_admin') ?? false;
    }

    public static function canView(Model $record): bool
    {
        return auth()->user()?->hasRole('super_admin') ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            TextEntry::make('created_at')->label('Zaman')->dateTime('d M Y H:i:s'),
            TextEntry::make('event')->label('Olay')->badge(),
            TextEntry::make('subject_label')
                ->label('Kayıt')
                ->getStateUsing(fn (Activity $r) => self::shortClassName($r->subject_type).' #'.$r->subject_id),
            TextEntry::make('causer_label')
                ->label('Kim')
                ->getStateUsing(fn (Activity $r) => self::causerLabel($r)),
            TextEntry::make('description')->label('Açıklama'),
            KeyValueEntry::make('properties.attributes')
                ->label('Yeni değerler')
                ->keyLabel('Alan')
                ->valueLabel('Değer'),
            KeyValueEntry::make('properties.old')
                ->label('Önceki değerler')
                ->keyLabel('Alan')
                ->valueLabel('Değer'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Zaman')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->since()
                    ->tooltip(fn (Activity $r) => $r->created_at?->format('d M Y H:i:s')),
                Tables\Columns\TextColumn::make('event')
                    ->label('Olay')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('subject_label')
                    ->label('Kayıt')
                    ->getStateUsing(fn (Activity $r) => self::shortClassName($r->subject_type).' #'.$r->subject_id)
                    ->fontFamily('mono')
                    ->size('xs'),
                Tables\Columns\TextColumn::make('causer_label')
                    ->label('Kim')
                    ->getStateUsing(fn (Activity $r) => self::causerLabel($r))
                    ->searchable(query: function ($q, string $search) {
                        return $q->whereHasMorph('causer', [User::class], function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                    }),
                Tables\Columns\TextColumn::make('description')
                    ->label('Açıklama')
                    ->limit(40)
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('event')
                    ->label('Olay')
                    ->options([
                        'created' => 'Eklendi',
                        'updated' => 'Güncellendi',
                        'deleted' => 'Silindi',
                    ]),
                Tables\Filters\SelectFilter::make('subject_type')
                    ->label('Kayıt tipi')
                    ->options(fn () => Activity::query()
                        ->select('subject_type')
                        ->distinct()
                        ->pluck('subject_type')
                        ->mapWithKeys(fn ($t) => [$t => self::shortClassName($t)])
                        ->all()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivities::route('/'),
            'view' => Pages\ViewActivity::route('/{record}'),
        ];
    }

    private static function shortClassName(?string $fqcn): string
    {
        if (! $fqcn) {
            return '—';
        }

        return class_basename($fqcn);
    }

    private static function causerLabel(Activity $a): string
    {
        if (! $a->causer) {
            return 'Sistem';
        }

        return $a->causer->name ?? class_basename($a->causer_type).' #'.$a->causer_id;
    }
}
