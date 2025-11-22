<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocationSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_admin_lokasi_can_persist_settings()
    {
        $loc = Location::create(['name' => 'Loc1', 'code' => 'L1']);
        $admin = User::factory()->create(['location_id' => $loc->id]);
        $admin->assignRole('Admin Lokasi');

        $this->actingAs($admin)
            ->patchJson("/locations/{$loc->id}/settings", [
                'settings' => [
                    ['key' => 'timezone', 'value' => 'Asia/Jakarta', 'type' => 'string'],
                    ['key' => 'theme', 'value' => 'blue', 'type' => 'string'],
                ]
            ])->assertStatus(200);

        $this->assertEquals('Asia/Jakarta', $loc->fresh()->getSetting('timezone'));
        $this->assertEquals('blue', $loc->fresh()->getSetting('theme'));
    }
}

