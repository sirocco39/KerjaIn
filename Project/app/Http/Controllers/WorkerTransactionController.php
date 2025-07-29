<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\CompletionProof;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\Request as JobRequest; // Alias Request to JobRequest
use App\Models\Report; // Add this import
use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

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

            // NEW: Load worker's report about the requester for this transaction
            // The `userReport` relationship is a HasOne, so it will fetch the first report found.
            // For allowing multiple reports, the frontend will simply present a fresh form.
            $workerReportForTransaction = Report::where('transaction_id', $transaction->id)
                ->where('reporter_id', $userId) // Reporter is the current worker
                ->where('reported_id', $transaction->requester_id) // Reported is the requester of this transaction
                ->first();

            $transaction->workerReport = $workerReportForTransaction;
            $transaction->has_worker_report = ($workerReportForTransaction !== null);

            // Ensure workerReport object is available and its properties are decoded for Blade
            if ($transaction->workerReport) {
                $transaction->workerReport->decoded_photo_urls = json_decode($transaction->workerReport->photo_url, true) ?? [];
            } else {
                // Create a dummy object if no report exists, to prevent errors in Blade
                $transaction->workerReport = (object)[
                    'decoded_photo_urls' => [],
                    'reasons' => null,
                ];
            }

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
            activity()
                ->inLog('Security')
                ->on($transaction)
                ->causedBy(Auth::user())
                ->log("Percobaan akses tidak sah ke halaman pekerjaan yang diterima #{$transaction->order_number}.");
            return redirect()->route('job-taker.home')->with('custom_error_alert', __('alerts.anda_tidak_berwenang_melihat'));
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

        // NEW: Check if a review already exists for this worker on this transaction (from requester)
        $hasReview = $transaction->reviewAboutWorker()->exists(); // Review given by requester about worker
        $receivedReview = $transaction->reviewAboutWorker; // Get the review object itself

        // NEW: Check if a review already exists FROM this worker ABOUT the requester for this transaction
        $hasReviewRequester = $transaction->reviewAboutRequester()->exists(); // Review given by worker about requester
        $receivedReviewRequester = $transaction->reviewAboutRequester; // This will be null if no review exists

        // NEW: Check if a report already exists FROM this worker ABOUT the requester for this transaction
        // The `userReport` relationship is a HasOne, so it will fetch the first report found.
        // For allowing multiple reports, the frontend will simply present a fresh form.
        $hasWorkerReport = Report::where('transaction_id', $transaction->id)
            ->where('reporter_id', Auth::id())
            ->where('reported_id', $transaction->requester_id)
            ->first();

        $workerReport = null;
        if ($hasWorkerReport) {
            $workerReport = $hasWorkerReport;
            // Decode photo_url if it's stored as JSON
            $workerReport->decoded_photo_urls = json_decode($workerReport->photo_url, true) ?? [];
        } else {
            // Create a dummy object if no report exists, to prevent errors in Blade
            $workerReport = (object)[
                'decoded_photo_urls' => [],
                'reasons' => null,
            ];
        }

        // Kirim ke view
        return view('job-taker.accepted-work-request', compact(
            'transaction',
            'request',
            'worker',
            'completionProof',
            'room',
            'hasReview',
            'receivedReview',
            'hasReviewRequester', // Pass this flag
            'receivedReviewRequester', // Pass the review about requester if it exists
            'hasWorkerReport', // Pass this flag for worker's own report
            'workerReport' // Pass the worker's report object if it exists
        ));
    }

    public function startWork($id, Request $request)
    {
        $transaction = Transaction::findOrFail($id);

        // Ensure only the assigned worker can start the job
        if (Auth::id() !== $transaction->worker_id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak berwenang untuk memulai pekerjaan ini.'
            ], 403); // Forbidden
        }

        if ($transaction->status !== 'accepted') {
            return response()->json([
                'success' => false,
                'message' => 'Pekerjaan tidak dalam status "Diterima" dan tidak dapat dimulai.'
            ], 400); // Bad Request
        }

        $transaction->status = 'in progress';
        $transaction->start_work = Carbon::now();
        $transaction->save();

        activity()
            ->inLog('Transaction')
            ->performedOn($transaction)
            ->causedBy(Auth::user())
            ->log("Pekerja telah memulai pekerjaan untuk transaksi #{$transaction->order_number}.");

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
     * @param   \Illuminate\Http\Request  $request
     * @param   \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function uploadProof(Request $request, Transaction $transaction)
    {
        try {
            // Ensure only the assigned worker can upload proof
            if (Auth::id() !== $transaction->worker_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak berwenang untuk mengunggah bukti pekerjaan ini.'
                ], 403);
            }

            if ($transaction->status !== 'in progress') {
                return response()->json([
                    'success' => false,
                    'message' => 'Pekerjaan tidak dalam status "Dikerjain" dan tidak dapat mengunggah bukti.'
                ], 400);
            }

            $request->validate([
                'photo' => 'required|array',
                'photo.*' => 'image|max:5120', // Max 5MB per image, consistent with previous context
                'note' => 'nullable|string|max:2000', // Added max length for note
            ]);

            $uploadedPhotoUrls = [];
            foreach ($request->file('photo') as $file) {
                // Store the file in 'completion_proofs' directory under 'public' disk
                $path = $file->store('completion_proofs', 'public');
                // Get the public URL for the stored file
                $photoUrl = Storage::url($path);
                $uploadedPhotoUrls[] = $photoUrl;
            }

            // Create a single CompletionProof record with JSON-encoded photo_url
            CompletionProof::updateOrCreate(
                ['transaction_id' => $transaction->id], // Find by transaction_id
                [
                    'photo_url' => json_encode($uploadedPhotoUrls), // Store as JSON array
                    'note' => $request->note,
                    'submitted_at' => now(),
                ]
            );

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
                'message' => 'Validasi gagal.',
                'errors' => $e->errors()
            ], 422); // 422 Unprocessable Entity for validation errors
        } catch (\Exception $e) {
            // Log the actual error for debugging
            Log::error("Error uploading proof for transaction {$transaction->id}: " . $e->getMessage());

            // Return JSON response for other general errors
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat upload foto: ' . $e->getMessage()
            ], 500); // 500 Internal Server Error
        }
    }

    // This `markComplete` method in WorkerTransactionController is likely for when a worker marks it complete.
    // However, the payment release logic usually happens from the requester's side.
    // If this method is indeed for worker to mark as "submitted", keep it simple.
    // If it's intended to finalize, it needs more robust logic.
    // Assuming it's for worker to set status to 'submitted'
    public function markComplete(Transaction $transaction)
    {
        // This method in WorkerTransactionController should perhaps not finalize the payment,
        // but merely transition the status to 'submitted' from the worker's perspective.
        // The actual 'completed' status and payment release should ideally be triggered by the requester.
        // If this method is called, it means the worker is confirming completion, awaiting requester's finalization.

        if (Auth::id() !== $transaction->worker_id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak berwenang untuk menandai pekerjaan ini selesai.'
            ], 403);
        }

        if ($transaction->status === 'in progress') {
            $transaction->status = 'submitted';
            $transaction->finish_work = Carbon::now(); // Ensure finish_work is set here
            $transaction->save();

            activity()
                ->inLog('Transaction')
                ->performedOn($transaction)
                ->causedBy(Auth::user())
                ->log("Pekerja telah menandai pekerjaan #{$transaction->order_number} sebagai 'submitted'.");

            return response()->json([
                'success' => true,
                'message' => 'Pekerjaan berhasil ditandai selesai dan sedang menunggu konfirmasi dari klien.',
                'new_status' => $transaction->status,
                'finish_work_time' => $transaction->finish_work->format('d M Y H:i'),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Status pekerjaan tidak memungkinkan untuk ditandai selesai.'
        ], 400);
    }


    public function storeReport(Request $request, $transactionId) // Changed method name to avoid conflict, used 'Request' alias
    {
        // Validate the incoming request data
        $request->validate([
            'reasons' => 'required|string|max:2000',
            'photo' => 'required|array|min:1|max:7', // At least 1, max 7 photos
            'photo.*' => 'image|mimes:jpg,jpeg,png,gif,webp|max:5120', // Each photo max 5MB
            'reporter_id' => 'required|exists:users,id',
            'reported_id' => 'required|exists:users,id',
        ]);

        $transaction = Transaction::findOrFail($transactionId);

        // Authorization check: only the worker of the transaction can report about the requester
        if (Auth::id() !== $transaction->worker_id || $request->reporter_id != Auth::id()) {
            activity()
                ->inLog('Security')
                ->on($transaction)
                ->causedBy(Auth::user())
                ->log("Percobaan laporan tidak sah transaksi #{$transaction->order_number}.");
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak berwenang melaporkan transaksi ini.'
            ], 403);
        }

        try {
            $photoUrls = []; // Array to store public URLs of uploaded photos

            foreach ($request->file('photo') as $file) {
                $path = $file->store('reports/photos', 'public'); // Store in storage/app/public/reports/photos
                $photoUrls[] = Storage::url($path); // Get public URL for storage
            }

            // Always create a new report entry
            Report::create([
                'transaction_id' => $transaction->id,
                'reporter_id' => $request->reporter_id,
                'reported_id' => $request->reported_id,
                'reasons' => $request->reasons,
                'photo_url' => json_encode($photoUrls), // Store JSON encoded array of URLs
                'status' => 'Not Reviewed', // Default status for a new report
            ]);

            activity()
                ->inLog('Report')
                ->performedOn($transaction)
                ->causedBy(Auth::user())
                ->withProperties([
                    'transaction_id' => $transaction->id,
                    'reported_user_id' => $request->reported_id,
                    'reasons' => $request->reasons,
                    'photo_count' => count($photoUrls)
                ])
                ->log("Pekerja telah mengajukan laporan untuk transaksi #{$transaction->order_number} mengenai klien.");

            return response()->json([
                'success' => true,
                'message' => 'Laporan berhasil dikirim dan akan segera ditinjau.'
            ]);
        } catch (\Exception $e) {
            Log::error("Error submitting report for worker transaction {$transactionId}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengirim laporan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function storeReview(Request $request, Transaction $transaction) // Changed method name to avoid conflict
    {
        // Validate the incoming request data
        $request->validate([
            'transaction_id' => 'required|exists:transactions,id',
            'reviewer_id' => 'required|exists:users,id',
            'reviewee_id' => 'required|exists:users,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000', // Added max length for comment
        ]);

        // Authorization check: only the worker of the transaction can review the requester
        if (Auth::id() !== $transaction->worker_id || $request->reviewer_id != Auth::id() || $request->reviewee_id != $transaction->requester_id) {
            activity()
                ->inLog('Security')
                ->on($transaction)
                ->causedBy(Auth::user())
                ->log("Percobaan ulasan tidak sah transaksi #{$transaction->order_number} oleh user bukan pekerja.");
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak berwenang untuk memberikan ulasan ini.'
            ], 403);
        }

        // Check if a review already exists from this worker about this requester for this transaction
        $existingReview = Review::where('transaction_id', $transaction->id)
            ->where('reviewer_id', Auth::id())
            ->where('reviewee_id', $transaction->requester_id)
            ->first();

        if ($existingReview) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah memberikan ulasan untuk transaksi ini.'
            ], 409); // Conflict
        }

        try {
            Review::create([
                'transaction_id' => $request->transaction_id,
                'reviewer_id' => $request->reviewer_id,
                'reviewee_id' => $request->reviewee_id,
                'rating' => $request->rating,
                'comment' => $request->comment,
            ]);

            // Update the average rating for the reviewee (requester in this case)
            $averageRating = Review::where('reviewee_id', $request->reviewee_id)->avg('rating');
            User::where('id', $request->reviewee_id)->update(['rating' => $averageRating]);

            activity()
                ->inLog('Review')
                ->performedOn($transaction)
                ->causedBy(Auth::user())
                ->withProperties([
                    'transaction_id' => $transaction->id,
                    'reviewed_user_id' => $request->reviewee_id,
                    'rating' => $request->rating,
                    'comment' => $request->comment
                ])
                ->log("Pekerja telah memberikan ulasan ({$request->rating} bintang) untuk klien transaksi #{$transaction->order_number}.");

            return response()->json([
                'success' => true,
                'message' => 'Ulasan Anda berhasil disimpan!'
            ]);
        } catch (\Exception $e) {
            Log::error("Error storing review for worker transaction {$transaction->id}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan ulasan: ' . $e->getMessage()
            ], 500);
        }
    }
}

