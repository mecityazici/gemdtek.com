<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use Filament\Actions\Action;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Cache;

class SiteSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'Site Ayarları';

    protected static ?string $navigationLabel = 'Genel Ayarlar';

    protected static ?string $title = 'Site Ayarları';

    protected static ?int $navigationSort = 0;

    protected static string $view = 'filament.pages.site-settings';

    public ?array $data = [];

    /**
     * Sadece super_admin görsün — editör/kaptan sol nav'da bile bu sayfayı görmez,
     * URL'i yazıp gelirse 403 alır.
     */
    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('super_admin') ?? false;
    }

    public function mount(): void
    {
        $this->form->fill([
            // Genel
            'site_name' => setting('site.name', 'GEMDTEK'),
            'site_tagline' => setting('site.tagline', 'Gemi İnşaatı ve Deniz Teknolojileri Kulübü'),
            'site_description' => setting('site.description', 'Üniversite ile denizcilik endüstrisi arasında mühendislik köprüsü.'),

            // İletişim
            'contact_email' => setting('contact.email', 'info@gemdtek.com'),
            'contact_phone' => setting('contact.phone'),
            'contact_address' => setting('contact.address'),
            'contact_campus' => setting('contact.campus', 'İTÜ Maslak Kampüsü, İnşaat Fakültesi'),
            'contact_response_note' => setting('contact.response_note', 'İletişim formundan gelen mesajlara 48 saat içinde dönüyoruz.'),

            // Sosyal Medya
            'social_linkedin' => setting('social.linkedin', 'https://linkedin.com/company/gemdtek'),
            'social_instagram' => setting('social.instagram', 'https://instagram.com/gemdtek'),
            'social_twitter' => setting('social.twitter', 'https://x.com/gemdtek'),
            'social_youtube' => setting('social.youtube'),
            'social_github' => setting('social.github'),

            // SEO
            'seo_keywords' => setting('seo.keywords', 'gemi inşaatı, deniz teknolojileri, kulüp, ITU, Ar-Ge'),
            'seo_author' => setting('seo.author', 'GEMDTEK'),

            // Bildirimler
            'notifications_email' => setting('notifications.email', 'info@gemdtek.com'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make()
                    ->columnSpanFull()
                    ->tabs([
                        Tabs\Tab::make('Genel')
                            ->icon('heroicon-o-globe-alt')
                            ->schema([
                                SpatieMediaLibraryFileUpload::make('logo')
                                    ->label('Logo')
                                    ->image()
                                    ->imageEditor()
                                    ->collection('image')
                                    ->model(fn () => SiteSetting::firstOrCreate(['key' => 'site.logo'], ['group' => 'general', 'type' => 'image']))
                                    ->helperText('Header ve footer için yatay logo. Önerilen: 400×120 PNG / SVG.'),
                                SpatieMediaLibraryFileUpload::make('favicon')
                                    ->label('Favicon')
                                    ->image()
                                    ->collection('image')
                                    ->model(fn () => SiteSetting::firstOrCreate(['key' => 'site.favicon'], ['group' => 'general', 'type' => 'image']))
                                    ->helperText('Tarayıcı sekmesi ikonu. 64×64 PNG.'),
                                SpatieMediaLibraryFileUpload::make('og_default')
                                    ->label('Sosyal medya kapağı (OG default)')
                                    ->image()
                                    ->imageEditor()
                                    ->collection('image')
                                    ->model(fn () => SiteSetting::firstOrCreate(['key' => 'site.og_default'], ['group' => 'general', 'type' => 'image']))
                                    ->helperText('Sayfaların kendi cover\'ı yoksa kullanılır. 1200×630 PNG/JPG.'),
                                TextInput::make('site_name')->label('Site adı')->required()->maxLength(80),
                                TextInput::make('site_tagline')->label('Tagline')->maxLength(160)->helperText('Footer\'da görünür.'),
                                Textarea::make('site_description')->label('Açıklama (SEO meta)')->rows(2)->maxLength(280),
                            ]),

                        Tabs\Tab::make('İletişim')
                            ->icon('heroicon-o-envelope')
                            ->schema([
                                TextInput::make('contact_email')->label('E-posta')->email()->required(),
                                TextInput::make('contact_phone')->label('Telefon')->tel(),
                                Textarea::make('contact_address')->label('Adres')->rows(2),
                                TextInput::make('contact_campus')->label('Kampüs')->maxLength(160),
                                Textarea::make('contact_response_note')->label('Yanıt notu')->rows(2)->helperText('İletişim sayfasının altında ufak metin.'),
                            ]),

                        Tabs\Tab::make('Sosyal Medya')
                            ->icon('heroicon-o-share')
                            ->schema([
                                TextInput::make('social_linkedin')->label('LinkedIn URL')->url(),
                                TextInput::make('social_instagram')->label('Instagram URL')->url(),
                                TextInput::make('social_twitter')->label('X / Twitter URL')->url(),
                                TextInput::make('social_youtube')->label('YouTube URL')->url(),
                                TextInput::make('social_github')->label('GitHub URL')->url(),
                            ]),

                        Tabs\Tab::make('SEO')
                            ->icon('heroicon-o-magnifying-glass')
                            ->schema([
                                Textarea::make('seo_keywords')->label('Meta keywords')->rows(2),
                                TextInput::make('seo_author')->label('Yazar / Owner')->maxLength(80),
                            ]),

                        Tabs\Tab::make('Bildirimler')
                            ->icon('heroicon-o-bell')
                            ->schema([
                                TextInput::make('notifications_email')->label('Form bildirimleri için e-posta')->email()->helperText('İletişim, sponsor lead, başvuru formu mailleri buraya gider.'),
                            ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $map = [
            'site_name' => ['key' => 'site.name', 'group' => 'general'],
            'site_tagline' => ['key' => 'site.tagline', 'group' => 'general'],
            'site_description' => ['key' => 'site.description', 'group' => 'general', 'type' => 'textarea'],
            'contact_email' => ['key' => 'contact.email', 'group' => 'contact', 'type' => 'email'],
            'contact_phone' => ['key' => 'contact.phone', 'group' => 'contact'],
            'contact_address' => ['key' => 'contact.address', 'group' => 'contact', 'type' => 'textarea'],
            'contact_campus' => ['key' => 'contact.campus', 'group' => 'contact'],
            'contact_response_note' => ['key' => 'contact.response_note', 'group' => 'contact', 'type' => 'textarea'],
            'social_linkedin' => ['key' => 'social.linkedin', 'group' => 'social', 'type' => 'url'],
            'social_instagram' => ['key' => 'social.instagram', 'group' => 'social', 'type' => 'url'],
            'social_twitter' => ['key' => 'social.twitter', 'group' => 'social', 'type' => 'url'],
            'social_youtube' => ['key' => 'social.youtube', 'group' => 'social', 'type' => 'url'],
            'social_github' => ['key' => 'social.github', 'group' => 'social', 'type' => 'url'],
            'seo_keywords' => ['key' => 'seo.keywords', 'group' => 'seo', 'type' => 'textarea'],
            'seo_author' => ['key' => 'seo.author', 'group' => 'seo'],
            'notifications_email' => ['key' => 'notifications.email', 'group' => 'notifications', 'type' => 'email'],
        ];

        foreach ($map as $field => $meta) {
            SiteSetting::set(
                $meta['key'],
                $this->data[$field] ?? null,
                $meta['group'],
                $meta['type'] ?? 'text',
            );
        }

        // Image cache keys'i ayrıca temizle (file upload Spatie kendi handle ediyor)
        foreach (['site.logo', 'site.favicon', 'site.og_default'] as $imgKey) {
            Cache::forget("setting:{$imgKey}");
        }

        Notification::make()
            ->title('Site ayarları güncellendi')
            ->body('Değişiklikler hemen aktif. Cache 1 saat sonra otomatik tazelenir.')
            ->success()
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Kaydet')
                ->icon('heroicon-o-check')
                ->color('primary')
                ->action('save'),
        ];
    }
}
