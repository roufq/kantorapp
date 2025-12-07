<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocationConfigurationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_location_settings_and_branding_render_on_dashboard(): void
    {
        $loc = Location::create([
            'name' => 'ConfigLoc',
            'code' => 'CFG',
            'brand_name' => 'CfgBrand',
            'brand_logo_url' => '/images/logo.png',
            'primary_color' => '#111111',
            'secondary_color' => '#222222',
            'custom_css_url' => '/css/custom-cfg.css',
            'is_active' => true,
        ]);

        $loc->setSetting('theme', 'green');
        $this->assertSame('green', $loc->getSetting('theme'));

        $user = User::factory()->create(['location_id' => $loc->id, 'email' => 'cfg@example.com']);
        $user->assignRole('Admin Lokasi');

        $html = $this->actingAs($user)->get('/dashboard')->assertStatus(200)->getContent();

        $this->assertStringContainsString('--brand-primary: #111111', $html);
        $this->assertStringContainsString('--brand-secondary: #222222', $html);
        $this->assertStringContainsString('/css/custom-cfg.css', $html);
        $this->assertStringContainsString('/themes/green.css', $html);
        $this->assertStringContainsString('CfgBrand', $html);
    }
}
