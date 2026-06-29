<?php

namespace Tests\Feature;

use App\Filament\Resources\ActivityResource;
use App\Filament\Resources\FormResource;
use App\Filament\Resources\SiteMetricResource;
use App\Filament\Resources\SponsorLeadResource;
use App\Filament\Resources\SponsorResource;
use App\Filament\Resources\UserResource;
use App\Filament\Widgets\RecentActivity;
use App\Filament\Widgets\RecentSubmissions;
use App\Models\User;
use Filament\Facades\Filament;
use Tests\TestCase;

/**
 * Mevcut RBAC testleri yalnızca $user->can('izin') katmanını doğruluyordu; oysa
 * panelin gerçekten kullandığı kapı Resource::canViewAny()/canCreate() ve
 * Widget::canView(). Policy yokken bunlar fail-open ile her role TRUE dönüyordu
 * (privilege escalation). Bu test o gerçek kapıyı assert eder.
 */
class RbacGateTest extends TestCase
{
    private function actAs(string $email): User
    {
        $user = User::where('email', $email)->first();
        $this->assertNotNull($user, "Seeded user {$email} not found");
        // Resource::canViewAny() Filament::auth()->user()'ı okur → panel context şart.
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        $this->actingAs($user);

        return $user;
    }

    public function test_super_admin_reaches_privileged_resources_and_widgets(): void
    {
        $this->actAs('admin@gemdtek.com');

        $this->assertTrue(UserResource::canViewAny());
        $this->assertTrue(SponsorResource::canCreate());
        $this->assertTrue(ActivityResource::canViewAny());
        $this->assertTrue(RecentSubmissions::canView());
        $this->assertTrue(RecentActivity::canView());
    }

    public function test_team_captain_is_locked_out_of_other_resources_and_sensitive_widgets(): void
    {
        $this->actAs('kaptan@gemdtek.com');

        $this->assertFalse(UserResource::canViewAny(), 'kaptan kullanıcı yönetemez / kendini yükseltemez');
        $this->assertFalse(SponsorResource::canViewAny());
        $this->assertFalse(SponsorLeadResource::canViewAny(), 'kaptan lead PII görmemeli');
        $this->assertFalse(FormResource::canViewAny());
        $this->assertFalse(ActivityResource::canViewAny(), 'kaptan audit log görmemeli');
        $this->assertFalse(SiteMetricResource::canViewAny());
        $this->assertFalse(RecentSubmissions::canView());
        $this->assertFalse(RecentActivity::canView());
    }

    public function test_editor_cannot_manage_users_mutate_sponsors_or_see_audit(): void
    {
        $this->actAs('editor@gemdtek.com');

        // Privilege escalation kapalı:
        $this->assertFalse(UserResource::canViewAny(), 'editor kullanıcı yönetip kendini super_admin yapamaz');
        $this->assertFalse(ActivityResource::canViewAny());
        $this->assertFalse(RecentSubmissions::canView());

        // Editor içeriği görür ama mutasyon yapamaz:
        $this->assertTrue(SponsorResource::canViewAny());   // view-only
        $this->assertFalse(SponsorResource::canCreate());
    }
}
