<?php

namespace App\Filament\Resources\FormResource\RelationManagers;

use App\Exports\FormSubmissionsExport;
use App\Models\Form as FormModel;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Maatwebsite\Excel\Facades\Excel;

class SubmissionsRelationManager extends RelationManager
{
    protected static string $relationship = 'submissions';

    protected static ?string $title = 'Başvurular';

    public function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public function table(Table $table): Table
    {
        /** @var FormModel $ownerForm */
        $ownerForm = $this->getOwnerRecord();
        $fields = $ownerForm->fields;

        $columns = [
            Tables\Columns\TextColumn::make('id')->label('#')->sortable(),
            Tables\Columns\TextColumn::make('created_at')->label('Gönderim')->dateTime('d M Y H:i')->sortable(),
        ];

        foreach ($fields as $field) {
            $name = $field->name;
            $columns[] = Tables\Columns\TextColumn::make("data.{$name}")
                ->label($field->label)
                ->formatStateUsing(function ($state) {
                    if (is_array($state)) {
                        return implode(', ', $state);
                    }

                    return (string) ($state ?? '—');
                })
                ->limit(40)
                ->toggleable();
        }

        $columns[] = Tables\Columns\TextColumn::make('ip_address')->label('IP')->fontFamily('mono')->toggleable(isToggledHiddenByDefault: true);

        return $table
            ->recordTitleAttribute('id')
            ->columns($columns)
            ->defaultSort('created_at', 'desc')
            ->headerActions([
                Tables\Actions\Action::make('exportExcel')
                    ->label('Excel olarak indir')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function () use ($ownerForm) {
                        $filename = $ownerForm->slug.'-basvurular-'.now()->format('Ymd-Hi').'.xlsx';

                        return Excel::download(new FormSubmissionsExport($ownerForm), $filename);
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->modalHeading(fn ($record) => "Başvuru #{$record->id}")
                    ->infolist(function ($record) use ($fields) {
                        $entries = [];
                        foreach ($fields as $field) {
                            $value = $record->data[$field->name] ?? null;
                            if (is_array($value)) {
                                $value = implode(', ', $value);
                            }
                            $entries[] = TextEntry::make("data.{$field->name}")
                                ->label($field->label)
                                ->state($value ?? '—');
                        }
                        $entries[] = TextEntry::make('created_at')
                            ->label('Gönderim zamanı')
                            ->dateTime('d M Y H:i:s');
                        $entries[] = TextEntry::make('ip_address')->label('IP');

                        return $entries;
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public function isReadOnly(): bool
    {
        return false;
    }

    protected function canCreate(): bool
    {
        return false;
    }
}
