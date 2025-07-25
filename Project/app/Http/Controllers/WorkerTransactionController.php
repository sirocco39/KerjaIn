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
use Illuminate\Validation\ValidationException; // Import ValidationException

class WorkerTransactionController extends Controller
{
    public function index()
    {
        // Get the authenticated user's ID (this is the worker)
        $userId = Auth::id();

        // Fetch all orders where the authenticated user is the WORKER
        // Eager load request, its requester, and the reviewAboutWorker relationship
        $transactions = Transaction::withTrashed()
            ->with(['request.requester', 'worker', 'reviewAboutWorker']) // Load the specific review for the worker
            ->where('worker_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Add a 'status_text' attribute and review-related flags/data to each transaction
        $allOrders = $transactions->map(function ($transaction) use ($userId) {
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

            // Check if there's a review *about this worker* for this transaction
            // Use the relation name 'reviewAboutWorker'
            $transaction->has_review = $transaction->reviewAboutWorker()->exists();
            $transaction->received_review = $transaction->reviewAboutWorker; // Get the review object itself

            return $transaction;
        });

        // Prepare data for different tabs based on your string statuses
        $pendingOrders = $allOrders->filter(function ($transaction) {
            return in_array($transaction->status, ['accepted', 'in progress', 'submitted']);
        });
        $completedOrders = $allOrders->filter(function ($transaction) {
            return $transaction->status === 'completed';
        });
        $cancelledOrders = $allOrders->filter(function ($transaction) {
            return $transaction->status === 'cancelled';
        });

        // Render the specified Blade view
        return view('job-taker.history', compact('allOrders', 'pendingOrders', 'completedOrders', 'cancelledOrders'));
    }

    public function show($id)
    {
        $transaction = Transaction::findOrFail($id);

        // Authorization check: only the worker of the transaction can view this page
        if (Auth::id() !== $transaction->worker_id) {
            // Changed to custom alert
            activity()
                ->inLog('Security')
                ->on($transaction)
                ->causedBy(Auth::user())
                ->log("Percobaan akses tidak sah ke halaman pekerjaan yang diterima #{$transaction->order_number}.");
            return redirect()->route('job-taker.home')->with('custom_error_alert', 'Anda tidak berwenang melihat halaman ini.');
        }

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

        // NEW: Check if a review already exists for this worker on this transaction
        $hasReview = $transaction->reviewAboutWorker()->exists();
        $receivedReview = $transaction->reviewAboutWorker; // This will be null if no review exists

        // Kirim ke view
        return view('job-taker.accepted-work-request', compact(
            'transaction',
            'request',
            'worker',
            'completionProof',
            'room',
            'hasReview', // Pass this flag
            'receivedReview' // Pass the review object if it exists
        ));
    }

    public function startWork($id, Request $request)
    {
        $transaction = Transaction::findOrFail($id);

        $transaction->status = 'in progress';
        $transaction->start_work = Carbon::now();
        $transaction->save();

        // Return JSON response for AJAX requests
        return response()->json([
            'success' => true,
            'message' => 'Pekerjaan dimulai.',
            'new_status' => $transaction->status,
            'start_work_time' => $transaction->start_work->format('d M Y H:i'),
        ]);
    }

    /**
     * Handles the upload of completion proof for a transaction.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function uploadProof(Request $request, Transaction $transaction)
    {
        try {
            $request->validate([
                'photo' => 'required|array',
                'photo.*' => 'image|max:2048', // Max 2MB per image
                'note' => 'nullable|string',
            ]);

            $uploadedPhotoUrls = [];
            foreach ($request->file('photo') as $file) {
                // Store the file in 'completion_proofs' directory under 'public' disk
                $path = $file->store('completion_proofs', 'public');
                // Get the public URL for the stored file
                $photoUrl = Storage::url($path);
                $uploadedPhotoUrls[] = $photoUrl;

                // Create a CompletionProof record for each uploaded photo
                CompletionProof::create([
                    'transaction_id' => $transaction->id,
                    'photo_url' => $photoUrl,
                    'note' => $request->note, // Note will be the same for all photos in this submission
                    'submitted_at' => now(),
                ]);
            }

            // Update the transaction status to 'submitted' and set finish_work timestamp
            $transaction->status = 'submitted';
            $transaction->finish_work = Carbon::now();
            $transaction->save();

            activity()
                ->inLog('Transaction') // Kelompokkan ke log 'Transaction'
                ->performedOn($transaction) // Targetnya adalah transaksi ini
                ->causedBy(Auth::user())    // Pelakunya adalah pekerja yang login
                ->withProperties(['uploaded_photos' => $uploadedPhotoUrls, 'note' => $request->note]) // Simpan URL foto & catatan
                ->log("Pekerja telah mengunggah bukti penyelesaian pekerjaan.");
            // Return a JSON success response for AJAX requests
            return response()->json([
                'success' => true,
                'message' => 'Bukti pekerjaan berhasil diupload. Pekerjaan Anda sekarang dalam status ditinjau.',
                'photo_urls' => $uploadedPhotoUrls, // Optionally return uploaded URLs
                'new_status' => $transaction->status,
                'finish_work_time' => $transaction->finish_work->format('d M Y H:i'),
                'next_action' => 'show_review_modal' // Indicate next action for frontend
            ]);
        } catch (ValidationException $e) {
            // Return JSON response for validation errors
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: ' . $e->getMessage(),
                'errors' => $e->errors()
            ], 422); // 422 Unprocessable Entity for validation errors
        } catch (\Exception $e) {
            // Return JSON response for other general errors
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat upload foto: ' . $e->getMessage()
            ], 500); // 500 Internal Server Error
        }
    }

    public function markComplete(Transaction $transaction)
    {
        // This method seems to be for worker marking complete, but the requester actually finalizes.
        // Based on the `on-going-work-request.blade.php` (requester side), the requester calls `markComplete`.
        // This method might be redundant or named incorrectly if it's strictly for worker actions.
        // Assuming for now it's still intended for worker to mark as 'submitted'
        if ($transaction->status === 'in progress') {
            $transaction->status = 'submitted';
            $transaction->save();
        }

        // Ambil data yang dibutuhkan untuk pop-up rating
        $job = JobRequest::find($transaction->request_id); // Corrected to use JobRequest alias
        $requester = $transaction->requester; // Corrected to use requester relation

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

            // Changed from JSON response to redirect with custom alert
            return response()->json([
                'success' => true,
                'message' => 'Laporan berhasil dikirim dan akan segera ditinjau.'
            ]);
        } catch (\Exception $e) {
            // Changed from JSON response to redirect with custom alert
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengirim laporan: ' . $e->getMessage()
            ], 500);
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

        // Changed from JSON response to redirect with custom alert
        return response()->json([
            'success' => true,
            'message' => 'Ulasan Anda berhasil disimpan!'
        ]);
    }
}
