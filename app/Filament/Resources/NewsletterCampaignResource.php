<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NewsletterCampaignResource\Pages;
use App\Mail\NewsletterCampaignMessage;
use App\Models\NewsletterCampaign;
use App\Models\NewsletterSubscriber;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;

class NewsletterCampaignResource extends Resource
{
    use Translatable;

    protected static ?string $model = NewsletterCampaign::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static ?string $navigationGroup = 'Bülten';

    protected static ?string $modelLabel = 'Kampanya';

    protected static ?string $pluralModelLabel = 'Kampanyalar';

    protected static ?int $navigationSort = 2;

    public static function getTranslatableLocales(): array
    {
        return ['tr', 'en'];
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('subject')
                ->label('Konu')
                ->required()
                ->maxLength(200)
                ->columnSpanFull(),
            Forms\Components\Textarea::make('body')
                ->label('Gövde (Markdown destekli)')
                ->required()
                ->rows(14)
                ->helperText('Markdown kullanabilirsin: # başlık, **kalın**, [link](url), - liste.')
                ->columnSpanFull(),
            Forms\Components\Grid::make(2)->schema([
                Forms\Components\Select::make('audience_locale')
                    ->label('Hedef kitle dili')
                    ->options(['tr' => 'Sadece Türkçe abonelere', 'en' => 'Sadece İngilizce abonelere'])
                    ->placeholder('Tüm aboneler')
                    ->helperText('Boş bırakırsan tüm onaylı abonelere gider.'),
                Forms\Components\Select::make('status')
                    ->label('Durum')
                    ->options(NewsletterCampaign::STATUSES)
                    ->default(NewsletterCampaign::STATUS_DRAFT)
                    ->disabled(fn (?NewsletterCampaign $record) => $record && $record->status !== NewsletterCampaign::STATUS_DRAFT)
                    ->dehydrated(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('subject')
                    ->label('Konu')
                    ->searchable()
                    ->limit(60)
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => NewsletterCampaign::STATUSES[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        NewsletterCampaign::STATUS_SENT => 'success',
                        NewsletterCampaign::STATUS_SENDING => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('audience_locale')
                    ->label('Kitle')
                    ->formatStateUsing(fn (?string $state) => $state ? strtoupper($state) : 'Tümü')
                    ->badge(),
                Tables\Columns\TextColumn::make('recipients_count')->label('Alıcı')->numeric()->sortable(),
                Tables\Columns\TextColumn::make('sent_at')->label('Gönderim')->dateTime('d M Y H:i')->placeholder('—')->sortable(),
                Tables\Columns\TextColumn::make('sentBy.name')->label('Gönderen')->placeholder('—'),
                Tables\Columns\TextColumn::make('created_at')->label('Oluşturma')->dateTime('d M Y')->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->label('Durum')->options(NewsletterCampaign::STATUSES),
            ])
            ->actions([
                Tables\Actions\Action::make('send')
                    ->label('Gönder')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->visible(fn (NewsletterCampaign $record) => $record->isSendable())
                    ->requiresConfirmation()
                    ->modalHeading('Kampanyayı tüm onaylı abonelere göndermek istiyor musun?')
                    ->modalDescription('Bu işlem geri alınamaz. Aboneler hedef kitle filtresine göre seçilecek.')
                    ->action(function (NewsletterCampaign $record) {
                        $count = self::dispatchCampaign($record);

                        Notification::make()
                            ->title('Kampanya gönderildi')
                            ->body($count.' aboneye e-posta kuyruğa alındı.')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn (NewsletterCampaign $record) => $record->status === NewsletterCampaign::STATUS_DRAFT),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function dispatchCampaign(NewsletterCampaign $campaign): int
    {
        $campaign->forceFill([
            'status' => NewsletterCampaign::STATUS_SENDING,
        ])->save();

        $query = NewsletterSubscriber::query()
            ->confirmed()
            ->forLocale($campaign->audience_locale);

        $count = 0;
        $query->chunkById(200, function ($subscribers) use ($campaign, &$count) {
            foreach ($subscribers as $subscriber) {
                Mail::to($subscriber->email)->queue(new NewsletterCampaignMessage($campaign, $subscriber));
                $count++;
            }
        });

        $campaign->forceFill([
            'status' => NewsletterCampaign::STATUS_SENT,
            'sent_at' => now(),
            'recipients_count' => $count,
            'sent_by' => auth()->id(),
        ])->save();

        return $count;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNewsletterCampaigns::route('/'),
            'create' => Pages\CreateNewsletterCampaign::route('/create'),
            'edit' => Pages\EditNewsletterCampaign::route('/{record}/edit'),
        ];
    }
}
