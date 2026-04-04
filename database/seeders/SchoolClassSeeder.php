<?php

namespace Database\Seeders;

use App\Models\SchoolClass;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SchoolClassSeeder extends Seeder
{
    public function run(): void
    {
        SchoolClass::create([
            'class_name' => 'Kelas A (Kelompok Bermain)',
            'school_year' => '2025/2026',
        ]);

        SchoolClass::create([
            'class_name' => 'Kelas B (Taman Kanak-Kanak)',
            'school_year' => '2025/2026',
        ]);

        SchoolClass::create([
            'class_name' => 'Kelas C (TK Lanjut)',
            'school_year' => '2025/2026',
        ]);
    }
}
