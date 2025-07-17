<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Transaction;
use App\Models\Request as JobRequest; // Alias Request to JobRequest to avoid conflict with Illuminate\Http\Request
use Illuminate\Support\Carbon;
use Illuminate\Http\Request as HttpRequest; // Alias Request to HttpRequest
// use App\Models\Request; // This might be redundant if using JobRequest alias, consider removing if not needed for other methods
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class TransactionController extends Controller
{
    public function index()
    {
        // Get the authenticated user's ID
        $userId = Auth::id();

        // Fetch all orders where the authenticated user is the REQUIESTER
        // Remove the 'orWhere('worker_id', $userId)' condition
        $transactions = Transaction::withTrashed()
            ->with(['request', 'requester', 'worker'])
            ->where('requester_id', $userId) // THIS IS THE KEY CHANGE: Filter by requester_id only
            ->orderBy('created_at', 'desc')
            ->get();

        // Prepare data for different tabs based on your string statuses
        // The Blade view is already using 'status_text' which is derived from 'status'
        $allOrders = $transactions;

        $pendingOrders = $transactions->filter(function ($transaction) {
            // These statuses map to 'Diterima', 'Dikerjain', 'Ditinjau' in your Blade logic
            return in_array($transaction->status, ['accepted', 'in progress', 'submitted']);
        });

        $completedOrders = $transactions->filter(function ($transaction) {
            return $transaction->status === 'completed';
        });

        $cancelledOrders = $transactions->filter(function ($transaction) {
            return $transaction->status === 'cancelled';
        });

        // Render the specified Blade view
        return view('Job_Requester.riwayat', compact('allOrders', 'pendingOrders', 'completedOrders', 'cancelledOrders'));
    }

    // ... (rest of your controller methods remain unchanged) ...

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

    public function showOngoing($transactionId)
    {
        // Ambil data transaction berdasarkan ID
        $transaction = Transaction::findOrFail($transactionId);

        // Ambil data request yang berhubungan dengan transaction
        $request = JobRequest::findOrFail($transaction->request_id);

        // Ambil pekerja yang melakukan pekerjaan berdasarkan relasi
        $worker = $transaction->worker; // Pastikan relasi sudah ada di model Transaction
        $room = \App\Models\ChatRoom::where('request_id', $request->id)
            ->where('worker_id', $worker->id)
            ->first();

        // Ambil completion proof terkait
        $completionProof = $transaction->completionProof;

        // Kirim data ke view

        return view('Job_Requester.on-going-work-request', compact('transaction', 'request', 'worker', 'completionProof', 'room'));
    }


    public function cancel($id)
    {
        // Cari transaction berdasarkan id
        $transaction = Transaction::findOrFail($id);

        // Ubah status transaction menjadi cancelled
        $transaction->status = 'cancelled';
        $transaction->save(); // Pastikan status transaction tersimpan

        // If the request status should also be updated when cancelled by requester
        // Assuming there's a status on the Request model too
        if ($transaction->request) {
            $transaction->request->status = 'cancelled'; // Or 'closed' if you prefer
            $transaction->request->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Pekerjaan dibatalkan.',
            'redirect_url' => route('orders.index') // Redirect back to history or specific page
        ]);
    }

    public function markComplete(Transaction $transaction)
    {
        if (in_array($transaction->status, ['in progress', 'submitted'])) {
            $transaction->status = 'completed';
            $transaction->save();

            // Update the associated request status if needed
            if ($transaction->request) {
                $transaction->request->status = 'completed'; // Or 'closed'
                $transaction->request->save();
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Pekerjaan berhasil ditandai selesai!'
        ]);
    }

    public function submitReport(HttpRequest $request)
    {
        $request->validate([
            'transaction_id' => 'required|exists:transactions,id',
            'reporter_id' => 'required|exists:users,id',
            'reported_id' => 'required|exists:users,id',
            'reasons' => 'required|string',
            'photo' => 'required|array',
            'photo.*' => 'image|mimes:jpg,jpeg,png|max:2048',
        ]);

        try {
            $photoUrls = [];

            foreach ($request->file('photo') as $file) {
                $path = $file->store('report_photos', 'public');
                $photoUrls[] = Storage::url($path);
            }

            Report::create([
                'transaction_id' => $request->transaction_id,
                'reporter_id' => $request->reporter_id,
                'reported_id' => $request->reported_id,
                'reasons' => $request->reasons,
                'photo_url' => json_encode($photoUrls),
                'status' => 'Not Reviewed',
            ]);

            return response()->json(['success' => true, 'message' => 'Laporan berhasil dikirim.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function showAcceptedWork($transactionId)
    {
        $transaction = Transaction::findOrFail($transactionId);
        $request = $transaction->request;
        $worker = $transaction->worker;
        $completionProof = $transaction->completionProof ?? null;
        return view('Job_Taker.accepted-work-request', compact('transaction', 'request', 'worker', 'completionProof'));
    }
}
