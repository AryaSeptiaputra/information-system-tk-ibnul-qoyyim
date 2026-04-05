<?php

use App\Models\Registration;
use App\Models\Student;
use App\Models\User;

test('guest multi-step registration saves to registrations table', function () {
    $user = User::factory()->create([
        'role' => 'guest',
    ]);

    $this->actingAs($user);

    // Step 1
    $this->post(route('registration.store', absolute: false), [
        'current_step' => 1,
        'next_step' => 2,
        'candidate_data' => [
            'name' => 'Budi Santoso',
            'birth_place' => 'Makassar',
            'birth_date' => '2021-01-01',
            'gender' => 'pria',
        ],
        'group' => 'A',
    ])->assertRedirect(route('dashboard', absolute: false) . '#registration-form');

    // Step 2
    $this->post(route('registration.store', absolute: false), [
        'current_step' => 2,
        'next_step' => 3,
        'parents_data' => [
            'father_name' => 'Ayah Budi',
            'father_phone' => '081234567890',
            'mother_name' => 'Ibu Budi',
            'mother_phone' => '081234567891',
            'father_job' => 'Karyawan',
            'mother_job' => 'Ibu Rumah Tangga',
        ],
    ])->assertRedirect(route('dashboard', absolute: false) . '#registration-form');

    // Step 3 submit: next_step can be empty in real form submit
    $this->post(route('registration.store', absolute: false), [
        'current_step' => 3,
        'next_step' => '',
    ])->assertRedirect(route('dashboard', absolute: false));

    $this->assertDatabaseHas('registrations', [
        'id_user' => $user->id,
        'status' => 'pending',
        'group' => 'A',
    ]);

    $registration = Registration::where('id_user', $user->id)->latest('id_registration')->first();
    expect($registration)->not->toBeNull();

    $this->assertDatabaseHas('students', [
        'id_registration' => $registration->id_registration,
        'name' => 'Budi Santoso',
        'group' => 'A',
    ]);

    $student = Student::where('id_registration', $registration->id_registration)->first();
    expect($student)->not->toBeNull();
    expect($student->status)->toBe('pending_payment');
});
