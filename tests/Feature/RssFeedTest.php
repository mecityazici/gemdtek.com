<?php

namespace Tests\Feature;

use Tests\TestCase;

class RssFeedTest extends TestCase
{
    public function test_news_rss_returns_valid_xml_with_published_posts(): void
    {
        $response = $this->get('/haberler/rss');

        $response->assertOk()
            ->assertHeader('Content-Type', 'application/rss+xml; charset=utf-8')
            ->assertSee('<rss version="2.0"', false)
            ->assertSee('<channel>', false)
            ->assertSee('GEMDTEK — Haberler')
            ->assertSee('TEKNOFEST'); // seeded news post
    }

    public function test_news_rss_advertises_self_atom_link(): void
    {
        $this->get('/haberler/rss')
            ->assertSee('rel="self"', false)
            ->assertSee('type="application/rss+xml"', false);
    }

    public function test_events_rss_returns_valid_xml_with_seeded_events(): void
    {
        $response = $this->get('/etkinlikler/rss');

        $response->assertOk()
            ->assertHeader('Content-Type', 'application/rss+xml; charset=utf-8')
            ->assertSee('<rss version="2.0"', false)
            ->assertSee('GEMDTEK — Etkinlikler')
            ->assertSee('Denizcilik Kariyer Zirvesi'); // seeded event
    }

    public function test_layout_advertises_feeds_via_link_alternate(): void
    {
        $this->get('/')
            ->assertSee('rel="alternate" type="application/rss+xml" title="GEMDTEK — Haberler"', false)
            ->assertSee('rel="alternate" type="application/rss+xml" title="GEMDTEK — Etkinlikler"', false);
    }

    public function test_news_show_still_works_after_rss_route_added(): void
    {
        // Ensures /haberler/rss is matched BEFORE /haberler/{post:slug}
        // so the slug binding doesn't intercept "rss"
        $this->get('/haberler/rov-takimi-teknofest-finalist')
            ->assertOk()
            ->assertSee('TEKNOFEST');
    }
}
