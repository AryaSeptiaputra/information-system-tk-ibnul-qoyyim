<?php

namespace Database\Seeders;

use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\ParentGuardian;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $parents = ParentGuardian::all();

        if ($parents->count() < 3) {
            return;
        }

        // group => class_name lookup
        $classMap = [
            'A' => SchoolClass::query()->where('class_name', 'LIKE', '%Kelas A%')->where('school_year', '2025/2026')->first(),
            'B' => SchoolClass::query()->where('class_name', 'LIKE', '%Kelas B%')->where('school_year', '2025/2026')->first(),
            'C' => SchoolClass::query()->where('class_name', 'LIKE', '%Kelas C%')->where('school_year', '2025/2026')->first(),
        ];

        $students = [
            [
                'id_parents'  => $parents[0]->id_parents,
                'name'        => 'Aldi Wijaya',
                'birth_place' => 'Bandung',
                'birth_date'  => '2020-05-15',
                'gender'      => 'pria',
                'group'       => 'A',
                'status'      => 'aktif',
            ],
            [
                'id_parents'  => $parents[0]->id_parents,
                'name'        => 'Arisya Wijaya',
                'birth_place' => 'Bandung',
                'birth_date'  => '2021-08-22',
                'gender'      => 'perempuan',
                'group'       => 'B',
                'status'      => 'aktif',
            ],
            [
                'id_parents'  => $parents[1]->id_parents,
                'name'        => 'Bintang Suryanto',
                'birth_place' => 'Jakarta',
                'birth_date'  => '2020-11-10',
                'gender'      => 'pria',
                'group'       => 'A',
                'status'      => 'aktif',
            ],
            [
                'id_parents'  => $parents[2]->id_parents,
                'name'        => 'Cantika Hermawan',
                'birth_place' => 'Bandung',
                'birth_date'  => '2021-03-18',
                'gender'      => 'perempuan',
                'group'       => 'C',
                'status'      => 'aktif',
            ],
            [
                'id_parents'  => $parents[2]->id_parents,
                'name'        => 'Daffa Hermawan',
                'birth_place' => 'Bandung',
                'birth_date'  => '2019-12-01',
                'gender'      => 'pria',
                'group'       => 'C',
                'status'      => 'aktif',
            ],
        ];

        foreach ($students as $data) {
            $student = Student::query()->updateOrCreate(
                [
                    'id_parents' => $data['id_parents'],
                    'name'       => $data['name'],
                ],
                [
                    'birth_place'     => $data['birth_place'],
                    'birth_date'      => $data['birth_date'],
                    'gender'          => $data['gender'],
                    'group'           => $data['group'],
                    'status'          => $data['status'],
                    'id_registration' => null,
                ]
            );

            // Assign to matching class
            $class = $classMap[$data['group']] ?? null;
            if ($class) {
                $class->students()->syncWithoutDetaching([$student->id_student]);
            }
        }
    }
}
