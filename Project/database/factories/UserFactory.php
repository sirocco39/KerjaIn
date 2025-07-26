<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\Factory;

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
            'balance' => $this->faker->randomFloat(2, 0, 1000000),
            'role' => fake()->randomElement(['user', 'admin']),
            'is_worker' => false,
            'rating' => $this->faker->randomFloat(2, 1, 5),
            'job_done' => 0,
            'is_blocked' => false,
            'bank_acc_num' => null,
            'last_activity' => $this->faker->optional(0.8, null) // 80% chance to have a recent activity, 20% to be null
                ->dateTimeBetween('-1 week', 'now'), // Activity within the last week

        ];
    }

    public function admin()
    {
        return $this->state([
            'role' => 'admin',
            'rating' => 0,
            'job_done' => 0,
            'is_worker' => false,
            'last_activity' => Carbon::now(), // Admins are usually active recently

        ]);
    }

    public function worker()
    {
        return $this->state([
            'role' => 'user',
            'is_worker' => true,
            'job_done' => $this->faker->numberBetween(1, 100),
            'bank_acc_num' => $this->faker->numerify('##########'),
            'last_activity' => $this->faker->dateTimeBetween('-3 days', 'now'), // Workers are often active

        ]);
    }

    public function nonWorker()
    {
        return $this->state([
            'role' => 'user',
            'is_worker' => false,
            'job_done' => 0,
            'last_activity' => $this->faker->optional(0.5, null) // Non-workers might be less active or null
                ->dateTimeBetween('-2 weeks', 'now'),

        ]);
    }
}
