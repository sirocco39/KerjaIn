<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Request as RequestModel;
use App\Models\WalletTransaction;
use App\Http\Controllers\Controller;

class AdminTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch all wallet transactions, ordered by creation date descending.
        // Eager load the 'user' relationship to avoid N+1 query problems.
        $transactions = WalletTransaction::with('user')->latest()->get();

        // Calculate aggregated data for the dashboard cards
        $totalUserBalance = User::sum('balance');
        $totalLockedBalance = User::sum('locked_balance');
        $latestTransactionAmount = WalletTransaction::latest()->first()->amount ?? 0;
        $transactionsLast7Days = WalletTransaction::where('created_at', '>=', Carbon::now()->subDays(7))->count();

        $totalServiceFeeProfit = RequestModel::sum('service_fee');


        return view('admin.transactions.index', compact(
            'transactions',
            'totalUserBalance',
            'totalLockedBalance',
            'latestTransactionAmount',
            'transactionsLast7Days',
            'totalServiceFeeProfit'
        ));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
