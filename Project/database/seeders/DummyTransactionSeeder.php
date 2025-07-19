<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Request as JobRequest;
use App\Models\Transaction;
use App\Models\ChatRoom; // Import ChatRoom model

class DummyTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define all dummy emails used in this seeder for cleanup
        $dummyEmails = [
            'hansengunawan64@gmail.com',
            'worker@example.com',
            'requester2@example.com',
            'worker2@example.com',
        ];

        // Define all dummy request titles for cleanup
        $dummyRequestTitles = [
            'Dummy Service Request',
            'Another Dummy Service Request',
            'Urgent Task Needed',
            'Quick Fix Job',
            'Maintenance Service Request',
            'Software Development Project',
            'Graphic Design Task',
        ];

        // --- Cleanup existing dummy data to prevent duplicates on re-seeding ---
        // Delete transactions created by dummy users
        Transaction::where(function ($query) use ($dummyEmails) {
            $query->whereHas('requester', function ($q) use ($dummyEmails) {
                $q->whereIn('email', $dummyEmails);
            })->orWhereHas('worker', function ($q) use ($dummyEmails) {
                $q->whereIn('email', $dummyEmails);
            });
        })->forceDelete();

        // Delete chat rooms linked to dummy requests/users (Crucial step for cleanup)
        ChatRoom::where(function ($query) use ($dummyEmails, $dummyRequestTitles) {
            $query->whereHas('requester', function ($q) use ($dummyEmails) {
                $q->whereIn('email', $dummyEmails);
            })->orWhereHas('worker', function ($q) use ($dummyEmails) {
                $q->whereIn('email', $dummyEmails);
            })->orWhereHas('request', function ($q) use ($dummyRequestTitles) {
                $q->whereIn('title', $dummyRequestTitles);
            });
        })->forceDelete();

        // Delete dummy requests
        JobRequest::whereIn('title', $dummyRequestTitles)->forceDelete();

        // Delete dummy users
        User::whereIn('email', $dummyEmails)->forceDelete();
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
                'balance' => 9999999.00, // Example balance
                'locked_balance' => 0.00, // Initialize locked_balance
                'is_worker' => true, // Can also act as a worker
                'rating' => 4.8,
                'job_done' => 10,
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
                'balance' => 300000.00, // Example balance
                'locked_balance' => 0.00, // Initialize locked_balance
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

        // Create another dummy requester/worker user
        $requester2 = User::firstOrCreate(
            ['email' => 'requester2@example.com'],
            [
                'first_name' => 'Alice',
                'last_name' => 'Smith',
                'password' => bcrypt('password123'),
                'role' => 'user',
                'phone_number' => '087654321098',
                'balance' => 75000.00,
                'locked_balance' => 0.00, // Initialize locked_balance
                'is_worker' => true,
                'rating' => 4.2,
                'job_done' => 3,
                'bank_acc_num' => '9876543210',
                'google_id' => null,
                'is_blocked' => false,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        );

        // Create another dummy worker user
        $worker2 = User::firstOrCreate(
            ['email' => 'worker2@example.com'],
            [
                'first_name' => 'Bob',
                'last_name' => 'Johnson',
                'password' => bcrypt('securepass'),
                'role' => 'user',
                'phone_number' => '081122334455',
                'balance' => 150000.00,
                'locked_balance' => 0.00, // Initialize locked_balance
                'is_worker' => true,
                'rating' => 4.9,
                'job_done' => 8,
                'bank_acc_num' => '1122334455',
                'google_id' => null,
                'is_blocked' => false,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        );


        // Create dummy requests
        $jobRequest1 = JobRequest::firstOrCreate(
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
                'created_at' => Carbon::now()->subDays(15),
                'updated_at' => Carbon::now()->subDays(15),
                'final_price' => 200000.00,
            ]
        );

        $jobRequest2 = JobRequest::firstOrCreate(
            ['title' => 'Another Dummy Service Request'],
            [
                'requester_id' => $requester2->id,
                'description' => 'A second dummy request for testing different scenarios and user interactions.',
                'location' => 'Bandung, Indonesia',
                'price' => 150000.00,
                'status' => 'open',
                'start_time' => Carbon::now()->subDays(8),
                'end_time' => Carbon::now()->subDays(6),
                'slug' => Str::slug('Another Dummy Service Request ' . Str::random(5)),
                'created_at' => Carbon::now()->subDays(10),
                'updated_at' => Carbon::now()->subDays(10),
                'final_price' => 150000.00,
            ]
        );

        $jobRequest3 = JobRequest::firstOrCreate(
            ['title' => 'Urgent Task Needed'],
            [
                'requester_id' => $requester->id,
                'description' => 'An urgent task requiring immediate attention and quick completion.',
                'location' => 'Surabaya, Indonesia',
                'price' => 300000.00,
                'status' => 'open',
                'start_time' => Carbon::now()->subDays(5),
                'end_time' => Carbon::now()->subDays(3),
                'slug' => Str::slug('Urgent Task Needed ' . Str::random(5)),
                'created_at' => Carbon::now()->subDays(7),
                'updated_at' => Carbon::now()->subDays(7),
                'final_price' => 300000.00,
            ]
        );

        $jobRequest4 = JobRequest::firstOrCreate(
            ['title' => 'Quick Fix Job'],
            [
                'requester_id' => $requester2->id,
                'description' => 'A small, quick job that can be completed in a few hours.',
                'location' => 'Yogyakarta, Indonesia',
                'price' => 50000.00,
                'status' => 'open',
                'start_time' => Carbon::now()->subDays(2),
                'end_time' => Carbon::now()->subDays(1),
                'slug' => Str::slug('Quick Fix Job ' . Str::random(5)),
                'created_at' => Carbon::now()->subDays(3),
                'updated_at' => Carbon::now()->subDays(3),
                'final_price' => 50000.00,
            ]
        );

        $jobRequest5 = JobRequest::firstOrCreate(
            ['title' => 'Maintenance Service Request'],
            [
                'requester_id' => $requester->id,
                'description' => 'Routine maintenance service for an existing system.',
                'location' => 'Semarang, Indonesia',
                'price' => 120000.00,
                'status' => 'open',
                'start_time' => Carbon::now()->subDays(9),
                'end_time' => Carbon::now()->subDays(7),
                'slug' => Str::slug('Maintenance Service Request ' . Str::random(5)),
                'created_at' => Carbon::now()->subDays(10),
                'updated_at' => Carbon::now()->subDays(10),
                'final_price' => 120000.00,
            ]
        );

        $jobRequest6 = JobRequest::firstOrCreate(
            ['title' => 'Software Development Project'],
            [
                'requester_id' => $requester2->id,
                'description' => 'A complex software development project requiring specialized skills.',
                'location' => 'Denpasar, Indonesia',
                'price' => 500000.00,
                'status' => 'open',
                'start_time' => Carbon::now()->subDays(20),
                'end_time' => Carbon::now()->subDays(15),
                'slug' => Str::slug('Software Development Project ' . Str::random(5)),
                'created_at' => Carbon::now()->subDays(25),
                'updated_at' => Carbon::now()->subDays(25),
                'final_price' => 500000.00,
            ]
        );

        $jobRequest7 = JobRequest::firstOrCreate(
            ['title' => 'Graphic Design Task'],
            [
                'requester_id' => $requester->id,
                'description' => 'Creation of marketing materials and graphic assets.',
                'location' => 'Makassar, Indonesia',
                'price' => 80000.00,
                'status' => 'open',
                'start_time' => Carbon::now()->subDays(4),
                'end_time' => Carbon::now()->subDays(2),
                'slug' => Str::slug('Graphic Design Task ' . Str::random(5)),
                'created_at' => Carbon::now()->subDays(5),
                'updated_at' => Carbon::now()->subDays(5),
                'final_price' => 80000.00,
            ]
        );

        // Create dummy transactions with various statuses and user roles
        $transactionsData = [
            // Transactions for requester (Hansen) as requester, worker as worker
            [
                'request_id' => $jobRequest1->id,
                'requester_id' => $requester->id,
                'worker_id' => $worker->id,
                'status' => 'accepted',
                'order_number' => 'ORD-' . Str::upper(Str::random(8)),
                'created_at' => Carbon::now()->subDays(6),
                'updated_at' => Carbon::now()->subDays(5),
                'start_work' => Carbon::now()->subDays(5)->setTime(9, 0), // Add start_work
                'finish_work' => Carbon::now()->subDays(5)->setTime(17, 0), // Add finish_work
            ],
            [
                'request_id' => $jobRequest1->id,
                'requester_id' => $requester->id,
                'worker_id' => $worker->id,
                'status' => 'in progress',
                'order_number' => 'ORD-' . Str::upper(Str::random(8)),
                'created_at' => Carbon::now()->subDays(4),
                'updated_at' => Carbon::now()->subDays(3),
                'start_work' => Carbon::now()->subDays(3)->setTime(10, 0),
                'finish_work' => null, // Not finished yet
            ],
            [
                'request_id' => $jobRequest1->id,
                'requester_id' => $requester->id,
                'worker_id' => $worker->id,
                'status' => 'submitted',
                'order_number' => 'ORD-' . Str::upper(Str::random(8)),
                'created_at' => Carbon::now()->subDays(3),
                'updated_at' => Carbon::now()->subDays(2),
                'start_work' => Carbon::now()->subDays(2)->setTime(8, 0),
                'finish_work' => Carbon::now()->subDays(2)->setTime(16, 0),
            ],
            [
                'request_id' => $jobRequest1->id,
                'requester_id' => $requester->id,
                'worker_id' => $worker->id,
                'status' => 'completed',
                'order_number' => 'ORD-' . Str::upper(Str::random(8)),
                'created_at' => Carbon::now()->subDays(11),
                'updated_at' => Carbon::now()->subDays(1), // Completed recently
                'start_work' => Carbon::now()->subDays(10)->setTime(9, 0),
                'finish_work' => Carbon::now()->subDays(1)->setTime(18, 0),
            ],
            [
                'request_id' => $jobRequest1->id,
                'requester_id' => $requester->id,
                'worker_id' => $worker->id,
                'status' => 'cancelled',
                'order_number' => 'ORD-' . Str::upper(Str::random(8)),
                'created_at' => Carbon::now()->subDays(8),
                'updated_at' => Carbon::now()->subDays(2), // Cancelled recently
                'start_work' => null, // Not applicable for cancelled before start
                'finish_work' => null,
            ],

            // Transactions for requester2 (Alice) as requester, worker2 (Bob) as worker
            [
                'request_id' => $jobRequest2->id,
                'requester_id' => $requester2->id,
                'worker_id' => $worker2->id,
                'status' => 'accepted',
                'order_number' => 'ORD-' . Str::upper(Str::random(8)),
                'created_at' => Carbon::now()->subDays(7),
                'updated_at' => Carbon::now()->subDays(6),
                'start_work' => Carbon::now()->subDays(6)->setTime(9, 0),
                'finish_work' => null,
            ],
            [
                'request_id' => $jobRequest2->id,
                'requester_id' => $requester2->id,
                'worker_id' => $worker2->id,
                'status' => 'in progress',
                'order_number' => 'ORD-' . Str::upper(Str::random(8)),
                'created_at' => Carbon::now()->subDays(5),
                'updated_at' => Carbon::now()->subDays(4),
                'start_work' => Carbon::now()->subDays(4)->setTime(10, 0),
                'finish_work' => null,
            ],
            [
                'request_id' => $jobRequest2->id,
                'requester_id' => $requester2->id,
                'worker_id' => $worker2->id,
                'status' => 'completed',
                'order_number' => 'ORD-' . Str::upper(Str::random(8)),
                'created_at' => Carbon::now()->subDays(9),
                'updated_at' => Carbon::now()->subDays(3),
                'start_work' => Carbon::now()->subDays(8)->setTime(11, 0),
                'finish_work' => Carbon::now()->subDays(3)->setTime(15, 0),
            ],
            [
                'request_id' => $jobRequest2->id,
                'requester_id' => $requester2->id,
                'worker_id' => $worker2->id,
                'status' => 'cancelled',
                'order_number' => 'ORD-' . Str::upper(Str::random(8)),
                'created_at' => Carbon::now()->subDays(10),
                'updated_at' => Carbon::now()->subDays(5),
                'start_work' => null,
                'finish_work' => null,
            ],

            // Transactions where requester (Hansen) is the requester, worker2 (Bob) is the worker
            [
                'request_id' => $jobRequest3->id,
                'requester_id' => $requester->id,
                'worker_id' => $worker2->id,
                'status' => 'accepted',
                'order_number' => 'ORD-' . Str::upper(Str::random(8)),
                'created_at' => Carbon::now()->subDays(4),
                'updated_at' => Carbon::now()->subDays(3),
                'start_work' => Carbon::now()->subDays(3)->setTime(9, 30),
                'finish_work' => null,
            ],
            [
                'request_id' => $jobRequest3->id,
                'requester_id' => $requester->id,
                'worker_id' => $worker2->id,
                'status' => 'completed',
                'order_number' => 'ORD-' . Str::upper(Str::random(8)),
                'created_at' => Carbon::now()->subDays(6),
                'updated_at' => Carbon::now()->subDays(2),
                'start_work' => Carbon::now()->subDays(5)->setTime(10, 0),
                'finish_work' => Carbon::now()->subDays(2)->setTime(14, 0),
            ],

            // Transactions where requester2 (Alice) is the requester, worker (Dummy Worker) is the worker
            [
                'request_id' => $jobRequest4->id,
                'requester_id' => $requester2->id,
                'worker_id' => $worker->id,
                'status' => 'in progress',
                'order_number' => 'ORD-' . Str::upper(Str::random(8)),
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDays(1),
                'start_work' => Carbon::now()->subDays(1)->setTime(8, 0),
                'finish_work' => null,
            ],
            [
                'request_id' => $jobRequest4->id,
                'requester_id' => $requester2->id,
                'worker_id' => $worker->id,
                'status' => 'submitted',
                'order_number' => 'ORD-' . Str::upper(Str::random(8)),
                'created_at' => Carbon::now()->subDays(1),
                'updated_at' => Carbon::now(),
                'start_work' => Carbon::now()->subHours(12)->setTime(9,0), // Example: started today, submitted today
                'finish_work' => Carbon::now()->subHours(1)->setTime(16,0),
            ],

            // Transactions demonstrating requester (Hansen) as a worker
            [
                'request_id' => $jobRequest5->id,
                'requester_id' => $requester2->id, // Alice requests
                'worker_id' => $requester->id,    // Hansen works
                'status' => 'accepted',
                'order_number' => 'ORD-' . Str::upper(Str::random(8)),
                'created_at' => Carbon::now()->subDays(8),
                'updated_at' => Carbon::now()->subDays(7),
                'start_work' => Carbon::now()->subDays(7)->setTime(13, 0),
                'finish_work' => null,
            ],
            [
                'request_id' => $jobRequest5->id,
                'requester_id' => $requester2->id, // Alice requests
                'worker_id' => $requester->id,    // Hansen works
                'status' => 'completed',
                'order_number' => 'ORD-' . Str::upper(Str::random(8)),
                'created_at' => Carbon::now()->subDays(10),
                'updated_at' => Carbon::now()->subDays(6),
                'start_work' => Carbon::now()->subDays(9)->setTime(9, 0),
                'finish_work' => Carbon::now()->subDays(6)->setTime(17, 0),
            ],

            // Transactions demonstrating worker (Dummy Worker) as a requester
            [
                'request_id' => $jobRequest6->id,
                'requester_id' => $worker->id,    // Dummy Worker requests
                'worker_id' => $worker2->id,      // Bob works
                'status' => 'in progress',
                'order_number' => 'ORD-' . Str::upper(Str::random(8)),
                'created_at' => Carbon::now()->subDays(18),
                'updated_at' => Carbon::now()->subDays(10),
                'start_work' => Carbon::now()->subDays(15)->setTime(10, 0),
                'finish_work' => null,
            ],
            [
                'request_id' => $jobRequest6->id,
                'requester_id' => $worker->id,    // Dummy Worker requests
                'worker_id' => $worker2->id,      // Bob works
                'status' => 'submitted',
                'order_number' => 'ORD-' . Str::upper(Str::random(8)),
                'created_at' => Carbon::now()->subDays(16),
                'updated_at' => Carbon::now()->subDays(8),
                'start_work' => Carbon::now()->subDays(12)->setTime(9, 0),
                'finish_work' => Carbon::now()->subDays(8)->setTime(17, 0),
            ],

            // More varied statuses and combinations
            [
                'request_id' => $jobRequest7->id,
                'requester_id' => $requester->id,
                'worker_id' => $worker2->id,
                'status' => 'accepted',
                'order_number' => 'ORD-' . Str::upper(Str::random(8)),
                'created_at' => Carbon::now()->subDays(3),
                'updated_at' => Carbon::now()->subDays(2),
                'start_work' => Carbon::now()->subDays(2)->setTime(14, 0),
                'finish_work' => null,
            ],
            [
                'request_id' => $jobRequest7->id,
                'requester_id' => $requester->id,
                'worker_id' => $worker2->id,
                'status' => 'completed',
                'order_number' => 'ORD-' . Str::upper(Str::random(8)),
                'created_at' => Carbon::now()->subDays(5),
                'updated_at' => Carbon::now()->subDays(1),
                'start_work' => Carbon::now()->subDays(4)->setTime(9, 0),
                'finish_work' => Carbon::now()->subDays(1)->setTime(17, 0),
            ],
            [
                'request_id' => $jobRequest3->id, // Using an existing request
                'requester_id' => $requester2->id, // Alice requests
                'worker_id' => $requester->id,    // Hansen works
                'status' => 'cancelled',
                'order_number' => 'ORD-' . Str::upper(Str::random(8)),
                'created_at' => Carbon::now()->subDays(9),
                'updated_at' => Carbon::now()->subDays(7),
                'start_work' => null,
                'finish_work' => null,
            ],
            [
                'request_id' => $jobRequest6->id, // Using an existing request
                'requester_id' => $requester->id, // Hansen requests
                'worker_id' => $worker->id,        // Dummy Worker works
                'status' => 'completed',
                'order_number' => 'ORD-' . Str::upper(Str::random(8)),
                'created_at' => Carbon::now()->subDays(22),
                'updated_at' => Carbon::now()->subDays(18),
                'start_work' => Carbon::now()->subDays(20)->setTime(8, 30),
                'finish_work' => Carbon::now()->subDays(18)->setTime(16, 0),
            ],
        ];

        foreach ($transactionsData as $data) {
            $transaction = Transaction::firstOrCreate(
                ['order_number' => $data['order_number']], // Unique key for firstOrCreate
                $data
            );

            // Ensure a chat room exists for this specific transaction's requester and worker
            ChatRoom::firstOrCreate(
                [
                    'request_id' => $transaction->request_id,
                    'requester_id' => $transaction->requester_id,
                    'worker_id' => $transaction->worker_id,
                ],
                [
                    'is_open' => !in_array($transaction->status, ['completed', 'cancelled']), // Open unless completed/cancelled
                    'created_at' => $transaction->created_at,
                    'updated_at' => $transaction->updated_at,
                ]
            );
        }
    }
}
