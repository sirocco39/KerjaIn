<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Offer>
 */
class OfferFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'amount' => $this->faker->randomFloat(2, 10000, 500000),
            'status' => fake()->randomElement(['pending', 'accepted', 'rejected', 'withdrawn']),
        ];
    }


    public function openOffer(){
        return $this->state([
            'status' => 'open',
        ]);
    }

    public function closedOffer(){
        return $this->state([
            'status' => 'closed',
        ]);
    }
}
