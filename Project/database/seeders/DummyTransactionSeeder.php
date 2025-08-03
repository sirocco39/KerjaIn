<?php

namespace Database\Seeders;

use App\Models\ChatRoom;
use App\Models\CompletionProof;
use App\Models\Payment;
use App\Models\Request as JobRequest; // Alias Request model to avoid conflict with Illuminate\Http\Request
use App\Models\Review;
use App\Models\Transaction;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Database\Console\Seeds\WithoutModelEvents; // Keep this if you use it elsewhere
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Faker\Factory as Faker;
use App\Models\Report; // <-- Ensure this is imported

class DummyTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'], // Use email as unique identifier for firstOrCreate
            [
                'first_name' => 'Admin',
                'last_name' => 'User',
                'password' => bcrypt('@Super645!'), // A common test password for admin
                'role' => 'admin', // Assuming 'admin' is a valid role from your schema
                'phone_number' => '081122334455',
                'balance' => 0.00, // Admins typically don't have balance
                'is_worker' => false,
                'rating' => 0.0,
                'job_done' => 0,
                'bank_acc_num' => null,
                'google_id' => null,
                'is_blocked' => false,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        );
    }
}
