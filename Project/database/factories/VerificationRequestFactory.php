<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VerificationRequest>
 */
class VerificationRequestFactory extends Factory
{
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
            'nik' => $this->faker->numerify('################'),
            'birthdate' => $this->faker->date(),
            'gender' => $this->faker->randomElement(['Male', 'Female']),
            'address' => $this->faker->address(),
            'phone_number' => $this->faker->numerify('08##########'),
            'photo_url' => $this->faker->imageUrl(),
            'id_card_url' => $this->faker->imageUrl(),
            'selfie_with_id_card_url' => $this->faker->imageUrl(),
            'status' => 'pending',
        ];
    }
}
