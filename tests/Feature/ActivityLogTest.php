<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Sponsor;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

class ActivityLogTest extends TestCase
{
    public function test_creating_a_sponsor_writes_an_activity_log_entry(): void
    {
        $before = Activity::count();

        $sponsor = Sponsor::create([
            'name' => ['tr' => 'Yeni Sponsor', 'en' => 'New Sponsor'],
            'tier' => 'gold',
            'url' => 'https://example.com/yeni',
        ]);

        $log = Activity::query()
            ->where('subject_type', Sponsor::class)
            ->where('subject_id', $sponsor->id)
            ->where('event', 'created')
            ->first();

        $this->assertSame($before + 1, Activity::count());
        $this->assertNotNull($log, 'Expected a created activity row for the new sponsor');
        $this->assertSame('created', $log->event);
    }

    public function test_updating_a_project_logs_dirty_fields_only(): void
    {
        $project = Project::first();
        $this->assertNotNull($project, 'Seeder should create projects');

        $before = Activity::count();

        $project->update([
            'name' => ['tr' => $project->getTranslation('name', 'tr').' (güncel)', 'en' => $project->getTranslation('name', 'en')],
            'is_active' => $project->is_active, // unchanged
        ]);

        $log = Activity::query()
            ->where('subject_type', Project::class)
            ->where('subject_id', $project->id)
            ->where('event', 'updated')
            ->latest('id')
            ->first();

        $this->assertSame($before + 1, Activity::count());
        $this->assertNotNull($log, 'Expected an updated activity row');
        $props = $log->properties->toArray();
        $this->assertArrayHasKey('attributes', $props);
        $this->assertArrayHasKey('name', $props['attributes']);
        $this->assertArrayNotHasKey('is_active', $props['attributes']);
    }

    public function test_no_log_when_nothing_changes(): void
    {
        $project = Project::first();
        $before = Activity::count();

        $project->save();

        $this->assertSame($before, Activity::count(), 'A no-op save should not write an activity row');
    }

    public function test_deleting_a_sponsor_writes_deleted_event(): void
    {
        $sponsor = Sponsor::create([
            'name' => ['tr' => 'Geçici', 'en' => 'Temp'],
            'tier' => 'destek',
            'url' => 'https://example.com/temp',
        ]);

        $sponsor->delete();

        $log = Activity::query()
            ->where('subject_type', Sponsor::class)
            ->where('subject_id', $sponsor->id)
            ->where('event', 'deleted')
            ->first();

        $this->assertNotNull($log);
    }
}
