<?php

namespace Database\Seeders;

use App\Models\AllowanceType;
use Illuminate\Database\Seeder;

class AllowanceTypeSeeder extends Seeder
{
    /**
     * Seed jenis tunjangan dasar. Nominal per posisi diisi admin via
     * position_allowances (lewat halaman Tunjangan Posisi).
     */
    public function run(): void
    {
        $types = [
            [
                'name' => 'Tunjangan Jabatan',
                'description' => 'Tunjangan tetap berdasarkan posisi/jabatan yang dipegang guru.',
            ],
        ];

        foreach ($types as $entry) {
            AllowanceType::query()->firstOrCreate(
                ['name' => $entry['name']],
                [
                    'description' => $entry['description'],
                    'is_active' => true,
                ]
            );
        }
    }
}
