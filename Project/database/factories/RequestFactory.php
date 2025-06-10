<?php

namespace Database\Factories;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Request>
 */
class RequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
public function definition(): array
    {
    $startTime = Carbon::now()->addDays(rand(0, 7))->addHours(rand(0, 23))->addMinutes(rand(0, 59));
    $endTime = (clone $startTime)->addHours(rand(1, 6))->addMinutes(rand(0, 59));
        return [
            'title' => $this->faker->sentence(4),
            'slug' => $this->faker->slug(),
            'description' => $this->faker->paragraph(),
            'price' => $this->faker->randomFloat(2, 10000, 500000),
            'location' => $this->faker->address(),
            'status' => fake()->randomElement(['open', 'closed']),
            'start_time' => $startTime,
            'end_time' => $endTime
        ];
    }
}
