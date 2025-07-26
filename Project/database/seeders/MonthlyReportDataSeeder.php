<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Review;
use App\Models\Transaction;
use Illuminate\Support\Str;
use App\Models\CompletionProof;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Request as JobRequest; // Renamed to avoid conflict with Illuminate\Http\Request

class MonthlyReportDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Clear existing data to ensure a fresh start
        // CompletionProof::query()->delete();
        // Review::query()->delete();
        // Transaction::query()->delete();
        // JobRequest::query()->delete();
        // User::query()->delete();

        // 1. Create Users
        $workerUser = User::create([
            'first_name' => 'Budi',
            'last_name' => 'Pekerja',
            'email' => 'budi.pekerja@example.com',
            'password' => Hash::make('password'),
            'phone_number' => '081234567890',
            'role' => 'user',
            'is_worker' => true,
            'balance' => 500000.00,
            'rating' => 4.5, // Initial rating
            'job_done' => 10, // Initial jobs done
            'bank_acc_num' => '1234567890',
            'google_id' => null,
            'is_blocked' => false,
        ]);

        $requesterUser = User::create([
            'first_name' => 'Ani',
            'last_name' => 'Peminta',
            'email' => 'ani.peminta@example.com',
            'password' => Hash::make('password'),
            'phone_number' => '087654321098',
            'role' => 'user',
            'is_worker' => false,
            'balance' => 1000000.00,
            'rating' => 0,
            'job_done' => 0,
            'bank_acc_num' => null,
            'google_id' => null,
            'is_blocked' => false,
        ]);

        $anotherRequesterUser = User::create([
            'first_name' => 'Cici',
            'last_name' => 'Klien',
            'email' => 'cici.klien@example.com',
            'password' => Hash::make('password'),
            'phone_number' => '081122334455',
            'role' => 'user',
            'is_worker' => false,
            'balance' => 750000.00,
            'rating' => 0,
            'job_done' => 0,
            'bank_acc_num' => null,
            'google_id' => null,
            'is_blocked' => false,
        ]);

        // 2. Create Requests (Jobs)
        $request1 = JobRequest::create([
            'slug' => Str::slug('Bersihkan Rumah Saya'),
            'requester_id' => $requesterUser->id,
            'title' => 'Bersihkan Rumah Saya',
            'description' => 'Membutuhkan bantuan untuk membersihkan seluruh rumah, termasuk menyapu, mengepel, membersihkan kamar mandi, dan dapur.',
            'price' => 150000.00,
            'final_price' => 150000.00, // Assuming no negotiation
            'location' => 'Jalan Merdeka No. 10, Jakarta Pusat',
            'status' => 'closed', // This will be completed
            'start_time' => Carbon::now()->subDays(5)->startOfDay(),
            'end_time' => Carbon::now()->subDays(5)->endOfDay(),
        ]);

        $request2 = JobRequest::create([
            'slug' => Str::slug('Perbaiki Keran Bocor'),
            'requester_id' => $anotherRequesterUser->id,
            'title' => 'Perbaiki Keran Bocor',
            'description' => 'Ada keran bocor di dapur, perlu diperbaiki segera.',
            'price' => 75000.00,
            'final_price' => 150000.00, // Assuming no negotiation

            'location' => 'Jalan Sudirman No. 25, Bandung',
            'status' => 'closed', // This will be completed
            'start_time' => Carbon::now()->subDays(10)->startOfDay(),
            'end_time' => Carbon::now()->subDays(10)->endOfDay(),
        ]);

        $request3 = JobRequest::create([
            'slug' => Str::slug('Pijat Relaksasi'),
            'requester_id' => $requesterUser->id,
            'title' => 'Pijat Relaksasi',
            'description' => 'Membutuhkan jasa pijat relaksasi selama 2 jam di rumah.',
            'price' => 200000.00,
            'final_price' => 150000.00, // Assuming no negotiation

            'location' => 'Jalan Gatot Subroto No. 5, Surabaya',
            'status' => 'open', // Example of an open request
            'start_time' => Carbon::now()->addDays(2)->startOfDay(),
            'end_time' => Carbon::now()->addDays(2)->endOfDay(),
        ]);


        // 3. Create Transactions for completed jobs
        // Transaction for request1 (completed by workerUser)
        $transaction1 = Transaction::create([
            'request_id' => $request1->id,
            'worker_id' => $workerUser->id,
            'requester_id' => $requesterUser->id,
            'status' => 'completed',
            'order_number' => 'TRX-' . Str::upper(Str::random(8)),
            'start_work' => Carbon::now()->subDays(5)->addHours(8),
            'finish_work' => Carbon::now()->subDays(5)->addHours(12),
        ]);

        // Transaction for request2 (completed by workerUser)
        $transaction2 = Transaction::create([
            'request_id' => $request2->id,
            'worker_id' => $workerUser->id,
            'requester_id' => $anotherRequesterUser->id,
            'status' => 'completed',
            'order_number' => 'TRX-' . Str::upper(Str::random(8)),
            'start_work' => Carbon::now()->subDays(10)->addHours(9),
            'finish_work' => Carbon::now()->subDays(10)->addHours(10),
        ]);

        // Transaction for an in-progress job (workerUser took it)
        $transaction3 = Transaction::create([
            'request_id' => $request3->id,
            'worker_id' => $workerUser->id,
            'requester_id' => $requesterUser->id,
            'status' => 'in progress',
            'order_number' => 'TRX-' . Str::upper(Str::random(8)),
            'start_work' => Carbon::now()->addDays(2)->addHours(10),
            'finish_work' => null, // Still in progress
        ]);

        // 4. Create Reviews for completed transactions
        Review::create([
            'transaction_id' => $transaction1->id,
            'reviewer_id' => $requesterUser->id, // Requester reviews worker
            'reviewee_id' => $workerUser->id,
            'rating' => 5,
            'comment' => 'Pekerjaan sangat baik dan rapi. Sangat puas!',
        ]);

        Review::create([
            'transaction_id' => $transaction2->id,
            'reviewer_id' => $anotherRequesterUser->id, // Another requester reviews worker
            'reviewee_id' => $workerUser->id,
            'rating' => 4,
            'comment' => 'Perbaikan cepat dan efektif, namun sedikit terlambat datang.',
        ]);

        // 5. Create Completion Proofs for completed transactions
        CompletionProof::create([
            'transaction_id' => $transaction1->id,
            'photo_url' => 'https://picsum.photos/id/' . rand(1, 100) . '/600/400',
            'note' => 'Rumah sudah bersih total.',
            'submitted_at' => Carbon::now()->subDays(5)->addHours(13),
        ]);

        CompletionProof::create([
            'transaction_id' => $transaction2->id,
            'photo_url' => 'https://picsum.photos/id/' . rand(1, 100) . '/600/400',
            'note' => 'Keran sudah tidak bocor lagi.',
            'submitted_at' => Carbon::now()->subDays(10)->addHours(11),
        ]);

        // Update worker's rating and job_done based on seeded data
        $workerUser->update([
            'rating' => ($workerUser->rating * $workerUser->job_done + 5 + 4) / ($workerUser->job_done + 2), // Update based on new reviews
            'job_done' => $workerUser->job_done + 2, // 2 new completed jobs
        ]);

        $this->command->info('Dummy database seeded successfully for worker login view!');
    }
}
