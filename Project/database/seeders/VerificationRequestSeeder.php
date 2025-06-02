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
                'status' => fake()->randomElement(['pending', 'rejected']),
            ]);
        }
        $workers = User::worker()->get()->random(10);
        foreach ($workers as $user) {
            VerificationRequest::factory()->create([
                'user_id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'status' => 'approved',
            ]);
        }
    }
}
