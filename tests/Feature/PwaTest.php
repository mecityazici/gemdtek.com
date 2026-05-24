<?php

namespace Tests\Feature;

use Tests\TestCase;

class PwaTest extends TestCase
{
    public function test_manifest_is_served_as_json_with_required_pwa_fields(): void
    {
        $path = public_path('manifest.json');
        $this->assertFileExists($path);

        $manifest = json_decode((string) file_get_contents($path), true);
        $this->assertIsArray($manifest);

        foreach (['name', 'short_name', 'start_url', 'display', 'background_color', 'theme_color', 'icons'] as $field) {
            $this->assertArrayHasKey($field, $manifest, "manifest.json missing '{$field}'");
        }

        $sizes = collect($manifest['icons'])->pluck('sizes')->all();
        $this->assertContains('192x192', $sizes);
        $this->assertContains('512x512', $sizes);

        $this->assertNotEmpty(collect($manifest['icons'])->where('purpose', 'maskable')->first());
    }

    public function test_service_worker_file_exists_with_expected_handlers(): void
    {
        $path = public_path('sw.js');
        $this->assertFileExists($path);

        $contents = (string) file_get_contents($path);
        $this->assertStringContainsString("addEventListener('install'", $contents);
        $this->assertStringContainsString("addEventListener('activate'", $contents);
        $this->assertStringContainsString("addEventListener('fetch'", $contents);
        $this->assertStringContainsString('OFFLINE_URL', $contents);
        $this->assertStringContainsString("startsWith('/admin')", $contents);
    }

    public function test_pwa_icons_are_generated(): void
    {
        foreach (['icon-192.png', 'icon-512.png', 'icon-maskable-512.png', 'apple-touch-icon.png'] as $file) {
            $this->assertFileExists(public_path('icons/'.$file), "Missing icon: {$file}");
        }
    }

    public function test_offline_page_renders(): void
    {
        $this->get('/offline')
            ->assertOk()
            ->assertSee('Bağlantı yok');
    }

    public function test_layout_advertises_manifest_and_theme_color(): void
    {
        $response = $this->get('/');
        $response->assertOk()
            ->assertSee('rel="manifest"', escape: false)
            ->assertSee('name="theme-color"', escape: false)
            ->assertSee('apple-touch-icon', escape: false);
    }

    public function test_layout_does_not_register_service_worker_outside_production(): void
    {
        // Test env != production, so SW register block should not render
        $this->get('/')
            ->assertOk()
            ->assertDontSee("navigator.serviceWorker.register('/sw.js')", escape: false);
    }
}
