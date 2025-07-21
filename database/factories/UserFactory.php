<?php

namespace Database\Factories;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => 'User1',
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role' => $this->faker->randomElement([UserRole::ADMIN, UserRole::EMPLOYEE]),
            'profile_picture_url' => $this->faker->imageUrl(),
            'gender' => $this->faker->randomElement(['male', 'female']),
            'position' => $this->faker->jobTitle(),
            'telephone' => $this->faker->phoneNumber(),
            'city' => $this->faker->city(),
            'created_by' => 'admin',
            'created_at' => now(),
            'updated_at' => null,
            'remember_token' => Str::random(10),
            'location_id' => $this->faker->uuid(),
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
}
