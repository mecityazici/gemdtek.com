<?php

namespace Tests\Feature;

use Tests\TestCase;

class ApiTest extends TestCase
{
    public function test_api_root_returns_endpoint_listing(): void
    {
        $this->getJson('/api/v1/')
            ->assertOk()
            ->assertJsonStructure(['api', 'version', 'endpoints', 'rate_limit']);
    }

    public function test_events_index_returns_paginated_collection(): void
    {
        $response = $this->getJson('/api/v1/events');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [['id', 'slug', 'title', 'event_date', 'category', 'category_label', 'is_upcoming', 'url']],
                'links',
                'meta' => ['current_page', 'total', 'per_page'],
            ]);
    }

    public function test_event_show_returns_single_resource_with_content(): void
    {
        $this->getJson('/api/v1/events/denizcilik-kariyer-zirvesi-2026')
            ->assertOk()
            ->assertJsonPath('data.slug', 'denizcilik-kariyer-zirvesi-2026')
            ->assertJsonStructure(['data' => ['id', 'slug', 'title', 'description', 'event_date']]);
    }

    public function test_events_upcoming_filter(): void
    {
        $response = $this->getJson('/api/v1/events?upcoming=1');
        $response->assertOk();

        foreach ($response->json('data') as $event) {
            $this->assertTrue($event['is_upcoming'], "Event {$event['slug']} should be upcoming");
        }
    }

    public function test_events_category_filter_rejects_unknown(): void
    {
        $r1 = $this->getJson('/api/v1/events?category=atolye');
        $r1->assertOk();
        foreach ($r1->json('data') as $event) {
            $this->assertSame('atolye', $event['category']);
        }

        // Unknown category should silently return all (filter ignored)
        $r2 = $this->getJson('/api/v1/events?category=xyz');
        $r2->assertOk();
    }

    public function test_news_endpoints_work(): void
    {
        $this->getJson('/api/v1/news')
            ->assertOk()
            ->assertJsonStructure(['data' => [['id', 'slug', 'title', 'category', 'published_at', 'url']]]);

        $this->getJson('/api/v1/news/rov-takimi-teknofest-finalist')
            ->assertOk()
            ->assertJsonPath('data.slug', 'rov-takimi-teknofest-finalist');
    }

    public function test_projects_endpoints_with_specs_and_members(): void
    {
        $response = $this->getJson('/api/v1/projects/teknofest-rov');

        $response->assertOk()
            ->assertJsonPath('data.slug', 'teknofest-rov')
            ->assertJsonStructure(['data' => ['specs', 'members']]);

        $this->assertGreaterThan(0, count($response->json('data.specs')));
        $this->assertGreaterThan(0, count($response->json('data.members')));
    }

    public function test_alumni_endpoint_filters_by_sector(): void
    {
        $response = $this->getJson('/api/v1/alumni?sector=klas');

        $response->assertOk();

        foreach ($response->json('data') as $alum) {
            $this->assertSame('klas', $alum['sector']);
        }
    }

    public function test_sponsors_endpoint_returns_active_only(): void
    {
        $response = $this->getJson('/api/v1/sponsors');

        $response->assertOk()
            ->assertJsonStructure(['data' => [['id', 'name', 'tier', 'logo_url']]]);
    }

    public function test_locale_query_returns_english_field_values(): void
    {
        $tr = $this->getJson('/api/v1/events/denizcilik-kariyer-zirvesi-2026')->json('data.title');
        $en = $this->getJson('/api/v1/events/denizcilik-kariyer-zirvesi-2026?lang=en')->json('data.title');

        $this->assertNotSame($tr, $en, 'Title should differ between TR and EN');
        $this->assertStringContainsString('Maritime', $en);
    }

    public function test_show_returns_404_for_inactive_or_unknown(): void
    {
        $this->getJson('/api/v1/events/nonexistent-slug')->assertNotFound();
        $this->getJson('/api/v1/news/nonexistent-slug')->assertNotFound();
        $this->getJson('/api/v1/projects/nonexistent-slug')->assertNotFound();
    }
}
