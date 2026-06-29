<?php

namespace Tests\Feature;

use App\Filament\Pages\SiteSettings;
use App\Models\SiteSetting;
use App\Models\User;
use Filament\Facades\Filament;
use Livewire\Livewire;
use Tests\TestCase;

class SiteSettingsPageTest extends TestCase
{
    private function actAsAdmin(): void
    {
        $admin = User::where('email', 'admin@gemdtek.com')->first();
        $this->assertNotNull($admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        $this->actingAs($admin);
    }

    public function test_save_persists_settings_via_validated_form_state(): void
    {
        $this->actAsAdmin();

        Livewire::test(SiteSettings::class)
            ->fillForm([
                'site_name' => 'GEMDTEK Test',
                'contact_email' => 'kayit@gemdtek.com',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('GEMDTEK Test', SiteSetting::get('site.name'));
        $this->assertSame('kayit@gemdtek.com', SiteSetting::get('contact.email'));
    }

    public function test_save_enforces_server_side_validation(): void
    {
        // getState() çağrısı sayesinde geçersiz e-posta artık server-side reddedilir.
        $this->actAsAdmin();

        Livewire::test(SiteSettings::class)
            ->fillForm([
                'contact_email' => 'gecersiz-eposta',
            ])
            ->call('save')
            ->assertHasFormErrors(['contact_email']);
    }
}
