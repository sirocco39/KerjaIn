<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Transaction;
use App\Models\Request as JobRequest;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request as HttpRequest;
use App\Models\Request;
use Illuminate\Support\Facades\Storage;

class TransactionController extends Controller
{
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

        return view('job-requester.on-going-work-request', compact('transaction', 'request', 'worker', 'completionProof', 'room'));
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

    // public function submitCompletion(HttpRequest $request, Transaction $transaction)
    // {
    //     $validated = $request->validate([
    //         'rating' => 'required|integer|min:1|max:5',
    //         'comment' => 'nullable|string|max:500',
    //     ]);

    //     $transaction->status = 'completed';
    //     $transaction->rating = $validated['rating'];
    //     $transaction->comment = $validated['comment'];
    //     $transaction->completed_at = now();
    //     $transaction->save();

    //     return back()->with('success', 'Pekerjaan berhasil ditandai selesai dan rating serta komentar telah terkirim.');
    // }

    public function markComplete(Transaction $transaction)
    {
        if (in_array($transaction->status, ['in progress', 'submitted'])) {
            $transaction->status = 'completed';
            $transaction->save();
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

            return back()->with('success', 'Laporan berhasil dikirim.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function showAcceptedWork($transactionId)
    {
        $transaction = Transaction::findOrFail($transactionId);
        $request = $transaction->request;
        $worker = $transaction->worker;
        $completionProof = $transaction->completionProof ?? null;
        return view('job-taker.accepted-work-request', compact('transaction', 'request', 'worker', 'completionProof'));
    }
}
