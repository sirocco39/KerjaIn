<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\CompletionProof;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\Request as JobRequest;
use App\Models\Report;
use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Facades\Auth;





class WorkerTransactionController extends Controller
{

    public function show($id)
    {
        // Ambil data transaction berdasarkan ID
        $transaction = Transaction::findOrFail($id);

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

        // Kirim ke view
        return view('job-taker.accepted-work-request', compact(
            'transaction',
            'request',
            'worker',
            'completionProof',
            'room'
        ));

        // Kirim ke view
        // return view('Job_Taker.accepted-work-request', compact('transaction', 'request', 'worker', 'completionProof'));
    }

    public function startWork($id, Request $request)
    {
        $transaction = Transaction::findOrFail($id);

        // Update status menjadi in_progress
        $transaction->status = 'in progress';
        $transaction->start_work = Carbon::now(); // Set waktu mulai kerja
        $transaction->save();

        return back()->with('success', 'Pekerjaan dimulai.');
    }

    public function uploadProof(Request $request, Transaction $transaction)
    {
        $request->validate([
            'photo' => 'required|array',
            'photo.*' => 'image|max:2048',
            'note' => 'nullable|string',
        ]);

        foreach ($request->file('photo') as $file) {
            $path = $file->store('completion_proofs', 'public');
            $photoUrl = Storage::url($path);

            CompletionProof::create([
                'transaction_id' => $transaction->id,
                'photo_url' => $photoUrl,
                'note' => $request->note,
                'submitted_at' => now(),
            ]);
        }

        $transaction->status = 'submitted';
        $transaction->save();

        return response()->json([
            'success' => true,
            'message' => 'Bukti pekerjaan berhasil diupload.'
        ]);
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

        // Kirim data ke view via session flash
        return back()->with([
            'show_rating_modal' => true,
            'rating_data' => [
                'title' => $job->title,
                'order_number' => $transaction->order_number,
                'client_name' => $requester->first_name . ' ' . $requester->last_name,
                'location' => $job->location,
                'order_date' => $job->start_time->format('Y-m-d'),
                'completion_date' => $job->end_time->format('Y-m-d'),
                'start_time' => $job->start_time->format('H.i'),
                'end_time' => $job->end_time->format('H.i'),
                'price' => $job->final_price,
            ],
        ]);
    }

    public function storeReport(Request $request)
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
        public function store(Request $request)
    {

        $request->validate([
            'transaction_id' => 'required|exists:transactions,id',
            'reviewer_id' => 'required|exists:users,id',
            'reviewee_id' => 'required|exists:users,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string',
        ]);

        $ratingGiven = $validated['rating'] ?? 5;

        Review::create([
            'transaction_id' => $request->transaction_id,
            'reviewer_id' => $request->reviewer_id,
            'reviewee_id' => $request->reviewee_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        $averageRating = Review::where('reviewee_id', $request->reviewee_id)->avg('rating');

        // 3. Update ke tabel users
        User::where('id', $request->reviewee_id)->update(['rating' => $averageRating]);


        return response()->json(['success' => true]);
    }
}
