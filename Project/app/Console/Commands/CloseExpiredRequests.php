<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Models\Request as JobRequest; 
use App\Models\WalletTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB; 
use Throwable; 
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
        $this->info('Mulai mencari pekerjaan yang kedaluwarsa...');
        $processedCount = 0;
        JobRequest::where('status', 'open')
            ->where('start_time', '<', Carbon::now())
            ->chunkById(100, function ($expiredRequests) use (&$processedCount) {
                foreach ($expiredRequests as $request) {
                    DB::beginTransaction();
                    try {
                        if (!$request->payment || !$request->requester) {
                            $this->error("Pekerjaan #{$request->id} dilewati: data payment atau requester tidak lengkap.");
                            DB::rollBack(); 
                            continue; 
                        }
                        $request->status = 'closed';
                        $request->save();
                        $payment = $request->payment;
                        $refundAmount = $payment->amount;
                        $payment->status = 'refunded_to_requester';
                        $payment->save();
                        $user = $request->requester;
                        $user->balance += $refundAmount;
                        $user->locked_balance -= $refundAmount;
                        $user->save();
                        WalletTransaction::create([
                            'user_id' => $user->id,
                            'amount' => $refundAmount,
                            'type' => 'debit', 
                            'description_id' => 'Pengembalian dana untuk pekerjaan kedaluwarsa: ' . $request->title,
                            'description_en' => 'Balance refund for expired job: ' . $request->title,
                        ]);
                        activity()
                           ->inLog('System')
                           ->on($request)
                           ->log("Pekerjaan '{$request->title}' (#{$request->id}) telah ditutup otomatis oleh sistem karena waktu mulai telah lewat.");
                        activity()
                           ->inLog('Finance')
                           ->on($user)
                           ->log("Dana sebesar Rp" . number_format($refundAmount) . " telah dikembalikan ke saldo {$user->first_name} karena pekerjaan '{$request->title}' (#{$request->id}) kedaluwarsa.");
                        DB::commit(); 
                        $this->info("Pekerjaan #{$request->id} ('{$request->title}') berhasil ditutup {$request->start_time} - {Carbon::now}.");
                        $processedCount++;
                    } catch (Throwable $e) {
                        DB::rollBack(); 
                        $this->error("Gagal memproses pekerjaan #{$request->id}: " . $e->getMessage());
                    }
                }
            });
        if ($processedCount > 0) {
            $this->info("Selesai. Total {$processedCount} pekerjaan kedaluwarsa berhasil diproses.");
        } else {
            $this->info('Tidak ada pekerjaan kedaluwarsa yang ditemukan.');
        }
    }
}
