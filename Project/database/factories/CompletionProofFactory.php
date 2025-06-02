<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CompletionProof>
 */
class CompletionProofFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'photo_url' => $this->faker->imageUrl(),
            'note' => $this->faker->sentence(),
            'submitted_at' => now(),
        ];
    }
}
