<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'phone_num' => fake()->phoneNumber(),
            'role' => 'guest',
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the user should be a superadmin.
     */
    public function superadmin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'superadmin',
        ]);
    }

    /**
     * Indicate that the user should be an administration staff.
     */
    public function administration(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'administration',
        ]);
    }

    /**
     * Indicate that the user should be a teacher.
     */
    public function teacher(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'teacher',
        ]);
    }

    /**
     * Indicate that the user should be a headmaster.
     */
    public function headmaster(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'headmaster',
        ]);
    }

    /**
     * Indicate that the user should be a guest.
     */
    public function guest(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'guest',
        ]);
    }
}
