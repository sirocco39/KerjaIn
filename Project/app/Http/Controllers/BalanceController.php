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

        
        if ($request->is('job-req/*')) {
            $viewPath = 'job-requester.balance';

            
        } elseif ($request->is('job-taker/*')) {
            $viewPath = 'job-taker.balance';

            
        } else {
            
            return redirect()->route('landing')->with('custom_error_alert', 'Halaman tidak ditemukan.');
        }

        $user = User::find(Auth::id());

        
        
        $walletTransactions = $user->walletTransactions()
            ->latest()
            ->paginate(5); 
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
