<?php

namespace App\Console\Commands;

use App\Models\Location;
use App\Models\LocationShift;
use App\Models\Shift;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SeedShiftTemplates extends Command
{
    protected $signature = 'shifts:templates {--location_id=* : Limit to specific location IDs} {--force : Overwrite existing templates}';
    protected $description = 'Seed default shift templates (pagi, siang, malam, 24 jam) and attach to locations.';

    public function handle(): int
    {
        $templates = [
            ['name' => 'Pagi', 'code' => 'PAGI', 'start' => '06:00', 'end' => '14:00'],
            ['name' => 'Siang', 'code' => 'SIANG', 'start' => '14:00', 'end' => '22:00'],
            ['name' => 'Malam', 'code' => 'MALAM', 'start' => '22:00', 'end' => '06:00'],
            ['name' => '24 Jam', 'code' => '24H', 'start' => '00:00', 'end' => '23:59'],
        ];

        $locations = $this->option('location_id')
            ? Location::whereIn('id', $this->option('location_id'))->get()
            : Location::all();

        if ($locations->isEmpty()) {
            $this->warn('No locations found.');
        }

        foreach ($templates as $tpl) {
            $shift = Shift::firstOrCreate(
                ['code' => $tpl['code']],
                [
                    'name' => $tpl['name'],
                    'shift_type' => 'single',
                    'category' => 'non_office',
                    'time_slots' => ['start' => $tpl['start'], 'end' => $tpl['end']],
                    'is_active' => true,
                    'description' => 'Template ' . Str::lower($tpl['name']),
                ]
            );

            // Overwrite time slots when --force
            if ($this->option('force')) {
                $shift->update([
                    'time_slots' => ['start' => $tpl['start'], 'end' => $tpl['end']],
                    'is_active' => true,
                ]);
            }

            foreach ($locations as $loc) {
                $existing = LocationShift::where('location_id', $loc->id)
                    ->where('shift_id', $shift->id)
                    ->first();
                if (!$existing) {
                    $loc->shifts()->attach($shift->id, [
                        'time_slots' => ['start' => $tpl['start'], 'end' => $tpl['end']],
                        'is_default' => false,
                    ]);
                } elseif ($this->option('force')) {
                    $existing->update(['time_slots' => ['start' => $tpl['start'], 'end' => $tpl['end']]]);
                }
            }
        }

        $this->info('Shift templates seeded.');
        return self::SUCCESS;
    }
}
