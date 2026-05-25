<?php

namespace Database\Seeders;

use App\Models\SchoolClass;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SchoolClassSeeder extends Seeder
{
    public function run(): void
    {
        $classes = [
            [
                'class_name'   => 'Kelas A (Kelompok Bermain)',
                'school_year'  => '2025/2026',
                'max_students' => 15,
            ],
            [
                'class_name'   => 'Kelas B (Taman Kanak-Kanak)',
                'school_year'  => '2025/2026',
                'max_students' => 15,
            ],
            [
                'class_name'   => 'Kelas C (TK Lanjut)',
                'school_year'  => '2025/2026',
                'max_students' => 15,
            ],
        ];

        foreach ($classes as $class) {
            SchoolClass::query()->updateOrCreate(
                [
                    'class_name'  => $class['class_name'],
                    'school_year' => $class['school_year'],
                ],
                ['max_students' => $class['max_students']]
            );
        }
    }
}
