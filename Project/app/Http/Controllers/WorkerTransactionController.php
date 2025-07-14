<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\CompletionProof;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\Request as JobRequest; // Alias Request to JobRequest
use App\Models\Report;
use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class WorkerTransactionController extends Controller
{
    public function index()
    {
        // Get the authenticated user's ID
        $userId = Auth::id();

        $transactions = Transaction::withTrashed()
            ->with(['request.requester', 'worker']) // Eager load request and its requester
            ->where(function ($query) use ($userId) {
                $query->where('requester_id', $userId)
                    ->orWhere('worker_id', $userId);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // Add a 'status_text' attribute to each transaction for display logic
        $transactions->each(function ($transaction) {
            switch ($transaction->status) {
                case 'accepted':
                    $transaction->status_text = 'Diterima';
                    break;
                case 'in progress':
                    $transaction->status_text = 'Dikerjain';
                    break;
                case 'submitted':
                    $transaction->status_text = 'Ditinjau';
                    break;
                case 'completed':
                    $transaction->status_text = 'Selesai';
                    break;
                case 'cancelled':
                    $transaction->status_text = 'Dibatalin';
                    break;
                default:
                    $transaction->status_text = ucfirst($transaction->status); // Fallback for other statuses
                    break;
            }
        });

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
        return view('Job_Taker.riwayat', compact('allOrders', 'pendingOrders', 'completedOrders', 'cancelledOrders'));
    }

    public function show($id)
    {
        $transaction = Transaction::findOrFail($id);

        $request = JobRequest::findOrFail($transaction->request_id);

        $worker = $transaction->worker;

        $completionProof = $transaction->completionProof;
        $room = \App\Models\ChatRoom::where('request_id', $request->id)
            ->where('worker_id', $worker->id)
            ->first();

        if (!$room) {
            $room = \App\Models\ChatRoom::create([
                'request_id'   => $request->id,
                'requester_id' => $request->requester_id,
                'worker_id'    => $worker->id,
            ]);
        }

        return view('Job_Taker.accepted-work-request', compact(
            'transaction',
            'request',
            'worker',
            'completionProof',
            'room'
        ));
    }

    public function startWork($id, Request $request)
    {
        $transaction = Transaction::findOrFail($id);

        $transaction->status = 'in progress';
        $transaction->start_work = Carbon::now();
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
        $transaction->status = 'submitted'; // This will be 'completed' when the client accepts
        $transaction->save();

        // The completion_proof submitted_at should ideally be set when the worker submits the proof.
        // If this method is called by the worker, it might be redundant.
        // If this method is called when the CLIENT marks it complete, then setting `finish_work` here is fine.
        // For now, let's assume this is the worker marking it complete and submitting.
        // If the `markComplete` function is only for client-side confirmation, then `submitted_at` should not be here.
        // I'll leave it as is based on the original code, but it's something to clarify.

        $completionProof = CompletionProof::where('transaction_id', $transaction->id)->first();
        if ($completionProof) {
            $completionProof->submitted_at = Carbon::now(); // This should be when proof is uploaded/submitted
            $completionProof->save();
        }

        // Get the related JobRequest and Requester for the rating pop-up
        $jobRequest = $transaction->request; // Access directly via the request relationship
        $requester = $jobRequest->requester; // Access requester via the jobRequest relationship

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
                'price' => $job->price,
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

        // Assuming $validated['rating'] is meant to be $request->rating here
        $ratingGiven = $request->rating ?? 5; // Use $request->rating directly

        Review::create([
            'transaction_id' => $request->transaction_id,
            'reviewer_id' => $request->reviewer_id,
            'reviewee_id' => $request->reviewee_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        $averageRating = Review::where('reviewee_id', $request->reviewee_id)->avg('rating');

        User::where('id', $request->reviewee_id)->update(['rating' => $averageRating]);

        return response()->json(['success' => true]);
    }
}
