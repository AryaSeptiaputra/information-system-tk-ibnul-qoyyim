<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'phone_num', 'role', 'status', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the teacher details for this user (if the user is a teacher).
     */
    public function teacherDetail(): HasOne
    {
        return $this->hasOne(TeacherDetail::class, 'id_user', 'id');
    }

    /**
     * Get the parent/guardian details for this user (if the user is a guest/parent).
     */
    public function parentGuardian(): HasOne
    {
        return $this->hasOne(ParentGuardian::class, 'id_user', 'id');
    }

    /**
     * Get all registrations made by this user.
     */
    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class, 'id_user', 'id');
    }

    /**
     * Get all student records linked through registrations.
     */
    public function students(): HasMany
    {
        return $this->hasManyThrough(
            Student::class,
            Registration::class,
            'id_user',
            'id_registration',
            'id',
            'id_registration'
        );
    }
}
