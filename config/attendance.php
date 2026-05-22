<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Teacher Attendance Policy
    |--------------------------------------------------------------------------
    | Kebijakan absensi guru TK Ibnul Qoyyim. Dipakai oleh:
    |  - TeacherAttendanceSelfController (validasi window check-in)
    |  - TeacherHonorManagementController (perhitungan potongan & holiday credit)
    */

    'timezone' => 'Asia/Makassar', // WITA

    // Jam window check-in mandiri (format HH:mm, di timezone di atas).
    'check_in_open' => '07:25',
    'late_after' => '08:00',
    'check_in_close' => '11:00',

    // Hari kerja: 1=Mon, 2=Tue, ... 5=Fri, 6=Sat, 7=Sun (ISO-8601).
    'workdays' => [1, 2, 3, 4, 5],

    // Potongan honor.
    'late_penalty' => 10000,           // Rp / hari telat
    'permission_penalty' => 10000,     // Rp / hari izin di luar grace period
    'permission_grace_days' => 2,      // Izin berturut ≤ N hari tidak kena potongan

    // Upload bukti izin.
    'attachment_disk' => 'public',
    'attachment_dir' => 'teacher-attendance',
    'attachment_max_kb' => 2048,                 // 2 MB
    'attachment_mimes' => 'jpg,jpeg,png,pdf',
];
