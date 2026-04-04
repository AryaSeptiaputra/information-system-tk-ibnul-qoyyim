<?php

namespace Database\Seeders;

use App\Models\ParentGuardian;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ParentGuardianSeeder extends Seeder
{
    public function run(): void
    {
        ParentGuardian::create([
            'father_name' => 'Ahmad Wijaya',
            'mother_name' => 'Siti Nurhaliza',
            'father_phone_num' => '+6281234567891',
            'mother_phone_num' => '+6281234567892',
            'father_occupation' => 'Insinyur',
            'mother_occupation' => 'Guru',
            'father_address' => 'Jl. Merdeka No. 123, Bandung',
            'mother_address' => 'Jl. Merdeka No. 123, Bandung',
        ]);

        ParentGuardian::create([
            'father_name' => 'Bambang Suryanto',
            'mother_name' => 'Dewi Lestari',
            'father_phone_num' => '+6281234567893',
            'mother_phone_num' => '+6281234567894',
            'father_occupation' => 'Pengusaha',
            'mother_occupation' => 'Dokter',
            'father_address' => 'Jl. Gatot Subroto No. 45, Bandung',
            'mother_address' => 'Jl. Gatot Subroto No. 45, Bandung',
        ]);

        ParentGuardian::create([
            'father_name' => 'Rudi Hermawan',
            'mother_name' => 'Rita Zahara',
            'father_phone_num' => '+6281234567895',
            'mother_phone_num' => '+6281234567896',
            'father_occupation' => 'Politisi',
            'mother_occupation' => 'Ibu Rumah Tangga',
            'father_address' => 'Jl. Dago No. 78, Bandung',
            'mother_address' => 'Jl. Dago No. 78, Bandung',
        ]);
    }
}
