<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Request as JobRequest;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request as HttpRequest;
use App\Models\Request;


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

        // Ambil completion proof terkait
        $completionProof = $transaction->completionProof;


        $room = \App\Models\ChatRoom::where('request_id', $request->id)
            ->where('worker_id', $worker->id)
            ->first();

        // Jika tidak ditemukan, kamu bisa buat baru (opsional)
        if (!$room) {
            $room = \App\Models\ChatRoom::create([
                'request_id'   => $request->id,
                'requester_id' => $request->requester_id,
                'worker_id'    => $worker->id,
            ]);
        }

        return view('Job_Requester.on-going-work-request', compact('transaction', 'request', 'worker', 'completionProof', 'room'));
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
