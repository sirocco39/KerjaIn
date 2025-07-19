<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BalanceController extends Controller
{
    public function index(Request $request)
    {

        $viewPath = '';

        // Cek apakah URL yang diakses mengandung 'job-requester'
        if ($request->is('job-req/*')) {
            $viewPath = 'job-requester.balance';

            // Cek apakah URL yang diakses mengandung 'job-taker'
        } elseif ($request->is('job-taker/*')) {
            $viewPath = 'job-taker.balance';

            // Jika tidak keduanya, arahkan ke halaman lain atau tampilkan error
        } else {
            abort(404, 'Halaman tidak ditemukan');
        }

        $user = User::find(Auth::id());

        // Ambil riwayat transaksi dari user yang sedang login, urutkan dari yang terbaru
        // Gunakan paginate untuk membatasi jumlah data per halaman
        $walletTransactions = $user->walletTransactions()
            ->latest()
            ->paginate(15); // Tampilkan 15 transaksi per halaman
        return view($viewPath, compact('user', 'walletTransactions'));
    }
    public function getCurrentBalance()
    {
        $user = User::find(Auth::id());

        return response()->json([
            'balance' => $user->balance,
            'formatted_balance' => 'Rp' . number_format($user->balance, 0, ',', '.')
        ]);
    }
}
