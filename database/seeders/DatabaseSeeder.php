<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create test users with different roles
        User::factory()->superadmin()->create([
            'name' => 'Superadmin User',
            'email' => 'superadmin@example.com',
            'phone_num' => '+62812345678',
        ]);

        User::factory()->administration()->create([
            'name' => 'Admin Staff',
            'email' => 'admin@example.com',
            'phone_num' => '+62812345679',
        ]);

        User::factory()->teacher()->create([
            'name' => 'Teacher Name',
            'email' => 'teacher@example.com',
            'phone_num' => '+62812345680',
        ]);

        User::factory()->headmaster()->create([
            'name' => 'Kepala Sekolah',
            'email' => 'headmaster@example.com',
            'phone_num' => '+62812345681',
        ]);

        User::factory()->guest()->create([
            'name' => 'Guest User',
            'email' => 'guest@example.com',
            'phone_num' => '+62812345682',
        ]);

        // Create additional users for testing
        User::factory(5)->teacher()->create();
        User::factory(3)->administration()->create();

        // Call seeders for new entities
        $this->call(ParentGuardianSeeder::class);
        $this->call(StudentSeeder::class);
        $this->call(SchoolClassSeeder::class);
        $this->call(TeacherDetailSeeder::class);
    }
}
