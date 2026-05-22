<?php

namespace Database\Seeders;

use App\Models\Position;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    /**
     * Seed posisi/jabatan yang dipakai untuk perhitungan tunjangan honor.
     * Idempotent: firstOrCreate — tidak menimpa data yang sudah diubah admin.
     */
    public function run(): void
    {
        $positions = [
            [
                'name' => 'Bendahara',
                'description' => 'Mengelola dana sekolah dan pembayaran honor.',
            ],
            [
                'name' => 'Kepala Sekolah',
                'description' => 'Penanggung jawab utama operasional sekolah.',
            ],
            [
                'name' => 'Wali Kelas',
                'description' => 'Bertanggung jawab atas pembinaan kelas tertentu.',
            ],
        ];

        foreach ($positions as $entry) {
            Position::query()->firstOrCreate(
                ['name' => $entry['name']],
                [
                    'description' => $entry['description'],
                    'is_active' => true,
                ]
            );
        }
    }
}
