<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Import Auth facade

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get the authenticated user's ID
        $userId = Auth::id();

        $transactions = Transaction::withTrashed() // ADDED: This will include soft-deleted records
                                   ->with(['request', 'requester', 'worker'])
                                   ->where(function ($query) use ($userId) {
                                       $query->where('requester_id', $userId)
                                             ->orWhere('worker_id', $userId);
                                   })
                                   ->orderBy('created_at', 'desc') // Order by creation date
                                   ->get();

        // Prepare data for different tabs based on your string statuses
        $allOrders = $transactions;
        $pendingOrders = $transactions->filter(function ($transaction) {
            return in_array($transaction->status, ['accepted', 'in progress', 'submitted']);
        });
        $completedOrders = $transactions->filter(function ($transaction) {
            return $transaction->status === 'completed';
        });
        $cancelledOrders = $transactions->filter(function ($transaction) {
            return $transaction->status === 'cancelled';
        });

        // Render the specified Blade view
        return view('Job_Requester.dummy-job_req-riwayat', compact('allOrders', 'pendingOrders', 'completedOrders', 'cancelledOrders'));
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
        //get the request by slug
        $workRequest = Transaction::where('id', $id)->with('requester', 'request')->firstOrFail();
        // If the request is not found, it will throw a 404 error
        // Return the view with the request data
        if (!$workRequest || $workRequest->deleted_at) {
            abort(404, 'Request not found or has been deleted.');
        }

        return response()->json($workRequest);
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
