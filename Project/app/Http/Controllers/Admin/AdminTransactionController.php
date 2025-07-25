<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Request as ServiceRequest; // Menggunakan alias untuk model Request
use App\Models\Payment; // Import model Payment
use App\Models\Transaction; // Import model Transaction
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminTransactionController extends Controller
{
    /**
     * Display a simplified and general financial overview focusing on company profit and user balances.
     */
    public function index()
    {
        // 1. Total Keuntungan Perusahaan dari Service Fee
        // Asumsi service_fee terkumpul saat request statusnya 'closed' di tabel 'requests'
        $totalCompanyProfitFromServiceFee = ServiceRequest::where('status', 'closed')->sum('service_fee');

        // 2. Total Saldo Semua Pengguna
        $totalUserBalance = User::sum('balance');

        // 3. Total Saldo Tertahan (Locked Balance) Semua Pengguna
        // Ini adalah jumlah 'locked_balance' di tabel 'users', yang seharusnya mencerminkan dana di escrow.
        $totalLockedBalance = User::sum('locked_balance');
        // Atau, jika Anda ingin menghitung dari `payments` yang sedang `holding`:
        // $totalHoldingPayments = Payment::where('status', 'holding')->sum('amount');
        // Keduanya bisa valid tergantung bagaimana logikanya di sisi aplikasi.
        // Untuk saat ini, kita akan pakai `users.locked_balance` karena itu ada di skema user.

        // 4. Tabel Request yang Masuk dan Menambah Service Fee
        // Kita akan menampilkan request yang statusnya 'closed' (karena service_fee didapat saat itu)
        // dan juga mungkin beberapa request 'open' terbaru yang berpotensi menghasilkan profit.
        $recentCompletedRequests = ServiceRequest::with(['requester', 'transaction.worker']) // Load requester dan worker melalui transaction
            ->where('status', 'closed')
            ->orderByDesc('updated_at')
            ->limit(10)
            ->get();

        // Variabel lama yang dihapus/tidak relevan lagi:
        // $transactions = WalletTransaction::with('user')->latest()->get(); // Tidak ada model WalletTransaction
        // $latestTransactionAmount = WalletTransaction::latest()->first()->amount ?? 0; // Tidak relevan
        // $transactionsLast7Days = WalletTransaction::where('created_at', '>=', Carbon::now()->subDays(7))->count(); // Tidak relevan

        return view('admin.transactions.index', compact(
            'totalCompanyProfitFromServiceFee',
            'totalUserBalance',
            'totalLockedBalance',
            'recentCompletedRequests'
        ));
    }

    // Metode create, store, show, edit, update, destroy dapat dihapus atau dibiarkan kosong
    // karena Anda meminta tampilan sederhana yang tidak memerlukan fungsionalitas CRUD di sini.
}
