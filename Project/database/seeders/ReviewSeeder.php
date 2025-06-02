<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Review;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $transactions = Transaction::where('status', 'completed')->get();
        foreach ($transactions as $transaction) {
            Review::factory()->create([
                'transaction_id' => $transaction->id,
                'reviewer_id' => $transaction->requester_id,
                'reviewee_id' => $transaction->worker_id,
            ]);
            Review::factory()->create([
                'transaction_id' => $transaction->id,
                'reviewer_id' => $transaction->worker_id,
                'reviewee_id' => $transaction->requester_id,
            ]);
        }
        
    }
}
