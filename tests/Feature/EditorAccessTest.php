<?php

namespace Tests\Feature;

use App\Models\NewsPost;
use App\Models\Sponsor;
use App\Models\User;
use Tests\TestCase;

class EditorAccessTest extends TestCase
{
    private function editor(): User
    {
        $u = User::where('email', 'editor@gemdtek.com')->first();
        $this->assertNotNull($u, 'Seeder should create the editor test user');
        $this->assertTrue($u->hasRole('editor'));

        return $u;
    }

    public function test_editor_can_access_admin_panel(): void
    {
        $editor = $this->editor();
        $this->assertTrue($editor->canAccessPanel(filament()->getDefaultPanel()));
    }

    public function test_editor_has_full_crud_on_news_post(): void
    {
        $editor = $this->editor();
        $this->actingAs($editor);

        $this->assertTrue($editor->can('view_any_news::post'));
        $this->assertTrue($editor->can('create_news::post'));
        $this->assertTrue($editor->can('update_news::post', NewsPost::first()));
        $this->assertTrue($editor->can('delete_news::post', NewsPost::first()));
    }

    public function test_editor_has_full_crud_on_event_and_timeline_and_alumni(): void
    {
        $editor = $this->editor();

        $this->assertTrue($editor->can('create_event'));
        $this->assertTrue($editor->can('delete_event'));
        $this->assertTrue($editor->can('create_timeline::event'));
        $this->assertTrue($editor->can('create_alumni'));
        $this->assertTrue($editor->can('update_alumni'));
    }

    public function test_editor_is_view_only_on_sponsor(): void
    {
        $editor = $this->editor();
        $sponsor = Sponsor::first();

        $this->assertTrue($editor->can('view_any_sponsor'));
        $this->assertTrue($editor->can('view_sponsor', $sponsor));

        $this->assertFalse($editor->can('create_sponsor'));
        $this->assertFalse($editor->can('update_sponsor', $sponsor));
        $this->assertFalse($editor->can('delete_sponsor', $sponsor));
    }

    public function test_editor_is_view_only_on_project_and_form(): void
    {
        $editor = $this->editor();

        $this->assertTrue($editor->can('view_any_project'));
        $this->assertFalse($editor->can('create_project'));

        $this->assertTrue($editor->can('view_any_form'));
        $this->assertFalse($editor->can('create_form'));
        $this->assertFalse($editor->can('delete_form'));

        // FormSubmission has no standalone resource — submissions live as a
        // relation manager under FormResource, so view_any_form is what gates
        // visibility there.
        $this->assertTrue($editor->can('view_any_sponsor::lead'));
        $this->assertFalse($editor->can('delete_sponsor::lead'));
    }

    public function test_editor_cannot_access_site_metrics_or_role_or_activity(): void
    {
        $editor = $this->editor();

        $this->assertFalse($editor->can('view_any_site::metric'));
        $this->assertFalse($editor->can('create_site::metric'));
        $this->assertFalse($editor->can('view_any_role'));
    }

    public function test_super_admin_still_bypasses_everything(): void
    {
        $admin = User::where('email', 'admin@gemdtek.com')->first();
        $this->assertNotNull($admin);

        // Shield's super_admin Gate::before grants all
        $this->assertTrue($admin->can('view_any_site::metric'));
        $this->assertTrue($admin->can('view_any_role'));
        $this->assertTrue($admin->can('create_sponsor'));
    }
}
