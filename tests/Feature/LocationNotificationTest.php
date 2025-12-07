<?php

namespace Tests\Feature;

use App\Console\Commands\SendShiftReminders;
use App\Models\Location;
use App\Models\LocationShift;
use App\Models\Shift;
use App\Models\ShiftAssignment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LocationNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'Karyawan']);
    }

    public function test_shift_reminder_and_absence_alert_are_location_scoped(): void
    {
        Notification::fake();

        $loc = Location::create([
            'name' => 'LocNotif',
            'code' => 'LN',
            'timezone' => 'Asia/Jakarta',
            'is_active' => true,
        ]);

        $shift = Shift::create([
            'name' => 'Morning',
            'code' => 'MORN',
            'shift_type' => 'single',
            'time_slots' => ['start' => '10:00', 'end' => '12:00'],
            'is_active' => true,
        ]);
        $shift->locations()->attach($loc->id, [
            'time_slots' => ['start' => '10:00', 'end' => '12:00'],
            'is_default' => true,
        ]);
        /** @var LocationShift $pivot */
        $pivot = $shift->locations()->where('locations.id', $loc->id)->first()->pivot;

        $userReminder = User::factory()->create([
            'location_id' => $loc->id,
            'email' => 'reminder@example.com',
        ]);
        $userReminder->assignRole('Karyawan');

        $userAlert = User::factory()->create([
            'location_id' => $loc->id,
            'email' => 'alert@example.com',
        ]);
        $userAlert->assignRole('Karyawan');

        // Reminder assignment (starts in 30 minutes)
        ShiftAssignment::create([
            'user_id' => $userReminder->id,
            'location_id' => $loc->id,
            'shift_id' => $shift->id,
            'location_shift_id' => $pivot->id,
            'date' => Carbon::today('Asia/Jakarta')->toDateString(),
            'status' => 'scheduled',
        ]);

        // Absence alert assignment (ended >5 minutes ago) using separate early shift
        $shiftEarly = Shift::create([
            'name' => 'Early',
            'code' => 'EARLY',
            'shift_type' => 'single',
            'time_slots' => ['start' => '07:00', 'end' => '08:00'],
            'is_active' => true,
        ]);
        $shiftEarly->locations()->attach($loc->id, [
            'time_slots' => ['start' => '07:00', 'end' => '08:00'],
            'is_default' => false,
        ]);
        $pivotEarly = $shiftEarly->locations()->where('locations.id', $loc->id)->first()->pivot;

        ShiftAssignment::create([
            'user_id' => $userAlert->id,
            'location_id' => $loc->id,
            'shift_id' => $shiftEarly->id,
            'location_shift_id' => $pivotEarly->id,
            'date' => Carbon::today('Asia/Jakarta')->toDateString(),
            'status' => 'scheduled',
        ]);

        // Current time 09:30 Asia/Jakarta
        Carbon::setTestNow(Carbon::today('Asia/Jakarta')->setTime(9, 30));

        Artisan::call(SendShiftReminders::class, ['--window' => 60, '--grace' => 5]);

        Notification::assertSentTimes(\App\Notifications\ShiftReminderNotification::class, 1);
        Notification::assertSentTimes(\App\Notifications\ShiftAbsenceAlertNotification::class, 1);
    }
}
