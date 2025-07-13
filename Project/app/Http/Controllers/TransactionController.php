<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Request as JobRequest;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request as HttpRequest;
use App\Models\Request;
use Illuminate\Support\Facades\Auth;


class TransactionController extends Controller
{
    public function index()
    {
        //
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
        return view('Job_Requester.riwayat', compact('allOrders', 'pendingOrders', 'completedOrders', 'cancelledOrders'));
    }

    public function showOngoing($transactionId)
    {
        // Ambil data transaction berdasarkan ID
        $transaction = Transaction::findOrFail($transactionId);

        // Ambil data request yang berhubungan dengan transaction
        $request = JobRequest::findOrFail($transaction->request_id);

        // Ambil pekerja yang melakukan pekerjaan berdasarkan relasi
        $worker = $transaction->worker; // Pastikan relasi sudah ada di model Transaction

        // Ambil completion proof terkait
        $completionProof = $transaction->completionProof;

        // Kirim data ke view
        return view('Job_Requester.on-going-work-request', compact('transaction', 'request', 'worker', 'completionProof'));
    }


    public function cancel($id)
    {
        // Cari transaction berdasarkan id
        $transaction = Transaction::findOrFail($id);

        // Ubah status transaction menjadi cancelled
        $transaction->status = 'cancelled';
        $transaction->save(); // Pastikan status transaction tersimpan

        // Jika status request bukan closed, beri info bahwa request tetap ada
        return back()->with('info', 'Pekerjaan dibatalkan dan request status diubah menjadi closed.');
    }

    public function submitCompletion(HttpRequest $request, Transaction $transaction)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        $transaction->status = 'completed';
        $transaction->rating = $validated['rating'];
        $transaction->comment = $validated['comment'];
        $transaction->completed_at = now();
        $transaction->save();

        return back()->with('success', 'Pekerjaan berhasil ditandai selesai dan rating serta komentar telah terkirim.');
    }

    public function markComplete(Transaction $transaction)
    {
        // Cek agar hanya transaksi in progress atau submitted yang bisa ditandai selesai
        if (in_array($transaction->status, ['in progress', 'submitted'])) {
            $transaction->status = 'completed';
            $transaction->save();

            return back()->with('success', 'Pekerjaan berhasil ditandai selesai.');
        }

        return back()->with('error', 'Transaksi tidak dapat ditandai selesai.');
    }
    public function submitReport(Request $request, Transaction $transaction)
    {
        $validated = $request->validate([
            'note' => 'required|string|max:1000',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Simpan report ke tabel reports
        $report = $transaction->reports()->create([
            'worker_id' => $transaction->worker_id,
            'note' => $validated['note'],
        ]);

        // Simpan foto-foto ke storage dan database jika ada
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('reports', 'public');

                $report->images()->create([
                    'path' => $path,
                ]);
            }
        }

        return back()->with('success', 'Laporan berhasil dikirim.');
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
