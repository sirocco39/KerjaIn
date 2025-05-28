<?php

namespace Database\Seeders;

use App\Models\ChatMessage;
use App\Models\ChatRoom;
use App\Models\Payment;
use App\Models\Report;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            VerificationRequestSeeder::class,
            RequestSeeder::class,
            ChatRoomSeeder::class,
            ChatMessageSeeder::class,
            OfferSeeder::class,
            PaymentSeeder::class,
            TransactionSeeder::class,
            CompletionProofSeeder::class,
            ReviewSeeder::class,
            ReportSeeder::class,
        ]);
    }
}
