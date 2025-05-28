<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
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
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => bcrypt('password'),
            'phone_number' => fake()->numerify('08##########'),
            'saldokerjain' => $this->faker->randomFloat(2, 0, 1000000),
            'role' => fake()->randomElement(['user', 'admin']),
            'is_worker' => false,
            'rating' => $this->faker->randomFloat(2, 1, 5),
            'job_done' => 0,
            'is_blocked' => false,
            'bank_acc_num' => null,
        ];
    }

    public function admin(){
        return $this->state([
            'role' => 'admin',
            'rating' => 0,
            'job_done' => 0,
            'is_worker' => false,
        ]);
    }

    public function worker()
    {
        return $this->state([
            'role' => 'user',
            'is_worker' => true,
            'job_done' => $this->faker->numberBetween(1, 100),
            'bank_acc_num' => $this->faker->numerify('##########'),
        ]);
    }

    public function nonWorker()
    {
        return $this->state([
            'role' => 'user',
            'is_worker' => false,
            'job_done' => 0,
        ]);
    }
}
