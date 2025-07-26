<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Request as ServiceRequest;
use App\Models\Payment;
use App\Models\Transaction;
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
        $totalCompanyProfitFromServiceFee = ServiceRequest::where('status', 'closed')->sum('service_fee');

        // 2. Total Saldo Semua Pengguna
        $totalUserBalance = User::sum('balance');

        // 3. Total Saldo Tertahan (Locked Balance) Semua Pengguna
        $totalLockedBalance = User::sum('locked_balance');

        // 4. Tabel Request yang Masuk dan Menambah Service Fee
        $Requests = ServiceRequest::with(['requester', 'transaction.worker'])
            ->withTrashed()
            ->orderByDesc('created_at')
            ->paginate(10);

        // Data Breadcrumbs
        $breadcrumbs = [
            'mainPageTitle' => 'Admin',
            'currentPageTitle' => 'Penghasilan',
        ];

        return view('admin.transactions.index', compact(
            'totalCompanyProfitFromServiceFee',
            'totalUserBalance',
            'totalLockedBalance',
            'Requests',
            'breadcrumbs' // Tambahkan breadcrumbs
        ));
    }
}
