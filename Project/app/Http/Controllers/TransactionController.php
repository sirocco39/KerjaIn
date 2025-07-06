<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Request as JobRequest;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request as HttpRequest;

class TransactionController extends Controller
{
    public function showOngoing($transactionId)
    {
        // Ambil data transaction berdasarkan ID
        $transaction = Transaction::findOrFail($transactionId);

        // Ambil data request yang berhubungan dengan transaction
        $request = JobRequest::findOrFail($transaction->request_id);

        // Ambil pekerja yang melakukan pekerjaan berdasarkan relasi
        $worker = $transaction->worker; // Pastikan relasi sudah ada di model Transaction

        // Generate nomor pesanan random (misalnya 12 digit)
        $orderNumber = '#' . str_pad(rand(0, 999999999999), 12, '0', STR_PAD_LEFT);

        // Ambil completion proof terkait
        $completionProof = $transaction->completionProof;

        // Kirim data ke view
        return view('Job_Requester.on-going-work-request', compact('transaction', 'request', 'worker', 'orderNumber', 'completionProof'));
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
}
