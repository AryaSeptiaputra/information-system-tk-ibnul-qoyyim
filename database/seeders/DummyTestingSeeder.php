<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DummyTestingSeeder extends Seeder
{
    /**
     * Seed dummy/testing data (safe to re-run).
     */
    public function run(): void
    {
        // Always ensure core required data exists first
        $this->call(CoreRequiredSeeder::class);

        $this->call([
            // Users & identities
            DemoUserSeeder::class,

            // Parents & students
            ParentGuardianSeeder::class,
            SchoolClassSeeder::class,
            StudentSeeder::class,

            // Teachers (must follow DemoUserSeeder)
            TeacherDetailSeeder::class,

            // Positions & allowances (must follow TeacherDetailSeeder + PositionSeeder)
            TeacherPositionSeeder::class,
            PositionAllowanceSeeder::class,

            // Attendance rates (must follow TeacherDetailSeeder)
            TeacherAttendanceRateSeeder::class,

            // Attendance records (must follow TeacherDetailSeeder)
            TeacherAttendanceSeeder::class,

            // Facilities
            FacilitySeeder::class,

            // Registrations
            RegistrationSeeder::class,

            // Student payments (must follow StudentSeeder + PaymentMasterSeeder)
            StudentPaymentSeeder::class,
        ]);
    }
}
