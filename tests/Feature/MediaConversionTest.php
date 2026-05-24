<?php

namespace Tests\Feature;

use App\Models\Alumni;
use App\Models\Event;
use App\Models\NewsPost;
use App\Models\Project;
use App\Models\Sponsor;
use App\Models\TeamMember;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaConversionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_sponsor_logo_generates_thumb_and_web_conversions(): void
    {
        $sponsor = Sponsor::first();
        $this->assertNotNull($sponsor);

        $sponsor->addMedia(base_path('tests/fixtures/sample.png'))
            ->preservingOriginal()
            ->usingFileName('logo.png')
            ->toMediaCollection('logo');

        $sponsor->refresh();

        $this->assertNotEmpty($sponsor->getFirstMediaUrl('logo'));
        $this->assertNotSame($sponsor->getFirstMediaUrl('logo'), $sponsor->getFirstMediaUrl('logo', 'thumb'));
        $this->assertNotSame($sponsor->getFirstMediaUrl('logo'), $sponsor->getFirstMediaUrl('logo', 'web'));
        $this->assertStringEndsWith('.webp', $sponsor->getFirstMediaUrl('logo', 'thumb'));
    }

    public function test_event_cover_generates_og_conversion_as_jpg(): void
    {
        $event = Event::first();
        $event->addMedia(base_path('tests/fixtures/sample.png'))
            ->preservingOriginal()
            ->usingFileName('cover.png')
            ->toMediaCollection('cover');

        $event->refresh();

        $ogUrl = $event->getFirstMediaUrl('cover', 'og');
        $this->assertNotEmpty($ogUrl);
        $this->assertStringEndsWith('.jpg', $ogUrl);
        $this->assertStringEndsWith('.webp', $event->getFirstMediaUrl('cover', 'web'));
    }

    public function test_news_post_og_accessor_falls_back_to_original_when_no_conversion(): void
    {
        $post = NewsPost::first();
        $this->assertNull($post->og_image_url);

        $post->addMedia(base_path('tests/fixtures/sample.png'))
            ->preservingOriginal()
            ->usingFileName('cover.png')
            ->toMediaCollection('cover');

        $post->refresh();
        $this->assertNotNull($post->og_image_url);
        $this->assertStringEndsWith('.jpg', $post->og_image_url);
    }

    public function test_project_thumb_and_web_url_accessors_resolve(): void
    {
        $project = Project::first();
        $project->addMedia(base_path('tests/fixtures/sample.png'))
            ->preservingOriginal()
            ->usingFileName('hero.png')
            ->toMediaCollection('hero');

        $project->refresh();
        $this->assertStringEndsWith('.webp', $project->hero_thumb_url);
        $this->assertStringEndsWith('.webp', $project->hero_web_url);
        $this->assertStringEndsWith('.jpg', $project->og_image_url);
    }

    public function test_alumni_photo_thumb_url_accessor_returns_webp(): void
    {
        $alumni = Alumni::first();
        $alumni->addMedia(base_path('tests/fixtures/sample.png'))
            ->preservingOriginal()
            ->usingFileName('photo.png')
            ->toMediaCollection('photo');

        $alumni->refresh();
        $this->assertStringEndsWith('.webp', $alumni->photo_thumb_url);
        $this->assertStringEndsWith('.webp', $alumni->photo_web_url);
    }

    public function test_team_member_photo_thumb_falls_back_when_no_media(): void
    {
        $member = TeamMember::first();
        $this->assertNull($member->photo_url);
        $this->assertNull($member->photo_thumb_url);
        $this->assertNull($member->photo_web_url);
    }
}
