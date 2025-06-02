<?php

namespace Database\Seeders;

use App\Models\Offer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Request;
use App\Models\Transaction;
use App\Models\User;


class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $offers = Offer::where('status', 'closed')->get();
        foreach($offers as $offer){
            Transaction::factory()->create([
                'request_id' => $offer->request_id,
                'requester_id' => $offer->requester_id,
                'worker_id' => $offer->worker_id,
            ]);
        }
    }
}
