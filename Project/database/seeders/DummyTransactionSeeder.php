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
use App\Models\ChatRoom;
use Illuminate\Database\Console\Seeds\WithoutModelEvents; // Keep this if you use it elsewhere
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
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
            'Perbaikan Pipa Bocor (Open)',
            'Pembersihan Taman (Accepted)',
            'Pemasangan Lampu Gantung (In Progress)',
            'Pengecatan Kamar Tidur (Submitted)',
            'Service AC Rutin (Completed)',
            'Bantuan Pindah Barang (Cancelled)',
            'Software Development Project (Open)', // Added for future open project
            'Desain Grafis Cepat (Open)', // Added for future open project
            'Pembersihan Rumah Mingguan (Open)', // Added for future open project
            'Perbaikan Listrik Darurat (Open)',
            'Pemasangan Wallpaper (Open)',
            'Servis Kendaraan (Open)',
            'Pengiriman Dokumen (Open)',
            'Pembersihan Kantor (Open)',
            'Pemasangan CCTV (Open)',
            'Perakitan Furnitur (Open)',
            'Les Privat Matematika (Open)',
            'Jasa Fotografi Acara (Open)',
            'Perbaikan Saluran Air (Open)',
            'Pemasangan Keramik (Open)',
        ];

        // --- Cleanup existing dummy data to prevent duplicates on re-seeding ---
        // Use DB::transaction for cleanup to ensure atomicity
        DB::transaction(function () use ($dummyEmails, $dummyRequestTitles) {
            // Get IDs of dummy users
            $dummyUserIds = User::whereIn('email', $dummyEmails)->pluck('id');

            // Get IDs of dummy requests
            $dummyRequestIds = JobRequest::whereIn('title', $dummyRequestTitles)->pluck('id');

            // Delete reviews first, as they depend on transactions
            Review::whereIn('transaction_id', Transaction::whereIn('requester_id', $dummyUserIds)
                ->orWhereIn('worker_id', $dummyUserIds)
                ->pluck('id'))
                ->forceDelete();

            // Delete completion proofs, as they depend on transactions
            CompletionProof::whereIn('transaction_id', Transaction::whereIn('requester_id', $dummyUserIds)
                ->orWhereIn('worker_id', $dummyUserIds)
                ->pluck('id'))
                ->forceDelete();

            // Delete wallet transactions linked to dummy users
            WalletTransaction::whereIn('user_id', $dummyUserIds)->forceDelete();

            // Delete payments linked to dummy requests
            Payment::whereIn('request_id', $dummyRequestIds)->forceDelete();

            // Delete transactions created by dummy users
            Transaction::whereIn('requester_id', $dummyUserIds)
                ->orWhereIn('worker_id', $dummyUserIds)
                ->forceDelete();

            // Delete chat rooms linked to dummy requests or users
            ChatRoom::whereIn('request_id', $dummyRequestIds)
                ->orWhereIn('requester_id', $dummyUserIds)
                ->orWhereIn('worker_id', $dummyUserIds)
                ->forceDelete();

            // Delete dummy requests
            JobRequest::whereIn('title', $dummyRequestTitles)->forceDelete();

            // Delete dummy users
            User::whereIn('email', $dummyEmails)->forceDelete();
        });
        // --- End Cleanup ---


        // Create dummy users
        $requester = User::firstOrCreate(
            ['email' => 'hansengunawan64@gmail.com'],
            [
                'first_name' => 'Hansen',
                'last_name' => 'Gunawan',
                'password' => Hash::make('@Super645!'),
                'role' => 'user',
                'phone_number' => '081234567890',
                'balance' => 1500000.00, // Increased initial balance
                'locked_balance' => 0.00,
                'is_worker' => true, // Hansen can also be a worker
                'rating' => 4.8,
                'job_done' => 10,
                'bank_acc_num' => '1234567890',
                'google_id' => null,
                'is_blocked' => false,
                'created_at' => Carbon::now()->subMonth(),
                'updated_at' => Carbon::now()->subMonth(),
            ]
        );

        $worker = User::firstOrCreate(
            ['email' => 'worker@example.com'],
            [
                'first_name' => 'Dummy',
                'last_name' => 'Worker',
                'password' => Hash::make('password'),
                'role' => 'user',
                'phone_number' => '089876543210',
                'balance' => 500000.00,
                'locked_balance' => 0.00,
                'is_worker' => true,
                'rating' => 4.5,
                'job_done' => 5,
                'bank_acc_num' => '1234567890',
                'google_id' => null,
                'is_blocked' => false,
                'created_at' => Carbon::now()->subMonth()->addDays(3),
                'updated_at' => Carbon::now()->subMonth()->addDays(3),
            ]
        );

        $requester2 = User::firstOrCreate(
            ['email' => 'requester2@example.com'],
            [
                'first_name' => 'Alice',
                'last_name' => 'Smith',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'phone_number' => '087654321098',
                'balance' => 200000.00, // Increased initial balance
                'locked_balance' => 0.00,
                'is_worker' => false, // Alice is primarily a requester
                'rating' => 4.2,
                'job_done' => 0,
                'bank_acc_num' => null,
                'google_id' => null,
                'is_blocked' => false,
                'created_at' => Carbon::now()->subMonth()->addDays(5),
                'updated_at' => Carbon::now()->subMonth()->addDays(5),
            ]
        );

        $worker2 = User::firstOrCreate(
            ['email' => 'worker2@example.com'],
            [
                'first_name' => 'Bob',
                'last_name' => 'Johnson',
                'password' => Hash::make('securepass'),
                'role' => 'user',
                'phone_number' => '081122334455',
                'balance' => 150000.00,
                'locked_balance' => 0.00,
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


        // Helper function to create a request and handle initial payment/escrow
        $createJobRequest = function ($title, $description, $price, $location, $requesterUser, $status, $start_time, $end_time) use ($faker) {
            $jobRequest = JobRequest::create([
                'title' => $title,
                'slug' => Str::slug($title . '-' . Str::random(5)),
                'description' => $description,
                'price' => $price,
                'final_price' => $price,
                'location' => $location,
                'requester_id' => $requesterUser->id,
                'status' => $status,
                'start_time' => $start_time,
                'end_time' => $end_time,
                'created_at' => $start_time->subHours(rand(1, 24)), // Request created slightly before start time
                'updated_at' => $start_time->subHours(rand(1, 24)),
            ]);

            // Handle initial payment (escrow)
            Payment::create([
                'request_id' => $jobRequest->id,
                'amount' => $price,
                'status' => 'holding',
            ]);
            $requesterUser->decrement('balance', $price);
            $requesterUser->increment('locked_balance', $price);
            WalletTransaction::create([
                'user_id' => $requesterUser->id,
                'amount' => $price,
                'type' => 'credit', // 'credit' for locked balance, 'debit' for active balance
                'description' => 'Penahanan saldo untuk pekerjaan: ' . $jobRequest->title,
            ]);

            return $jobRequest;
        };

        // --- Scenario 1: Open Requests (Future Date/Time - discoverable by workers) ---
        $jobRequest1 = $createJobRequest(
            'Perbaikan Pipa Bocor (Open)',
            'Pipa air di dapur bocor parah, butuh perbaikan segera.',
            75000.00,
            'Jl. Merdeka No. 10, Jakarta',
            $requester,
            'open',
            Carbon::now()->addDays(5)->setTime(9, 0),
            Carbon::now()->addDays(5)->setTime(17, 0)
        );

        $jobRequest2 = $createJobRequest(
            'Pembersihan Rumah Mingguan (Open)',
            'Pembersihan menyeluruh rumah 3 kamar tidur, termasuk kamar mandi dan dapur.',
            180000.00,
            'Komplek Griya Asri, Tangerang',
            $requester2,
            'open',
            Carbon::now()->addDays(2)->setTime(10, 0),
            Carbon::now()->addDays(2)->setTime(14, 0)
        );

        $jobRequest3 = $createJobRequest(
            'Perbaikan Listrik Darurat (Open)',
            'Listrik padam di sebagian rumah, butuh teknisi listrik segera.',
            250000.00,
            'Jl. Pahlawan No. 22, Bekasi',
            $requester,
            'open',
            Carbon::now()->addHours(3)->setTime(Carbon::now()->addHours(3)->hour, 0), // Today, 3 hours from now
            Carbon::now()->addHours(6)->setTime(Carbon::now()->addHours(6)->hour, 0)
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
                'start_work' => Carbon::now()->subHours(12)->setTime(9,0), // Example: started today, submitted today
                'finish_work' => Carbon::now()->subHours(1)->setTime(16,0),
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
                'start_work' => Carbon::now()->subDays(4)->setTime(9, 0),
                'finish_work' => Carbon::now()->subDays(1)->setTime(17, 0),
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
                'worker_id' => $worker->id,        // Dummy Worker works
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
