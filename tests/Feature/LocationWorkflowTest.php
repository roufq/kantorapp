<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocationWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_super_admin_can_create_location_and_view_it()
    {
        $master = User::factory()->create();
        $master->assignRole('Super Admin');

        $payload = [
            'name' => 'HQ',
            'code' => 'HQ',
            'address' => 'Jl. Test',
            'timezone' => 'Asia/Jakarta',
            'is_active' => true,
        ];

        $this->actingAs($master)
            ->post('/locations', $payload)
            ->assertStatus(302);

        $loc = Location::where('code', 'HQ')->first();
        $this->assertNotNull($loc);

        $this->actingAs($master)
            ->get("/locations/{$loc->id}")
            ->assertStatus(200)
            ->assertSee('HQ');
    }
}

