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
use App\Models\Review; // Assuming Review model exists
use Faker\Factory as Faker;

class DummyTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

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
        })->forceDelete(); // Use forceDelete if soft deletes are enabled but you want to truly remove

        // Delete dummy requests
        JobRequest::whereIn('title', $dummyRequestTitles)->forceDelete();

        // Delete dummy users
        User::whereIn('email', $dummyEmails)->forceDelete();

        // Delete dummy reviews associated with dummy transactions (optional, but good for a clean re-seed)
        Review::whereIn('transaction_id', Transaction::where(function ($query) use ($dummyEmails) {
            $query->whereHas('requester', function ($q) use ($dummyEmails) {
                $q->whereIn('email', $dummyEmails);
            })->orWhereHas('worker', function ($q) use ($dummyEmails) {
                $q->whereIn('email', $dummyEmails);
            });
        })->pluck('id'))->forceDelete();
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
                'is_worker' => true, // Can also act as a worker
                'rating' => 4.8,
                'job_done' => 10,
                'bank_acc_num' => null, // Or an example bank account number if needed
                'google_id' => null,
                'is_blocked' => false,
                'created_at' => Carbon::now()->subMonth(),
                'updated_at' => Carbon::now()->subMonth(),
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
                'is_worker' => true,
                'rating' => 4.5,
                'job_done' => 5,
                'bank_acc_num' => '1234567890', // Example bank account number
                'google_id' => null,
                'is_blocked' => false,
                'created_at' => Carbon::now()->subMonth()->addDays(3),
                'updated_at' => Carbon::now()->subMonth()->addDays(3),
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
                'is_worker' => true,
                'rating' => 4.2,
                'job_done' => 3,
                'bank_acc_num' => '9876543210',
                'google_id' => null,
                'is_blocked' => false,
                'created_at' => Carbon::now()->subMonth()->addDays(5),
                'updated_at' => Carbon::now()->subMonth()->addDays(5),
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
                'is_worker' => true,
                'rating' => 4.9,
                'job_done' => 8,
                'bank_acc_num' => '1122334455',
                'google_id' => null,
                'is_blocked' => false,
                'created_at' => Carbon::now()->subMonth()->addDays(7),
                'updated_at' => Carbon::now()->subMonth()->addDays(7),
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
                'start_time' => Carbon::now()->subDays(35),
                'end_time' => Carbon::now()->subDays(33),
                'slug' => Str::slug('Dummy Service Request ' . Str::random(5)), // Ensure slug is unique
                'created_at' => Carbon::now()->subDays(38),
                'updated_at' => Carbon::now()->subDays(38),
                'final_price'=> 200000.00,
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
                'start_time' => Carbon::now()->subDays(30),
                'end_time' => Carbon::now()->subDays(28),
                'slug' => Str::slug('Another Dummy Service Request ' . Str::random(5)),
                'created_at' => Carbon::now()->subDays(32),
                'updated_at' => Carbon::now()->subDays(32),
                'final_price'=> 150000.00,
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
                'start_time' => Carbon::now()->subDays(25),
                'end_time' => Carbon::now()->subDays(23),
                'slug' => Str::slug('Urgent Task Needed ' . Str::random(5)),
                'created_at' => Carbon::now()->subDays(27),
                'updated_at' => Carbon::now()->subDays(27),
                'final_price'=> 300000.00,
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
                'end_time' => Carbon::now()->subDay(),
                'slug' => Str::slug('Quick Fix Job ' . Str::random(5)),
                'created_at' => Carbon::now()->subDays(4),
                'updated_at' => Carbon::now()->subDays(4),
                'final_price'=> 50000.00,
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
                'start_time' => Carbon::now()->subDays(31),
                'end_time' => Carbon::now()->subDays(29),
                'slug' => Str::slug('Maintenance Service Request ' . Str::random(5)),
                'created_at' => Carbon::now()->subDays(33),
                'updated_at' => Carbon::now()->subDays(33),
                'final_price'=> 120000.00,
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
                'start_time' => Carbon::now()->subDays(45),
                'end_time' => Carbon::now()->subDays(40),
                'slug' => Str::slug('Software Development Project ' . Str::random(5)),
                'created_at' => Carbon::now()->subDays(50),
                'updated_at' => Carbon::now()->subDays(50),
                'final_price'=> 500000.00,
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
                'start_time' => Carbon::now()->subDays(1),
                'end_time' => Carbon::now(),
                'slug' => Str::slug('Graphic Design Task ' . Str::random(5)),
                'created_at' => Carbon::now()->subDays(6),
                'updated_at' => Carbon::now()->subDays(6),
                'final_price'=> 80000.00,
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
                'created_at' => Carbon::now()->subDays(36),
                'updated_at' => Carbon::now()->subDays(35),
            ],
            [
                'request_id' => $jobRequest1->id,
                'requester_id' => $requester->id,
                'worker_id' => $worker->id,
                'status' => 'in progress',
                'order_number' => 'ORD-' . Str::upper(Str::random(8)),
                'created_at' => Carbon::now()->subDays(34),
                'updated_at' => Carbon::now()->subDays(33),
            ],
            [
                'request_id' => $jobRequest1->id,
                'requester_id' => $requester->id,
                'worker_id' => $worker->id,
                'status' => 'submitted',
                'order_number' => 'ORD-' . Str::upper(Str::random(8)),
                'created_at' => Carbon::now()->subDays(33),
                'updated_at' => Carbon::now()->subDays(32),
            ],
            [
                'request_id' => $jobRequest1->id,
                'requester_id' => $requester->id,
                'worker_id' => $worker->id,
                'status' => 'completed',
                'start_work' => Carbon::now()->subDays(35)->addHours(9),
                'finish_work' => Carbon::now()->subDays(35)->addHours(13),
                'order_number' => 'ORD-' . Str::upper(Str::random(8)),
                'created_at' => Carbon::now()->subDays(38),
                'updated_at' => Carbon::now()->subDays(32), // Completed in the previous month
            ],
            [
                'request_id' => $jobRequest1->id,
                'requester_id' => $requester->id,
                'worker_id' => $worker->id,
                'status' => 'cancelled',
                'order_number' => 'ORD-' . Str::upper(Str::random(8)),
                'created_at' => Carbon::now()->subDays(33),
                'updated_at' => Carbon::now()->subDays(32), // Cancelled in the previous month
            ],

            // Transactions for requester2 (Alice) as requester, worker2 (Bob) as worker
            [
                'request_id' => $jobRequest2->id,
                'requester_id' => $requester2->id,
                'worker_id' => $worker2->id,
                'status' => 'accepted',
                'order_number' => 'ORD-' . Str::upper(Str::random(8)),
                'created_at' => Carbon::now()->subDays(31),
                'updated_at' => Carbon::now()->subDays(30),
            ],
            [
                'request_id' => $jobRequest2->id,
                'requester_id' => $requester2->id,
                'worker_id' => $worker2->id,
                'status' => 'in progress',
                'order_number' => 'ORD-' . Str::upper(Str::random(8)),
                'created_at' => Carbon::now()->subDays(29),
                'updated_at' => Carbon::now()->subDays(28),
            ],
            [
                'request_id' => $jobRequest2->id,
                'requester_id' => $requester2->id,
                'worker_id' => $worker2->id,
                'status' => 'completed',
                'start_work' => Carbon::now()->subDays(30)->addHours(10),
                'finish_work' => Carbon::now()->subDays(30)->addHours(15),
                'order_number' => 'ORD-' . Str::upper(Str::random(8)),
                'created_at' => Carbon::now()->subDays(32),
                'updated_at' => Carbon::now()->subDays(28), // Completed in the previous month
            ],
            [
                'request_id' => $jobRequest2->id,
                'requester_id' => $requester2->id,
                'worker_id' => $worker2->id,
                'status' => 'cancelled',
                'order_number' => 'ORD-' . Str::upper(Str::random(8)),
                'created_at' => Carbon::now()->subDays(32),
                'updated_at' => Carbon::now()->subDays(30), // Cancelled in the previous month
            ],

            // Transactions where requester (Hansen) is the requester, worker2 (Bob) is the worker
            [
                'request_id' => $jobRequest3->id,
                'requester_id' => $requester->id,
                'worker_id' => $worker2->id,
                'status' => 'accepted',
                'order_number' => 'ORD-' . Str::upper(Str::random(8)),
                'created_at' => Carbon::now()->subDays(26),
                'updated_at' => Carbon::now()->subDays(25),
            ],
            [
                'request_id' => $jobRequest3->id,
                'requester_id' => $requester->id,
                'worker_id' => $worker2->id,
                'status' => 'completed',
                'start_work' => Carbon::now()->subDays(25)->addHours(8),
                'finish_work' => Carbon::now()->subDays(25)->addHours(11),
                'order_number' => 'ORD-' . Str::upper(Str::random(8)),
                'created_at' => Carbon::now()->subDays(27),
                'updated_at' => Carbon::now()->subDays(23), // Completed in the previous month
            ],

            // Transactions where requester2 (Alice) is the requester, worker (Dummy Worker) is the worker
            [
                'request_id' => $jobRequest4->id,
                'requester_id' => $requester2->id,
                'worker_id' => $worker->id,
                'status' => 'in progress',
                'order_number' => 'ORD-' . Str::upper(Str::random(8)),
                'created_at' => Carbon::now()->subDays(3),
                'updated_at' => Carbon::now()->subDays(2),
            ],
            [
                'request_id' => $jobRequest4->id,
                'requester_id' => $requester2->id,
                'worker_id' => $worker->id,
                'status' => 'submitted',
                'order_number' => 'ORD-' . Str::upper(Str::random(8)),
                'created_at' => Carbon::now()->subDays(1),
                'updated_at' => Carbon::now(),
            ],

            // Transactions demonstrating requester (Hansen) as a worker
            [
                'request_id' => $jobRequest5->id,
                'requester_id' => $requester2->id, // Alice requests
                'worker_id' => $requester->id,     // Hansen works
                'status' => 'accepted',
                'order_number' => 'ORD-' . Str::upper(Str::random(8)),
                'created_at' => Carbon::now()->subDays(32),
                'updated_at' => Carbon::now()->subDays(31),
            ],
            [
                'request_id' => $jobRequest5->id,
                'requester_id' => $requester2->id, // Alice requests
                'worker_id' => $requester->id,     // Hansen works
                'status' => 'completed',
                'start_work' => Carbon::now()->subDays(31)->addHours(9),
                'finish_work' => Carbon::now()->subDays(31)->addHours(14),
                'order_number' => 'ORD-' . Str::upper(Str::random(8)),
                'created_at' => Carbon::now()->subDays(33),
                'updated_at' => Carbon::now()->subDays(29), // Completed in the previous month
            ],

            // Transactions demonstrating worker (Dummy Worker) as a requester
            [
                'request_id' => $jobRequest6->id,
                'requester_id' => $worker->id,     // Dummy Worker requests
                'worker_id' => $worker2->id,     // Bob works
                'status' => 'in progress',
                'order_number' => 'ORD-' . Str::upper(Str::random(8)),
                'created_at' => Carbon::now()->subDays(47),
                'updated_at' => Carbon::now()->subDays(40),
            ],
            [
                'request_id' => $jobRequest6->id,
                'requester_id' => $worker->id,     // Dummy Worker requests
                'worker_id' => $worker2->id,     // Bob works
                'status' => 'submitted',
                'order_number' => 'ORD-' . Str::upper(Str::random(8)),
                'created_at' => Carbon::now()->subDays(46),
                'updated_at' => Carbon::now()->subDays(38),
            ],

            // More varied statuses and combinations
            [
                'request_id' => $jobRequest7->id,
                'requester_id' => $requester->id,
                'worker_id' => $worker2->id,
                'status' => 'accepted',
                'order_number' => 'ORD-' . Str::upper(Str::random(8)),
                'created_at' => Carbon::now()->subDays(7),
                'updated_at' => Carbon::now()->subDays(6),
            ],
            [
                'request_id' => $jobRequest7->id,
                'requester_id' => $requester->id,
                'worker_id' => $worker2->id,
                'status' => 'completed',
                'start_work' => Carbon::now()->subDays(6)->addHours(11),
                'finish_work' => Carbon::now()->subDays(6)->addHours(15),
                'order_number' => 'ORD-' . Str::upper(Str::random(8)),
                'created_at' => Carbon::now()->subDays(11),
                'updated_at' => Carbon::now()->subDays(1),
            ],
            [
                'request_id' => $jobRequest3->id, // Using an existing request
                'requester_id' => $requester2->id, // Alice requests
                'worker_id' => $requester->id,     // Hansen works
                'status' => 'cancelled',
                'order_number' => 'ORD-' . Str::upper(Str::random(8)),
                'created_at' => Carbon::now()->subDays(30),
                'updated_at' => Carbon::now()->subDays(28),
            ],
            [
                'request_id' => $jobRequest6->id, // Using an existing request
                'requester_id' => $requester->id, // Hansen requests
                'worker_id' => $worker->id,       // Dummy Worker works
                'status' => 'completed',
                'start_work' => Carbon::now()->subDays(42)->addHours(13),
                'finish_work' => Carbon::now()->subDays(42)->addHours(16),
                'order_number' => 'ORD-' . Str::upper(Str::random(8)),
                'created_at' => Carbon::now()->subDays(52),
                'updated_at' => Carbon::now()->subDays(48),
            ],
        ];

        foreach ($transactionsData as &$data) {
            Transaction::firstOrCreate(
                ['order_number' => $data['order_number']], // Unique key for firstOrCreate
                $data
            );
        }

        // Create reviews for all completed transactions within the last two months
        $twoMonthsAgo = Carbon::now()->subMonths(2);
        $completedTransactions = Transaction::where('status', 'completed')
            ->where('updated_at', '>=', $twoMonthsAgo)
            ->get();

        foreach ($completedTransactions as $transaction) {
            // Requester reviews the worker
            Review::firstOrCreate([
                'transaction_id' => $transaction->id,
                'reviewer_id' => $transaction->requester_id,
                'reviewee_id' => $transaction->worker_id,
            ], [
                'rating' => rand(3, 5), // Generate a rating between 3 and 5 (mostly positive)
                'comment' => $faker->sentence(10),
            ]);

            // Worker reviews the requester
            Review::firstOrCreate([
                'transaction_id' => $transaction->id,
                'reviewer_id' => $transaction->worker_id,
                'reviewee_id' => $transaction->requester_id,
            ], [
                'rating' => rand(3, 5), // Generate a rating between 3 and 5 (mostly positive)
                'comment' => $faker->sentence(10),
            ]);
        }
    }
}