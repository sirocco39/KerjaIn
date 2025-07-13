<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User; // Assuming User model exists
use App\Models\Request as JobRequest; // Alias Request model to avoid conflict with Illuminate\Http\Request
use App\Models\Transaction; // Assuming Transaction model exists

class DummyTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // --- Cleanup existing dummy data to prevent duplicates on re-seeding ---
        // Delete transactions created by dummy users
        Transaction::whereHas('requester', function ($query) {
            $query->whereIn('email', ['hansengunawan64@gmail.com', 'worker@example.com']);
        })->orWhereHas('worker', function ($query) {
            $query->whereIn('email', ['hansengunawan64@gmail.com', 'worker@example.com']);
        })->forceDelete(); // Use forceDelete if soft deletes are enabled but you want to truly remove

        // Delete dummy requests
        JobRequest::where('title', 'Dummy Service Request')->forceDelete();

        // Delete dummy users
        User::whereIn('email', ['hansengunawan64@gmail.com', 'worker@example.com'])->forceDelete();
        // --- End Cleanup ---


        // Create a dummy requester user with genuine-like email and password
        $requester = User::firstOrCreate(
            ['email' => 'hansengunawan64@gmail.com'], // Use email as unique identifier for firstOrCreate
            [
                'first_name' => 'Hansen',
                'last_name' => 'Gunawan',
                'password' => bcrypt('@Super645!'), // Hashed password
                'role' => 'user', // Assuming 'user' is a valid role from your schema
                'phone_number' => '081234567890', // Example phone number
                'saldokerjain' => 100000.00, // Example balance
                'is_worker' => true,
                'rating' => 0.0,
                'job_done' => 0,
                'bank_acc_num' => null, // Or an example bank account number if needed
                'google_id' => null,
                'is_blocked' => false,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        );

        // Create a dummy worker user
        $worker = User::firstOrCreate(
            ['email' => 'worker@example.com'], // Use email as unique identifier for firstOrCreate
            [
                'first_name' => 'Dummy',
                'last_name' => 'Worker',
                'password' => bcrypt('password'), // A common test password, securely hashed
                'role' => 'user', // Assuming 'user' is a valid role
                'phone_number' => '089876543210', // Example phone number
                'saldokerjain' => 300000.00, // Example balance
                'is_worker' => true,
                'rating' => 4.5,
                'job_done' => 5,
                'bank_acc_num' => '1234567890', // Example bank account number
                'google_id' => null,
                'is_blocked' => false,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        );

        // Create a dummy request
        $jobRequest = JobRequest::firstOrCreate(
            ['title' => 'Dummy Service Request'], // Use title as unique identifier for firstOrCreate
            [
                'requester_id' => $requester->id,
                'description' => 'This is a dummy request for testing purposes. It requires a skilled worker to complete the task efficiently.',
                'location' => 'Jakarta, Indonesia',
                'price' => 200000.00, // Price is on the request table
                'status' => 'open', // Initial status for the request (from your requests table enum)
                'start_time' => Carbon::now()->subDays(12),
                'end_time' => Carbon::now()->subDays(10),
                'slug' => Str::slug('Dummy Service Request ' . Str::random(5)), // Ensure slug is unique
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        );

        // Create dummy transactions with various statuses
        $transactionsData = [
            [
                'request_id' => $jobRequest->id,
                'requester_id' => $requester->id,
                'worker_id' => $worker->id,
                'status' => 'accepted',
                'order_number' => 'ORD-' . Str::upper(Str::random(8)),
                'created_at' => Carbon::now()->subDays(6),
                'updated_at' => Carbon::now()->subDays(5),
            ],
            [
                'request_id' => $jobRequest->id,
                'requester_id' => $requester->id,
                'worker_id' => $worker->id,
                'status' => 'in progress',
                'order_number' => 'ORD-' . Str::upper(Str::random(8)),
                'created_at' => Carbon::now()->subDays(4),
                'updated_at' => Carbon::now()->subDays(3),
            ],
            [
                'request_id' => $jobRequest->id,
                'requester_id' => $requester->id,
                'worker_id' => $worker->id,
                'status' => 'submitted',
                'order_number' => 'ORD-' . Str::upper(Str::random(8)),
                'created_at' => Carbon::now()->subDays(3),
                'updated_at' => Carbon::now()->subDays(2),
            ],
            [
                'request_id' => $jobRequest->id,
                'requester_id' => $requester->id,
                'worker_id' => $worker->id,
                'status' => 'completed',
                'order_number' => 'ORD-' . Str::upper(Str::random(8)),
                'created_at' => Carbon::now()->subDays(11),
                'updated_at' => Carbon::now()->subDays(1), // Completed recently
            ],
            [
                'request_id' => $jobRequest->id,
                'requester_id' => $requester->id,
                'worker_id' => $worker->id,
                'status' => 'cancelled',
                'order_number' => 'ORD-' . Str::upper(Str::random(8)),
                'created_at' => Carbon::now()->subDays(8),
                'updated_at' => Carbon::now()->subDays(2), // Cancelled recently
            ],
        ];

        foreach ($transactionsData as $data) {
            Transaction::firstOrCreate(
                ['order_number' => $data['order_number']], // Unique key for firstOrCreate
                $data
            );
        }
    }
}
