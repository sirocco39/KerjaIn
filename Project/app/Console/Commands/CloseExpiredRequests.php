<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Request as JobRequest; // Gunakan alias untuk menghindari konflik
use Carbon\Carbon;

class CloseExpiredRequests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:close-expired-requests';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mencari dan menutup permintaan pekerjaan yang sudah melewati waktu mulai';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // 1. Ambil semua request 'open' yang waktu mulainya sudah lewat.
        $expiredRequests = JobRequest::where('status', 'open')
                                  ->where('start_time', '<', Carbon::now())
                                  ->get();

        if ($expiredRequests->isEmpty()) {
            $this->info('Tidak ada pekerjaan kedaluwarsa yang ditemukan.');
            return;
        }

        $this->info("Menemukan {$expiredRequests->count()} pekerjaan yang kedaluwarsa...");

        foreach ($expiredRequests as $request) {
            // 2. Ubah statusnya menjadi 'closed'.
            $request->status = 'closed';
            $request->save();

            // 3. LOGGING MANUAL untuk aksi sistem
            activity()
                ->inLog('System') // Kelompokkan ke log 'System'
                ->on($request)    // Targetnya adalah request yang diubah
                // Kita tidak menggunakan causedBy() karena pelakunya adalah sistem
                ->log("Pekerjaan '{$request->title}' (#{$request->id}) telah ditutup otomatis oleh sistem karena waktu mulai telah lewat.");
            
            $this->info("Pekerjaan #{$request->id} ('{$request->title}') telah ditutup.");
        }

        $this->info('Semua pekerjaan kedaluwarsa berhasil diperbarui.');
    }
}