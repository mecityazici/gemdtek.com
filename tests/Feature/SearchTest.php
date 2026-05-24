<?php

namespace Tests\Feature;

use App\Support\SearchHelper;
use Tests\TestCase;

class SearchTest extends TestCase
{
    public function test_search_page_renders_without_query(): void
    {
        $this->get('/arama')
            ->assertOk()
            ->assertSee('Sitede ara');
    }

    public function test_short_query_shows_too_short_warning(): void
    {
        $this->get('/arama?q=a')
            ->assertOk()
            ->assertSee('En az 2 karakter');
    }

    public function test_query_returns_grouped_results_with_highlight(): void
    {
        $response = $this->get('/arama?q=TEKNOFEST');

        $response->assertOk()
            ->assertSee('TEKNOFEST')
            ->assertSee('<mark', escape: false);
    }

    public function test_type_filter_restricts_visible_groups(): void
    {
        // Seeded TEKNOFEST appears in projects, not in news/alumni
        $response = $this->get('/arama?q=TEKNOFEST&type=projects');
        $response->assertOk()
            ->assertSee('Projeler')
            ->assertDontSee('class="font-display text-xl font-bold text-navy-800">Mezunlar', escape: false);
    }

    public function test_type_chips_carry_query_and_active_state(): void
    {
        $response = $this->get('/arama?q=TEKNOFEST');
        $response->assertOk()
            ->assertSee('type=projects', escape: false)
            ->assertSee('type=news', escape: false)
            ->assertSee('type=events', escape: false)
            ->assertSee('type=alumni', escape: false);
    }

    public function test_invalid_type_falls_back_to_all_groups(): void
    {
        $this->get('/arama?q=TEKNOFEST&type=garbage')
            ->assertOk()
            ->assertSee('TEKNOFEST');
    }

    public function test_no_results_shows_empty_state(): void
    {
        $this->get('/arama?q=zzznoesuchterm')
            ->assertOk()
            ->assertSee('sonuç bulunamadı');
    }

    public function test_search_helper_wraps_match_in_mark_tag(): void
    {
        $html = (string) SearchHelper::highlight('GEMDTEK Maritime', 'maritime');
        $this->assertStringContainsString('<mark', $html);
        $this->assertStringContainsString('Maritime</mark>', $html);
    }

    public function test_search_helper_escapes_html_in_input(): void
    {
        $html = (string) SearchHelper::highlight('<script>alert(1)</script>', 'alert');
        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringContainsString('&lt;script&gt;', $html);
    }

    public function test_search_helper_excerpt_centres_on_match(): void
    {
        $long = str_repeat('Lorem ipsum dolor sit amet. ', 20).'NEEDLE in the haystack. '.str_repeat('Sed do eiusmod. ', 20);
        $excerpt = SearchHelper::excerpt($long, 'NEEDLE', 30);

        $this->assertStringContainsString('NEEDLE', $excerpt);
        $this->assertStringStartsWith('…', $excerpt);
        $this->assertStringEndsWith('…', $excerpt);
    }

    public function test_search_helper_excerpt_falls_back_to_prefix_when_no_match(): void
    {
        $excerpt = SearchHelper::excerpt('Short text without match', 'banana', 10);
        $this->assertStringContainsString('Short', $excerpt);
    }
}
