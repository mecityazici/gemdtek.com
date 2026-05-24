<?php

namespace Tests\Feature;

use Tests\TestCase;

class PublicSmokeTest extends TestCase
{
    public function test_home_returns_200_with_seeded_content(): void
    {
        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('GEMDTEK')
            ->assertSee('Aktif üye'); // from SiteMetric seed
    }

    public function test_about_page_returns_200(): void
    {
        $this->get('/hakkimizda')
            ->assertOk()
            ->assertSee('Mehmet Demir'); // team member from seed
    }

    public function test_projects_index_returns_200_and_lists_projects(): void
    {
        $this->get('/ar-ge')
            ->assertOk()
            ->assertSee('TEKNOFEST');
    }

    public function test_project_detail_returns_200(): void
    {
        $this->get('/ar-ge/teknofest-rov')
            ->assertOk()
            ->assertSee('ROV')
            ->assertSee('Mekanik'); // spec category
    }

    public function test_project_detail_404_for_inactive_or_unknown(): void
    {
        $this->get('/ar-ge/nonexistent-project')->assertNotFound();
    }

    public function test_events_index_returns_200(): void
    {
        $this->get('/etkinlikler')
            ->assertOk()
            ->assertSee('Zirve');
    }

    public function test_event_detail_returns_200(): void
    {
        $this->get('/etkinlikler/denizcilik-kariyer-zirvesi-2026')
            ->assertOk()
            ->assertSee('Zirve');
    }

    public function test_events_filtered_by_category(): void
    {
        $this->get('/etkinlikler?cat=atolye')
            ->assertOk()
            ->assertSee('CFD');
    }

    public function test_news_index_returns_200(): void
    {
        $this->get('/haberler')->assertOk();
    }

    public function test_news_detail_returns_200(): void
    {
        $this->get('/haberler/rov-takimi-teknofest-finalist')
            ->assertOk()
            ->assertSee('TEKNOFEST');
    }

    public function test_forms_index_returns_200(): void
    {
        $this->get('/basvuru')
            ->assertOk()
            ->assertSee('Üyelik');
    }

    public function test_form_detail_returns_200(): void
    {
        $this->get('/basvuru/uyelik')
            ->assertOk()
            ->assertSee('Ad Soyad');
    }

    public function test_alumni_index_returns_200(): void
    {
        $this->get('/mezunlar')
            ->assertOk()
            ->assertSee('Cem Aydoğdu');
    }

    public function test_alumni_sector_filter(): void
    {
        $this->get('/mezunlar?sector=klas')
            ->assertOk()
            ->assertSee('Klas');
    }

    public function test_sponsor_lead_page_returns_200(): void
    {
        $this->get('/sponsor-ol')
            ->assertOk()
            ->assertSee('Platin')
            ->assertSee('120.000 TL');
    }

    public function test_contact_page_returns_200(): void
    {
        $this->get('/iletisim')
            ->assertOk()
            ->assertSee('info@gemdtek.com');
    }

    public function test_privacy_page_returns_200(): void
    {
        $this->get('/kvkk')
            ->assertOk()
            ->assertSee('KVKK');
    }

    public function test_search_page_renders_without_query(): void
    {
        $this->get('/arama')->assertOk();
    }

    public function test_search_finds_existing_content(): void
    {
        $this->get('/arama?q=teknofest')
            ->assertOk()
            ->assertSee('TEKNOFEST');
    }

    public function test_search_too_short_shows_warning(): void
    {
        $this->get('/arama?q=t')
            ->assertOk()
            ->assertSee('karakter'); // "En az 2 karakter giriniz"
    }

    public function test_sitemap_returns_valid_xml(): void
    {
        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=utf-8')
            ->assertSee('<urlset', false)
            ->assertSee('teknofest-rov');
    }

    public function test_404_page_returns_404(): void
    {
        $this->get('/this-route-does-not-exist')->assertNotFound();
    }

    public function test_locale_switch_persists_in_session(): void
    {
        // Switch to EN
        $this->get('/lang/en');

        $this->get('/hakkimizda')
            ->assertOk()
            ->assertSee('About'); // EN nav label / page title
    }

    public function test_robots_txt_served_statically(): void
    {
        // Robots is a static file, can't go through Laravel — skipped from HTTP tests
        $this->assertFileExists(public_path('robots.txt'));
        $this->assertStringContainsString(
            'Sitemap: https://gemdtek.com/sitemap.xml',
            file_get_contents(public_path('robots.txt'))
        );
    }
}
