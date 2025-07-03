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
        ];
    }


    public function openOffer(){
        return $this->state([
            'status' => 'open',
        ]);
    }

    public function rejectedOffer(){
        return $this->state([
            'status' => 'rejected',
        ]);
    }

    public function acceptedOffer(){
        return $this->state([
            'status' => 'accepted',
        ]);
    }
}
