<?php

namespace Database\Seeders;

use App\Models\Facility;
use Illuminate\Database\Seeder;

class FacilitySeeder extends Seeder
{
    public function run(): void
    {
        $facilities = [
            [
                'name'             => 'Ruang Kelas',
                'description'      => 'Ruang belajar utama untuk kegiatan KB/TK.',
                'quantity'         => 3,
                'condition'        => 'Baik',
                'category'         => 'Ruangan',
                'fund_source'      => 'bosda',
                'acquisition_year' => 2018,
                'is_active'        => true,
            ],
            [
                'name'             => 'Perosotan',
                'description'      => 'Fasilitas bermain outdoor.',
                'quantity'         => 1,
                'condition'        => 'Baik',
                'category'         => 'Permainan',
                'fund_source'      => 'gedung',
                'acquisition_year' => 2020,
                'is_active'        => true,
            ],
            [
                'name'             => 'Ayunan',
                'description'      => 'Fasilitas bermain outdoor.',
                'quantity'         => 2,
                'condition'        => 'Baik',
                'category'         => 'Permainan',
                'fund_source'      => 'gedung',
                'acquisition_year' => 2020,
                'is_active'        => true,
            ],
            [
                'name'             => 'Alat Tulis',
                'description'      => 'Stok alat tulis kelas (pensil, krayon, dll).',
                'quantity'         => 50,
                'condition'        => 'Baik',
                'category'         => 'ATK',
                'fund_source'      => 'atk',
                'acquisition_year' => 2025,
                'is_active'        => true,
            ],
            [
                'name'             => 'Meja Belajar Anak',
                'description'      => 'Meja plastik ukuran anak untuk aktivitas belajar dan menggambar.',
                'quantity'         => 20,
                'condition'        => 'Baik',
                'category'         => 'Perabotan',
                'fund_source'      => 'sarana',
                'acquisition_year' => 2022,
                'is_active'        => true,
            ],
            [
                'name'             => 'Kursi Anak',
                'description'      => 'Kursi plastik kecil sesuai postur anak TK.',
                'quantity'         => 30,
                'condition'        => 'Baik',
                'category'         => 'Perabotan',
                'fund_source'      => 'sarana',
                'acquisition_year' => 2022,
                'is_active'        => true,
            ],
            [
                'name'             => 'Papan Tulis',
                'description'      => 'Whiteboard untuk kegiatan belajar mengajar.',
                'quantity'         => 3,
                'condition'        => 'Baik',
                'category'         => 'Media Belajar',
                'fund_source'      => 'bop',
                'acquisition_year' => 2019,
                'is_active'        => true,
            ],
            [
                'name'             => 'Komputer/Laptop',
                'description'      => 'Laptop untuk administrasi dan pelaporan sekolah.',
                'quantity'         => 1,
                'condition'        => 'Baik',
                'category'         => 'Elektronik',
                'fund_source'      => 'bosda',
                'acquisition_year' => 2023,
                'is_active'        => true,
            ],
            [
                'name'             => 'Mainan Edukatif',
                'description'      => 'Puzzle, balok, dan mainan edukasi lainnya.',
                'quantity'         => 15,
                'condition'        => 'Cukup',
                'category'         => 'Permainan',
                'fund_source'      => 'bop',
                'acquisition_year' => 2021,
                'is_active'        => true,
            ],
        ];

        foreach ($facilities as $facility) {
            Facility::query()->updateOrCreate(
                ['name' => $facility['name']],
                [
                    'description'      => $facility['description'],
                    'quantity'         => $facility['quantity'],
                    'condition'        => $facility['condition'],
                    'category'         => $facility['category'],
                    'fund_source'      => $facility['fund_source'],
                    'acquisition_year' => $facility['acquisition_year'],
                    'image_path'       => null,
                    'is_active'        => $facility['is_active'],
                ]
            );
        }
    }
}
