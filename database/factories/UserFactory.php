<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'username' => $this->faker->userName(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
        ];
    }

    public function unverified(): UserFactory
    {
        return $this->state(fn() => ['email_verified_at' => null]);
    }
}
