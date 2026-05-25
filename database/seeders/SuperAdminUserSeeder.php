<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => env('SUPERADMIN_EMAIL', 'superadmin@gmail.com')],
            [
                'name'              => env('SUPERADMIN_NAME', 'Super Admin'),
                'phone_num'         => env('SUPERADMIN_PHONE', '+628123456789'),
                'role'              => 'superadmin',
                'status'            => 'active',
                'password'          => Hash::make(env('SUPERADMIN_PASSWORD', 'password')),
                'email_verified_at' => now(),
            ]
        );
    }
}
