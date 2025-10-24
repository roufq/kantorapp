<?php

namespace Tests\Unit;

use App\Models\Location;
use App\Models\LocationSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocationModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_location_has_users_relationship(): void
    {
        $loc = Location::create([
            'name' => 'Test Loc',
            'code' => 'TEST',
            'timezone' => 'Asia/Jakarta',
            'is_active' => true,
        ]);

        $u = User::factory()->create(['location_id' => $loc->id]);

        $this->assertEquals(1, $loc->users()->count());
        $this->assertTrue($loc->users->contains($u));
    }

    public function test_get_set_setting_helpers_work(): void
    {
        $loc = Location::create([
            'name' => 'Test Loc',
            'code' => 'TLOC',
            'timezone' => 'Asia/Jakarta',
            'is_active' => true,
        ]);

        $this->assertNull($loc->getSetting('work_hours'));

        $loc->setSetting('work_hours', 8, 'number');
        $this->assertNotNull(LocationSetting::where('location_id', $loc->id)->where('key', 'work_hours')->first());
        $this->assertEquals(8, $loc->getSetting('work_hours'));
    }
}

