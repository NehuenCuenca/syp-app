<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'username' => fake()->unique()->userName(),
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'role' => 'Usuario',
        ];
    }

    /**
     * Configure the factory to create an Admin user.
     */
    public function admin(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => 'Admin',
            ];
        });
    }
}
