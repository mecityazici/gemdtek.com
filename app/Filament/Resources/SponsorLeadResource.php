<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SponsorLeadResource\Pages;
use App\Models\SponsorLead;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SponsorLeadResource extends Resource
{
    protected static ?string $model = SponsorLead::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationGroup = 'Başvurular';

    protected static ?string $modelLabel = 'Sponsor Lead';

    protected static ?string $pluralModelLabel = 'Sponsor Leadleri';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Grid::make(2)->schema([
                Forms\Components\TextInput::make('company_name')->label('Şirket adı')->required()->maxLength(160),
                Forms\Components\Select::make('interest_tier')
                    ->label('İlgilenilen seviye')
                    ->options(SponsorLead::TIERS),
            ]),
            Forms\Components\Grid::make(2)->schema([
                Forms\Components\TextInput::make('contact_name')->label('İletişim kişisi')->required()->maxLength(120),
                Forms\Components\TextInput::make('contact_role')->label('Görev / Pozisyon')->maxLength(120),
            ]),
            Forms\Components\TextInput::make('contact_email')->label('E-posta')->email()->required()->maxLength(160),
            Forms\Components\Textarea::make('message')->label('Mesaj')->rows(4)->maxLength(2000),
            Forms\Components\TextInput::make('source')->label('Kaynak')->disabled(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')->label('Tarih')->dateTime('d M Y H:i')->sortable(),
                Tables\Columns\TextColumn::make('company_name')->label('Şirket')->searchable()->weight('bold'),
                Tables\Columns\TextColumn::make('contact_name')->label('Kişi')->searchable(),
                Tables\Columns\TextColumn::make('contact_email')->label('E-posta')->copyable()->copyMessage('Kopyalandı'),
                Tables\Columns\TextColumn::make('interest_tier')
                    ->label('Seviye')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => $state ? (SponsorLead::TIERS[$state] ?? $state) : '—')
                    ->color(fn (?string $state): string => match ($state) {
                        'platinum' => 'primary',
                        'gold'     => 'warning',
                        'silver'   => 'gray',
                        'bronze'   => 'danger',
                        default    => 'info',
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('interest_tier')->label('Seviye')->options(SponsorLead::TIERS),
            ])
            ->headerActions([
                Tables\Actions\Action::make('exportCsv')
                    ->label('CSV indir')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function () {
                        $filename = 'sponsor-leadleri-' . now()->format('Ymd-Hi') . '.csv';
                        return response()->streamDownload(function () {
                            $out = fopen('php://output', 'w');
                            fputcsv($out, ['#', 'Tarih', 'Şirket', 'Kişi', 'Görev', 'E-posta', 'Seviye', 'Mesaj', 'IP', 'Kaynak']);
                            SponsorLead::orderByDesc('created_at')->chunk(200, function ($leads) use ($out) {
                                foreach ($leads as $l) {
                                    fputcsv($out, [
                                        $l->id,
                                        $l->created_at?->format('Y-m-d H:i'),
                                        $l->company_name,
                                        $l->contact_name,
                                        $l->contact_role,
                                        $l->contact_email,
                                        SponsorLead::TIERS[$l->interest_tier] ?? $l->interest_tier,
                                        $l->message,
                                        $l->ip_address,
                                        $l->source,
                                    ]);
                                }
                            });
                            fclose($out);
                        }, $filename, ['Content-Type' => 'text/csv; charset=utf-8']);
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index'  => Pages\ListSponsorLeads::route('/'),
            'create' => Pages\CreateSponsorLead::route('/create'),
            'edit'   => Pages\EditSponsorLead::route('/{record}/edit'),
        ];
    }
}
