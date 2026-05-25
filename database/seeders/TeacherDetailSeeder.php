<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\TeacherDetail;
use Illuminate\Database\Seeder;

class TeacherDetailSeeder extends Seeder
{
    public function run(): void
    {
        $teachers = [
            [
                'email'           => 'headmaster@example.com',
                'name'            => 'Bapak Ahmad Fauzi, S.Pd',
                'nip'             => '197805152005011001',
                'nuptk'           => '5847756658300003',
                'birth_place'     => 'Bandung',
                'birth_date'      => '1978-05-15',
                'start_work_date' => '2005-01-03',
                'education'       => 'S1 Pendidikan Agama Islam',
                'phone_num'       => '+62812345681',
                'position'        => 'Kepala Sekolah',
                'status'          => 'active',
            ],
            [
                'email'           => 'teacher@example.com',
                'name'            => 'Ibu Samiyah, S.Pd',
                'nip'             => null,
                'nuptk'           => '4551765666210072',
                'birth_place'     => 'Garut',
                'birth_date'      => '1985-11-23',
                'start_work_date' => '2010-07-12',
                'education'       => 'S1 Pendidikan Anak Usia Dini',
                'phone_num'       => '+62812345680',
                'position'        => 'Wali Kelas A',
                'status'          => 'active',
            ],
            [
                'email'           => 'teacher2@example.com',
                'name'            => 'Bapak Rahmat Hidayat, S.Pd',
                'nip'             => null,
                'nuptk'           => '6537864657300054',
                'birth_place'     => 'Sumedang',
                'birth_date'      => '1990-03-08',
                'start_work_date' => '2015-08-01',
                'education'       => 'S1 Pendidikan Guru SD',
                'phone_num'       => '+62812345684',
                'position'        => 'Wali Kelas B',
                'status'          => 'active',
            ],
            [
                'email'           => 'teacher3@example.com',
                'name'            => 'Ibu Yanti Rahayu',
                'nip'             => null,
                'nuptk'           => null,
                'birth_place'     => 'Cimahi',
                'birth_date'      => '1995-07-14',
                'start_work_date' => '2020-01-06',
                'education'       => 'D3 PAUD',
                'phone_num'       => '+62812345685',
                'position'        => 'Guru Pendamping',
                'status'          => 'active',
            ],
        ];

        foreach ($teachers as $data) {
            $user = User::query()->where('email', $data['email'])->first();
            if (! $user) {
                continue;
            }

            TeacherDetail::query()->updateOrCreate(
                ['id_user' => $user->id],
                [
                    'name'            => $data['name'],
                    'nip'             => $data['nip'],
                    'nuptk'           => $data['nuptk'],
                    'birth_place'     => $data['birth_place'],
                    'birth_date'      => $data['birth_date'],
                    'start_work_date' => $data['start_work_date'],
                    'education'       => $data['education'],
                    'phone_num'       => $data['phone_num'],
                    'email'           => $data['email'],
                    'position'        => $data['position'],
                    'status'          => $data['status'],
                ]
            );
        }
    }
}
