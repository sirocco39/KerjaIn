<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Transaction;
use App\Models\Report;

class ReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $transactions = Transaction::where('status', 'completed')->get();
foreach ($transactions as $transaction) {
    Report::factory()->create([
        'transaction_id' => $transaction->id,
        'reporter_id' => $transaction->requester_id,
        'reported_id' => $transaction->worker_id,
    ]);

    Report::factory()->create([
        'transaction_id' => $transaction->id,
        'reporter_id' => $transaction->worker_id,
        'reported_id' => $transaction->requester_id,
    ]);
}
    }
}
