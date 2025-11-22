<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Location;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locations = [
            [
                'name' => 'Nava Group',
                'code' => 'JJ_MAIN',
                'address' => 'Jl. Sugeng Jeroni No.54, Patangpuluhan, Wirobrajan, Kota Yogyakarta, Daerah Istimewa Yogyakarta 55251',
                'timezone' => 'Asia/Jakarta',
                'latitude' => -7.812183414227418,
                'longitude' => 110.35052119376307,
                'radius' => 50,
                'is_active' => true,
                'shift_enabled' => true,
                'settings' => [
                    'work_hours_per_day' => 8,
                    'break_duration' => 60,
                    'overtime_rate' => 1.5,
                ],
            ],
            [
                'name' => 'Factory Bandung',
                'code' => 'FACT_BDG',
                'address' => 'Jl. Industri No. 15, Bandung, Jawa Barat 40135',
                'timezone' => 'Asia/Jakarta',
                'latitude' => -6.9175,
                'longitude' => 107.6191,
                'radius' => 150,
                'is_active' => true,
                'shift_enabled' => true,
                'settings' => [
                    'work_hours_per_day' => 12,
                    'break_duration' => 30,
                    'overtime_rate' => 2.0,
                ],
            ],
            [
                'name' => 'Branch Office Surabaya',
                'code' => 'BRANCH_SBY',
                'address' => 'Jl. Tunjungan No. 25, Surabaya, Jawa Timur 60275',
                'timezone' => 'Asia/Jakarta',
                'latitude' => -7.2575,
                'longitude' => 112.7521,
                'radius' => 80,
                'is_active' => true,
                'shift_enabled' => true,
                'settings' => [
                    'work_hours_per_day' => 8,
                    'break_duration' => 60,
                    'overtime_rate' => 1.5,
                ],
            ],
            [
                'name' => 'Warehouse Semarang',
                'code' => 'WH_SMG',
                'address' => 'Jl. Logistik No. 8, Semarang, Jawa Tengah 50241',
                'timezone' => 'Asia/Jakarta',
                'latitude' => -6.9667,
                'longitude' => 110.4167,
                'radius' => 120,
                'is_active' => true,
                'shift_enabled' => true,
                'settings' => [
                    'work_hours_per_day' => 12,
                    'break_duration' => 45,
                    'overtime_rate' => 1.75,
                ],
            ],
        ];

        foreach ($locations as $locationData) {
            // Use code as unique key to avoid duplicate seeding errors
            $code = $locationData['code'];
            Location::firstOrCreate(
                ['code' => $code],
                $locationData
            );
        }
    }
}
