<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Shift;
use App\Models\Location;

class ShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first location or create a default one
        $location = Location::first();

        if (!$location) {
            // Create location using the LocationController or directly
            $location = Location::create([
                'name' => 'Main Office',
                'code' => 'MAIN',
                'address' => 'Jl. Sudirman No. 1, Jakarta',
                'timezone' => 'Asia/Jakarta',
                'is_active' => true,
            ]);
        }

        $shifts = [
            [
                'name' => 'Office Standard Shift',
                'code' => 'OFFICE',
                'day' => null,
                'shift_type' => 'single',
                'time_slots' => ['start' => '09:00', 'end' => '17:00'],
                'is_active' => true,
                'description' => 'Standard office hours from 9 AM to 5 PM',
            ],
            [
                'name' => 'Factory Multiple Shifts',
                'code' => 'FACTORY',
                'day' => null,
                'shift_type' => 'multiple',
                'time_slots' => [
                    ['start' => '06:00', 'end' => '14:00'],
                    ['start' => '14:00', 'end' => '22:00'],
                    ['start' => '22:00', 'end' => '06:00'],
                ],
                'is_active' => true,
                'description' => 'Factory shifts: morning, afternoon, and night',
            ],
        ];

        // Cleanup deprecated shifts if present
        Shift::where('code', 'WKND_OFF')->delete();

        foreach ($shifts as $shiftData) {
            // Idempotent insert by unique code
            Shift::firstOrCreate(
                ['code' => $shiftData['code']],
                $shiftData
            );
        }
    }
}
