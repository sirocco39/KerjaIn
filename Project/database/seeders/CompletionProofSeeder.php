<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Transaction;
use App\Models\CompletionProof;

class CompletionProofSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $transactions = Transaction::where('status', 'completed')->where('status', 'submitted')->get();
        foreach($transactions as $transaction) {
  $transaction->completionProof()->create(
        CompletionProof::factory()->make()->toArray()
    );
        }
    }
}
