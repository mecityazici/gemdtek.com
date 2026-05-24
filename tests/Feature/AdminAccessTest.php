<?php

namespace Tests\Feature;

use App\Filament\Resources\ProjectResource;
use App\Models\Project;
use App\Models\User;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    public function test_guest_admin_route_redirects_to_login(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
    }

    public function test_admin_login_page_renders(): void
    {
        $this->get('/admin/login')->assertOk()->assertSee('GEMDTEK');
    }

    public function test_super_admin_can_access_panel(): void
    {
        $admin = User::where('email', 'admin@gemdtek.com')->first();
        $this->assertNotNull($admin, 'Seeded admin not found');
        $this->assertTrue($admin->hasRole('super_admin'));
        $this->assertTrue($admin->canAccessPanel(filament()->getDefaultPanel()));
    }

    public function test_team_captain_can_access_panel(): void
    {
        $captain = User::where('email', 'kaptan@gemdtek.com')->first();
        $this->assertNotNull($captain, 'Seeded team captain not found');
        $this->assertTrue($captain->hasRole('team_captain'));
        $this->assertTrue($captain->canAccessPanel(filament()->getDefaultPanel()));
    }

    public function test_unauthenticated_user_cannot_access_panel(): void
    {
        $randomUser = User::factory()->create();
        $this->assertFalse($randomUser->canAccessPanel(filament()->getDefaultPanel()));
    }

    public function test_team_captain_only_sees_their_assigned_project(): void
    {
        $captain = User::where('email', 'kaptan@gemdtek.com')->first();
        $this->actingAs($captain);

        $visible = ProjectResource::getEloquentQuery()->get();

        $this->assertCount(1, $visible, 'Team captain should see exactly 1 project');
        $this->assertSame($captain->id, $visible->first()->captain_user_id);
    }

    public function test_super_admin_sees_all_projects(): void
    {
        $admin = User::where('email', 'admin@gemdtek.com')->first();
        $this->actingAs($admin);

        $visible = ProjectResource::getEloquentQuery()->get();
        $total   = Project::count();

        $this->assertSame($total, $visible->count(), 'Super admin should see every project');
        $this->assertGreaterThan(1, $total, 'Seeder should produce multiple projects');
    }

    public function test_team_captain_can_update_own_project_via_policy(): void
    {
        $captain = User::where('email', 'kaptan@gemdtek.com')->first();
        $ownProject = Project::where('captain_user_id', $captain->id)->first();
        $this->assertNotNull($ownProject);

        $this->assertTrue($captain->can('update', $ownProject));
    }

    public function test_team_captain_cannot_update_other_projects(): void
    {
        $captain = User::where('email', 'kaptan@gemdtek.com')->first();
        $otherProject = Project::where(function ($q) use ($captain) {
            $q->whereNull('captain_user_id')->orWhere('captain_user_id', '!=', $captain->id);
        })->first();
        $this->assertNotNull($otherProject);

        $this->assertFalse($captain->can('update', $otherProject));
    }

    public function test_team_captain_cannot_create_or_delete_projects(): void
    {
        $captain = User::where('email', 'kaptan@gemdtek.com')->first();
        $ownProject = Project::where('captain_user_id', $captain->id)->first();

        $this->assertFalse($captain->can('create', Project::class));
        $this->assertFalse($captain->can('delete', $ownProject));
    }
}
