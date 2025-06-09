<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\VerificationRequest;



class VerificationRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $nonWorkers = User::nonWorker()->get()->random(10);
        foreach ($nonWorkers as $user) {
            VerificationRequest::factory()->create([
                'user_id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'account_name' => $user->first_name . ' ' . $user->last_name,
                'account_number' => fake()->numerify(str_repeat('#', 10)),
                'status' => fake()->randomElement(['pending', 'rejected']),
            ]);
        }
            VerificationRequest::factory()->create([
                'user_id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'account_name' => $user->first_name . ' ' . $user->last_name,
                'account_number' => fake()->numerify(str_repeat('#', 10)),
                'status' => 'approved',
            ]);
    }
}
