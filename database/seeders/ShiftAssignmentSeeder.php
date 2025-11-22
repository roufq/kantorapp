<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Location;
use App\Models\User;
use App\Models\Shift;
use App\Models\ShiftAssignment;
use Carbon\Carbon;

class ShiftAssignmentSeeder extends Seeder
{
    public function run(): void
    {
        $today = Carbon::today('Asia/Jakarta');

        $locations = Location::all();
        foreach ($locations as $loc) {
            // Ambil maksimal 2 karyawan per lokasi
            $users = User::role('Karyawan')->where('location_id', $loc->id)->take(2)->get();
            if ($users->isEmpty()) {
                continue;
            }

            // Ambil shift yang terhubung ke lokasi; fallback ke semua shift aktif jika kosong
            $shifts = $loc->shifts()->active()->get();
            if ($shifts->isEmpty()) {
                $shifts = Shift::active()->get();
            }
            if ($shifts->isEmpty()) {
                continue;
            }

            // Buat assignment untuk 7 hari kedepan
            foreach ($users as $u) {
                for ($i = 0; $i < 7; $i++) {
                    $date = $today->copy()->addDays($i);
                    // Rotasi shift berdasarkan index
                    $shift = $shifts[$i % $shifts->count()];

                    // Hindari duplikat (unique user_id+date)
                    if (!ShiftAssignment::where('user_id', $u->id)->whereDate('date', $date->toDateString())->exists()) {
                        ShiftAssignment::create([
                            'user_id' => $u->id,
                            'shift_id' => $shift->id,
                            'date' => $date->toDateString(),
                            'status' => 'scheduled',
                            'notes' => 'Seeded assignment',
                        ]);
                    }
                }
            }
        }
    }
}

