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
                'password' => bcrypt('password'), // A common test password for admin
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

            // Delete reports linked to dummy transactions or users
            Report::whereIn('transaction_id', Transaction::whereIn('requester_id', $dummyUserIds)
                ->orWhereIn('worker_id', $dummyUserIds)
                ->pluck('id'))
                ->orWhereIn('reporter_id', $dummyUserIds)
                ->orWhereIn('reported_id', $dummyUserIds)
                ->forceDelete();

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
                'password' => Hash::make('password!123'),
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
                'password' => Hash::make('password123!'),
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

        $jobRequest4 = $createJobRequest(
            'Pemasangan Wallpaper (Open)',
            'Pasang wallpaper di dinding ruang tamu, ukuran 4x3 meter.',
            100000.00,
            'Jl. Melati Indah No. 7, Bogor',
            $requester2,
            'open',
            Carbon::now()->addDays(1)->setTime(13, 0),
            Carbon::now()->addDays(1)->setTime(16, 0)
        );


        // --- Scenario 2: Accepted Transaction (Future Date/Time, already accepted by a worker) ---
        $acceptedStartDate = Carbon::now()->addDays(3)->setTime(10, 0);
        $acceptedEndDate = Carbon::now()->addDays(3)->setTime(14, 0);
        $jobRequestAccepted = $createJobRequest(
            'Pembersihan Taman (Accepted)',
            'Membersihkan taman belakang rumah, termasuk memotong rumput dan merapikan semak.',
            120000.00,
            'Jl. Damai Indah No. 5, Bandung',
            $requester,
            'closed', // Request is closed once accepted
            $acceptedStartDate,
            $acceptedEndDate
        );
        Transaction::create([
            'order_number' => 'ORD-' . Str::upper(Str::random(8)),
            'request_id' => $jobRequestAccepted->id,
            'requester_id' => $requester->id,
            'worker_id' => $worker->id,
            'status' => 'accepted',
            'accepted_at' => Carbon::now()->subHours(12), // Accepted some time ago
            'created_at' => Carbon::now()->subHours(13),
            'updated_at' => Carbon::now()->subHours(12),
        ]);
        ChatRoom::firstOrCreate(
            ['request_id' => $jobRequestAccepted->id, 'worker_id' => $worker->id],
            ['requester_id' => $requester->id, 'is_open' => true]
        );


        // --- Scenario 3: In Progress Transaction (Currently ongoing or recently started) ---
        $inProgressStartDate = Carbon::now()->subHours(rand(1, 3))->setTime(Carbon::now()->subHours(rand(1, 3))->hour, 0);
        $inProgressEndDate = Carbon::now()->addHours(rand(1, 3))->setTime(Carbon::now()->addHours(rand(1, 3))->hour, 0);
        $jobRequestInProgress = $createJobRequest(
            'Pemasangan Lampu Gantung (In Progress)',
            'Pasang 3 lampu gantung di ruang tamu dan ruang makan.',
            90000.00,
            'Apartemen Sentul City, Bogor',
            $requester,
            'closed',
            $inProgressStartDate,
            $inProgressEndDate
        );
        Transaction::create([
            'order_number' => 'ORD-' . Str::upper(Str::random(8)),
            'request_id' => $jobRequestInProgress->id,
            'requester_id' => $requester->id,
            'worker_id' => $worker->id,
            'status' => 'in progress',
            'accepted_at' => Carbon::now()->subDay(),
            'start_work' => Carbon::now()->subHours(rand(1, 2)), // Started recently
            'created_at' => Carbon::now()->subDay()->subHour(),
            'updated_at' => Carbon::now()->subHours(rand(1, 2)),
        ]);
        ChatRoom::firstOrCreate(
            ['request_id' => $jobRequestInProgress->id, 'worker_id' => $worker->id],
            ['requester_id' => $requester->id, 'is_open' => true]
        );


        // --- Scenario 4: Submitted Transaction (Awaiting Requester Approval) ---
        $submittedStartDate = Carbon::now()->subDays(2)->setTime(8, 0);
        $submittedEndDate = Carbon::now()->subDays(2)->setTime(12, 0);
        $jobRequestSubmitted = $createJobRequest(
            'Pengecatan Kamar Tidur (Submitted)',
            'Cat ulang kamar tidur utama dengan warna biru muda.',
            150000.00,
            'Rumah Cluster Griya Asri, Depok',
            $requester2,
            'closed',
            $submittedStartDate,
            $submittedEndDate
        );
        $transactionSubmitted = Transaction::create([
            'order_number' => 'ORD-' . Str::upper(Str::random(8)),
            'request_id' => $jobRequestSubmitted->id,
            'requester_id' => $requester2->id,
            'worker_id' => $worker2->id,
            'status' => 'submitted',
            'accepted_at' => Carbon::now()->subDays(3),
            'start_work' => $submittedStartDate->addMinutes(30),
            'finish_work' => $submittedEndDate->subMinutes(30),
            'created_at' => Carbon::now()->subDays(3)->subHour(),
            'updated_at' => Carbon::now()->subDays(2), // Submitted on this date
        ]);
        CompletionProof::create([
            'transaction_id' => $transactionSubmitted->id,
            'photo_url' => json_encode(['https://placehold.co/600x400/FF0000/FFFFFF?text=Proof1']),
            'note' => 'Pengecatan selesai sesuai permintaan.',
            'submitted_at' => Carbon::now()->subDays(2)->addHours(1),
        ]);
        ChatRoom::firstOrCreate(
            ['request_id' => $jobRequestSubmitted->id, 'worker_id' => $worker2->id],
            ['requester_id' => $requester2->id, 'is_open' => true]
        );
        // Add a dummy report for a submitted transaction to simulate the original error context
        // FIX: Ensure 'photo_url' is JSON encoded as an array of strings
        Report::create([
            'status' => 'Not Reviewed',
            'reasons' => 'Iure velit sunt nihil.',
            'photo_url' => json_encode(['https://via.placeholder.com/640x480.png/0044aa?text=laborum']), // <-- FIXED LINE
            'transaction_id' => $transactionSubmitted->id, // Use actual transaction ID
            'reporter_id' => $requester->id, // A dummy reporter
            'reported_id' => $worker->id, // A dummy reported user
            'created_at' => Carbon::now()->subHours(2),
            'updated_at' => Carbon::now()->subHours(2),
        ]);


        // --- Scenario 5: Completed Transaction (Past Date, funds released, reviewed) ---
        $completedStartDate = Carbon::now()->subDays(7)->setTime(9, 0);
        $completedEndDate = Carbon::now()->subDays(7)->setTime(11, 0);
        $jobRequestCompleted = $createJobRequest(
            'Service AC Rutin (Completed)',
            'Service 2 unit AC di ruang tamu dan kamar tidur.',
            100000.00,
            'Perumahan Grand Residence, Bekasi',
            $requester,
            'closed',
            $completedStartDate,
            $completedEndDate
        );
        // Simulate funds release for completed job
        $requester->increment('balance', $jobRequestCompleted->price); // Add back for initial deduction
        $requester->decrement('locked_balance', $jobRequestCompleted->price);
        $worker->increment('balance', $jobRequestCompleted->price);
        $jobRequestCompleted->payment->update(['status' => 'released_to_worker']);
        WalletTransaction::create([
            'user_id' => $requester->id,
            'amount' => $jobRequestCompleted->price,
            'type' => 'debit',
            'description' => 'Pelepasan saldo untuk: ' . $jobRequestCompleted->title,
        ]);
        WalletTransaction::create([
            'user_id' => $worker->id,
            'amount' => $jobRequestCompleted->price,
            'type' => 'credit',
            'description' => 'Penerimaan pembayaran dari: ' . $jobRequestCompleted->title,
        ]);

        $transactionCompleted = Transaction::create([
            'order_number' => 'ORD-' . Str::upper(Str::random(8)),
            'request_id' => $jobRequestCompleted->id,
            'requester_id' => $requester->id,
            'worker_id' => $worker->id,
            'status' => 'completed',
            'accepted_at' => Carbon::now()->subDays(8),
            'start_work' => $completedStartDate->addMinutes(10),
            'finish_work' => $completedEndDate->subMinutes(10),
            'created_at' => Carbon::now()->subDays(8)->subHour(),
            'updated_at' => Carbon::now()->subDays(7)->addHours(2), // Marked completed on this date
        ]);
        CompletionProof::create([
            'transaction_id' => $transactionCompleted->id,
            'photo_url' => json_encode(['https://placehold.co/600x400/00FF00/FFFFFF?text=Proof2']),
            'note' => 'Service AC selesai, unit bersih dan dingin.',
            'submitted_at' => Carbon::now()->subDays(7)->addHours(1),
        ]);
        Review::create([
            'transaction_id' => $transactionCompleted->id,
            'reviewer_id' => $requester->id,
            'reviewee_id' => $worker->id,
            'rating' => 5,
            'comment' => 'Pekerjaan sangat memuaskan, rapi dan cepat!',
        ]);
        Review::create([
            'transaction_id' => $transactionCompleted->id,
            'reviewer_id' => $worker->id,
            'reviewee_id' => $requester->id,
            'rating' => 4,
            'comment' => 'Klien ramah dan responsif.',
        ]);
        ChatRoom::firstOrCreate(
            ['request_id' => $jobRequestCompleted->id, 'worker_id' => $worker->id],
            ['requester_id' => $requester->id, 'is_open' => false] // Chat closed after completion
        );


        // --- Scenario 6: Cancelled Transaction (Requester Cancelled, funds refunded) ---
        $cancelledStartDate = Carbon::now()->addDays(1)->setTime(13, 0);
        $cancelledEndDate = Carbon::now()->addDays(1)->setTime(15, 0);
        $jobRequestCancelled = $createJobRequest(
            'Bantuan Pindah Barang (Cancelled)',
            'Butuh bantuan memindahkan beberapa kotak dari lantai 2 ke lantai 1.',
            60000.00,
            'Rumah Pondok Indah, Jakarta',
            $requester,
            'closed', // Request is closed once cancelled
            $cancelledStartDate,
            $cancelledEndDate
        );
        // Simulate funds refund for cancelled job
        $requester->increment('balance', $jobRequestCancelled->price); // Funds returned to active balance
        $requester->decrement('locked_balance', $jobRequestCancelled->price);
        $jobRequestCancelled->payment->update(['status' => 'refunded_to_requester']);
        WalletTransaction::create([
            'user_id' => $requester->id,
            'amount' => $jobRequestCancelled->price,
            'type' => 'debit',
            'description' => 'Pengembalian saldo dari pembatalan pekerjaan: ' . $jobRequestCancelled->title,
        ]);
        Transaction::create([
            'order_number' => 'ORD-' . Str::upper(Str::random(8)),
            'request_id' => $jobRequestCancelled->id,
            'requester_id' => $requester->id,
            'worker_id' => $worker->id,
            'status' => 'cancelled',
            'accepted_at' => Carbon::now()->subDay(),
            'created_at' => Carbon::now()->subDay()->subHour(),
            'updated_at' => Carbon::now()->subDay()->addHours(2), // Cancelled on this date
        ]);
        // No ChatRoom created or it would be closed if it existed.

        // --- Additional Open Requests for more browsing options ---
        $createJobRequest(
            'Software Development Project (Open)',
            'Pengembangan aplikasi web kustom untuk manajemen proyek internal.',
            500000.00,
            'Online (Remote)',
            $requester2,
            'open',
            Carbon::now()->addDays(10)->setTime(9, 0),
            Carbon::now()->addDays(20)->setTime(17, 0)
        );

        $createJobRequest(
            'Desain Grafis Cepat (Open)',
            'Desain logo dan kartu nama untuk startup baru.',
            80000.00,
            'Jakarta Pusat',
            $requester,
            'open',
            Carbon::now()->addDays(1)->setTime(14, 0),
            Carbon::now()->addDays(1)->setTime(18, 0)
        );

        $createJobRequest(
            'Servis Kendaraan (Open)',
            'Servis rutin mobil sedan di rumah, ganti oli dan cek rem.',
            175000.00,
            'Perumahan Citra Indah, Surabaya',
            $requester2,
            'open',
            Carbon::now()->addDays(4)->setTime(8, 30),
            Carbon::now()->addDays(4)->setTime(11, 0)
        );

        $createJobRequest(
            'Pengiriman Dokumen (Open)',
            'Ambil dokumen dari kantor A dan antar ke kantor B secepatnya.',
            30000.00,
            'Area Sudirman, Jakarta',
            $requester,
            'open',
            Carbon::now()->addHours(1)->setTime(Carbon::now()->addHours(1)->hour, 30), // Today, soon
            Carbon::now()->addHours(2)->setTime(Carbon::now()->addHours(2)->hour, 0)
        );

        // Update user balances in DB after all operations
        $requester->save();
        $worker->save();
        $requester2->save();
        $worker2->save();
    }
}
