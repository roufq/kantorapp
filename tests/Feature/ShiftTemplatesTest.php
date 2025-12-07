<?php

namespace Tests\Feature;

use App\Console\Commands\GenerateShiftRotation;
use App\Console\Commands\SeedShiftTemplates;
use App\Models\Location;
use App\Models\LocationShift;
use App\Models\ShiftAssignment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ShiftTemplatesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'Karyawan']);
    }

    public function test_seed_shift_templates_creates_default_shifts_and_links_locations(): void
    {
        $locA = Location::create(['name' => 'A', 'code' => 'A1', 'is_active' => true]);
        $locB = Location::create(['name' => 'B', 'code' => 'B1', 'is_active' => true]);

        Artisan::call(SeedShiftTemplates::class, ['--location_id' => [$locA->id, $locB->id]]);

        $this->assertDatabaseHas('shifts', ['code' => 'PAGI']);
        $this->assertDatabaseHas('location_shifts', ['location_id' => $locA->id]);
        $this->assertDatabaseHas('location_shifts', ['location_id' => $locB->id]);
    }

    public function test_generate_rotation_uses_location_shifts_to_create_assignments(): void
    {
        $loc = Location::create(['name' => 'A', 'code' => 'A1', 'is_active' => true]);
        Artisan::call(SeedShiftTemplates::class, ['--location_id' => [$loc->id]]);

        $shiftPivot = LocationShift::where('location_id', $loc->id)->first();

        $u1 = User::factory()->create(['location_id' => $loc->id, 'email' => 'u1@example.com']);
        $u2 = User::factory()->create(['location_id' => $loc->id, 'email' => 'u2@example.com']);
        $u1->assignRole('Karyawan');
        $u2->assignRole('Karyawan');

        Artisan::call(GenerateShiftRotation::class, ['location_id' => $loc->id, '--days' => 2]);

        $this->assertDatabaseCount('shift_assignments', 4); // 2 users * 2 days
        $this->assertDatabaseHas('shift_assignments', ['user_id' => $u1->id]);
        $this->assertDatabaseHas('shift_assignments', ['user_id' => $u2->id]);
        $this->assertDatabaseHas('shift_assignments', ['location_shift_id' => $shiftPivot->id]);
    }
}
