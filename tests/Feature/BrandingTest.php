<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BrandingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_dashboard_includes_branding_assets_and_theme()
    {
        $loc = Location::create([
            'name' => 'Branded',
            'code' => 'BR',
            'brand_name' => 'BrandX',
            'brand_logo_url' => '/images/logo.png',
            'primary_color' => '#112233',
            'secondary_color' => '#445566',
            'custom_css_url' => '/css/custom-brand.css',
        ]);
        // Set theme via settings
        $loc->setSetting('theme', 'blue');

        $user = User::factory()->create(['location_id' => $loc->id]);
        $user->assignRole('Employee');

        $html = $this->actingAs($user)->get('/dashboard')->assertStatus(200)->getContent();

        $this->assertStringContainsString('--brand-primary: #112233', $html);
        $this->assertStringContainsString('--brand-secondary: #445566', $html);
        $this->assertStringContainsString('/css/custom-brand.css', $html);
        $this->assertStringContainsString('/themes/blue.css', $html);
    }
}

