<?php

namespace App\Http\Controllers;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\CompletionProof;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Str;


class WorkerTransactionController extends Controller
{

    public function show($id)
    {
        // Ambil data transaction berdasarkan ID
        $transaction = Transaction::findOrFail($id);

        // Kirim ke view
        return view('Job_Taker.accepted-work-request', compact('transaction'));
    }

    public function startWork($id, Request $request)
    {
        $transaction = Transaction::findOrFail($id);

        // Update status menjadi in_progress
        $transaction->status = 'in progress';
        $transaction->save();

        return back()->with('success', 'Pekerjaan dimulai.');
    }

    public function uploadProof(Request $request, Transaction $transaction)
    {
        $request->validate([
            'photo' => 'required|image|max:2048', // max 2MB
            'note' => 'nullable|string',
        ]);

        // Simpan file ke storage
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('completion_proofs', 'public');
            $photoUrl = Storage::url($path);
        } else {
            return back()->with('error', 'Foto bukti harus diunggah.');
        }

        // Insert ke tabel completion_proofs
        CompletionProof::create([
            'transaction_id' => $transaction->id,
            'photo_url' => $photoUrl,
            'note' => $request->note,
            'submitted_at' => null,
        ]);

        return back()->with('success', 'Bukti pekerjaan berhasil diupload.');
    }

    public function markComplete(Transaction $transaction)
    {
        // Update status transaction menjadi 'submitted'
        $transaction->status = 'submitted';
        $transaction->save();

        // Update submitted_at pada completion_proofs yang terkait
        $completionProof = CompletionProof::where('transaction_id', $transaction->id)->first();

        if ($completionProof) {
            $completionProof->submitted_at = Carbon::now();
            $completionProof->save();
        }

        // Ambil data yang dibutuhkan untuk pop-up rating
        $job = Request::find($transaction->job->request_id);
        $requester = $transaction->job->requester;

        // Generate nomor pesanan
        $orderNumber = '#' . Str::random(12);

        // Kirim data ke view via session flash
        return back()->with([
            'show_rating_modal' => true,
            'rating_data' => [
                'title' => $job->title,
                'order_number' => $orderNumber,
                'client_name' => $requester->first_name . ' ' . $requester->last_name,
                'location' => $job->location,
                'order_date' => $job->start_time->format('Y-m-d'),
                'completion_date' => $job->end_time->format('Y-m-d'),
                'start_time' => $job->start_time->format('H.i'),
                'end_time' => $job->end_time->format('H.i'),
                'price' => $job->price,
            ],
        ]);

    }
}