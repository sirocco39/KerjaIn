<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Offer;
use App\Models\Payment;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $offers = Offer::where('status', 'closed')->get();
        foreach ($offers as $offer) {
            Payment::factory()->create([
                'offer_id' => $offer->id,
                'request_id' => $offer->request_id,
                'amount' => $offer->amount,
            ]);
        }
    }
}
