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
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class WorkerTransactionController extends Controller
{
    public function index()
    {
        
        $userId = Auth::id();

        
        
        $transactions = Transaction::withTrashed()
            ->with(['request.requester', 'worker', 'reviewAboutWorker']) 
            ->where('worker_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        
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
                    $transaction->status_text = ucfirst($transaction->status); 
                    break;
            }

            
            
            $transaction->has_review = $transaction->reviewAboutWorker()->exists();
            $transaction->received_review = $transaction->reviewAboutWorker; 

            
            
            
            $workerReportForTransaction = Report::where('transaction_id', $transaction->id)
                ->where('reporter_id', $userId) 
                ->where('reported_id', $transaction->requester_id) 
                ->first();

            $transaction->workerReport = $workerReportForTransaction;
            $transaction->has_worker_report = ($workerReportForTransaction !== null);

            
            if ($transaction->workerReport) {
                $transaction->workerReport->decoded_photo_urls = json_decode($transaction->workerReport->photo_url, true) ?? [];
                
                $transaction->workerReport->created_at_formatted_for_blade = \Carbon\Carbon::parse($transaction->workerReport->created_at)->setTimezone('Asia/Jakarta')->format('d M Y, H:i');
            } else {
                
                $transaction->workerReport = (object)[
                    'decoded_photo_urls' => [],
                    'reasons' => null,
                    'created_at_formatted_for_blade' => '-', 
                ];
            }

            
            $transaction->finish_work_formatted_for_blade = $transaction->finish_work
                ? \Carbon\Carbon::parse($transaction->finish_work)->setTimezone('Asia/Jakarta')->format('d - m - Y')
                : '-';

            return $transaction;
        });

        
        $pendingOrders = $allOrders->filter(function ($transaction) {
            return in_array($transaction->status, ['accepted', 'in progress', 'submitted']);
        });
        $completedOrders = $allOrders->filter(function ($transaction) {
            return $transaction->status === 'completed';
        });
        $cancelledOrders = $allOrders->filter(function ($transaction) {
            return $transaction->status === 'cancelled';
        });

        
        return view('job-taker.history', compact('allOrders', 'pendingOrders', 'completedOrders', 'cancelledOrders'));
    }

    public function show($id)
    {
        $transaction = Transaction::findOrFail($id);

        
        if (Auth::id() !== $transaction->worker_id) {
            activity()
                ->inLog('Security')
                ->on($transaction)
                ->causedBy(Auth::user())
                ->log("Percobaan akses tidak sah ke halaman pekerjaan yang diterima #{$transaction->order_number} pada " . Carbon::now('Asia/Jakarta')->format('d M Y, H:i:s') . "."); 
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

        
        $hasReview = $transaction->reviewAboutWorker()->exists(); 
        $receivedReview = $transaction->reviewAboutWorker; 

        
        $hasReviewRequester = $transaction->reviewAboutRequester()->exists(); 
        $receivedReviewRequester = $transaction->reviewAboutRequester; 

        
        
        
        $hasWorkerReport = Report::where('transaction_id', $transaction->id)
            ->where('reporter_id', Auth::id())
            ->where('reported_id', $transaction->requester_id)
            ->first();

        $workerReport = null;
        if ($hasWorkerReport) {
            $workerReport = $hasWorkerReport;
            
            $workerReport->decoded_photo_urls = json_decode($workerReport->photo_url, true) ?? [];
        } else {
            
            $workerReport = (object)[
                'decoded_photo_urls' => [],
                'reasons' => null,
            ];
        }

        
        $transactionStartWorkFormatted = $transaction->start_work
            ? \Carbon\Carbon::parse($transaction->start_work)->setTimezone('Asia/Jakarta')->format('d M Y H:i')
            : '-';
        $transactionFinishWorkFormatted = $transaction->finish_work
            ? \Carbon\Carbon::parse($transaction->finish_work)->setTimezone('Asia/Jakarta')->format('d M Y H:i')
            : '-';
        $transactionCreatedAtFormatted = \Carbon\Carbon::parse($transaction->created_at)->setTimezone('Asia/Jakarta')->format('d M Y');
        $transactionUpdatedAtFormatted = \Carbon\Carbon::parse($transaction->updated_at)->setTimezone('Asia/Jakarta')->format('d M Y');

        $workerCreatedAtYear = \Carbon\Carbon::parse($worker->created_at)->setTimezone('Asia/Jakarta')->format('F Y');

        
        return view('job-taker.accepted-work-request', compact(
            'transaction',
            'request',
            'worker',
            'completionProof',
            'room',
            'hasReview',
            'receivedReview',
            'hasReviewRequester', 
            'receivedReviewRequester', 
            'hasWorkerReport', 
            'workerReport', 
            'transactionStartWorkFormatted', 
            'transactionFinishWorkFormatted',
            'transactionCreatedAtFormatted',
            'transactionUpdatedAtFormatted',
            'workerCreatedAtYear'
        ));
    }

    public function startWork($id, Request $request)
    {
        $transaction = Transaction::findOrFail($id);

        
        if (Auth::id() !== $transaction->worker_id) {
            return response()->json([
                'success' => false,
                'message' => __('alerts.not_authorized_to_start_job')
            ], 403);
        }

        if ($transaction->status !== 'accepted') {
            return response()->json([
                'success' => false,
                'message' => __('alerts.job_not_in_accepted_status')
            ], 400);
        }

        $transaction->status = 'in progress';
        $transaction->start_work = Carbon::now('UTC'); 
        $transaction->save();

        activity()
            ->inLog('Transaction')
            ->performedOn($transaction)
            ->causedBy(Auth::user())
            ->log("Pekerja telah memulai pekerjaan untuk transaksi #{$transaction->order_number} pada " . Carbon::now('Asia/Jakarta')->format('d M Y, H:i:s') . "."); 

        
        return response()->json([
            'success' => true,
            'message' => __('alerts.job_started'),
            'new_status' => $transaction->status,
            'start_work_time' => $transaction->start_work->setTimezone('Asia/Jakarta')->format('d M Y H:i'), 
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
            
            if (Auth::id() !== $transaction->worker_id) {
                return response()->json([
                    'success' => false,
                    'message' => __('alerts.not_authorized_to_upload_proof')
                ], 403);
            }

            if ($transaction->status !== 'in progress') {
                return response()->json([
                    'success' => false,
                    'message' => __('alerts.job_not_in_progress_status')
                ], 400);
            }
            $request->validate([
                'photo' => 'required|array',
                'photo.*' => 'image|max:5120', 
                'note' => 'nullable|string|max:2000', 
            ]);

            $uploadedPhotoUrls = [];
            foreach ($request->file('photo') as $file) {
                
                $path = $file->store('completion_proofs', 'public');
                
                $photoUrl = Storage::url($path);
                $uploadedPhotoUrls[] = $photoUrl;
            }

            
            CompletionProof::updateOrCreate(
                ['transaction_id' => $transaction->id], 
                [
                    'photo_url' => json_encode($uploadedPhotoUrls), 
                    'note' => $request->note,
                    'submitted_at' => Carbon::now('UTC'), 
                ]
            );

            
            $transaction->status = 'submitted';
            $transaction->finish_work = Carbon::now('UTC'); 
            $transaction->save();

            activity()
                ->inLog('Transaction') 
                ->performedOn($transaction) 
                ->causedBy(Auth::user())    
                ->withProperties(['uploaded_photos' => $uploadedPhotoUrls, 'note' => $request->note]) 
                ->log("Pekerja telah mengunggah bukti penyelesaian pekerjaan pada " . Carbon::now('Asia/Jakarta')->format('d M Y, H:i:s') . "."); 

            
            return response()->json([
                'success' => true,
              'message' => __('alerts.proof_uploaded_success'),
                'photo_urls' => $uploadedPhotoUrls, 
                'new_status' => $transaction->status,
                'finish_work_time' => $transaction->finish_work->setTimezone('Asia/Jakarta')->format('d M Y H:i'), 
                'next_action' => 'show_review_modal' 
            ]);
        } catch (ValidationException $e) {
            
            return response()->json([
                'success' => false,
                 'message' => __('alerts.validation_failed'),
                'errors' => $e->errors()
            ], 422); 
        } catch (\Exception $e) {
            
            Log::error("Error uploading proof for transaction {$transaction->id}: " . $e->getMessage());

            
            return response()->json([
                'success' => false,
                  'message' => __('alerts.error_uploading_proof') . ' ' . $e->getMessage()
            ], 500); 
        }
    }

    
    
    
    
    
    public function markComplete(Transaction $transaction)
    {
        
        
        
        

        if (Auth::id() !== $transaction->worker_id) {
            return response()->json([
                'success' => false,
                'message' => __('alerts.not_authorized_to_mark_complete')
            ], 403);
        }

        if ($transaction->status === 'in progress') {
            $transaction->status = 'submitted';
            $transaction->finish_work = Carbon::now('UTC'); 
            $transaction->save();

            activity()
                ->inLog('Transaction')
                ->performedOn($transaction)
                ->causedBy(Auth::user())
                ->log("Pekerja telah menandai pekerjaan #{$transaction->order_number} sebagai 'submitted' pada " . Carbon::now('Asia/Jakarta')->format('d M Y, H:i:s') . "."); 

            return response()->json([
                'success' => true,
                'message' => __('alerts.job_marked_submitted_success'),
                'new_status' => $transaction->status,
                'finish_work_time' => $transaction->finish_work->setTimezone('Asia/Jakarta')->format('d M Y H:i'), 
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => __('alerts.job_status_not_allowed')
        ], 400);
    }


    public function storeReport(Request $request, $transactionId) 
    {
        
        $request->validate([
            'reasons' => 'required|string|max:2000',
            'photo' => 'required|array|min:1|max:7', 
            'photo.*' => 'image|mimes:jpg,jpeg,png,gif,webp|max:5120', 
            'reporter_id' => 'required|exists:users,id',
            'reported_id' => 'required|exists:users,id',
        ]);

        $transaction = Transaction::findOrFail($transactionId);

        
        if (Auth::id() !== $transaction->worker_id || $request->reporter_id != Auth::id()) {
            activity()
                ->inLog('Security')
                ->on($transaction)
                ->causedBy(Auth::user())
                ->log("Percobaan laporan tidak sah transaksi #{$transaction->order_number} pada " . Carbon::now('Asia/Jakarta')->format('d M Y, H:i:s') . "."); 
          return response()->json([
                'success' => false,
                'message' => __('alerts.not_authorized_to_report')
            ], 403);
        }

        try {
            $photoUrls = []; 

            foreach ($request->file('photo') as $file) {
                $path = $file->store('reports/photos', 'public'); 
                $photoUrls[] = Storage::url($path); 
            }

            
            Report::create([
                'transaction_id' => $transaction->id,
                'reporter_id' => $request->reporter_id,
                'reported_id' => $request->reported_id,
                'reasons' => $request->reasons,
                'photo_url' => json_encode($photoUrls), 
                'status' => 'Not Reviewed', 
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
                ->log("Pekerja telah mengajukan laporan untuk transaksi #{$transaction->order_number} mengenai klien pada " . Carbon::now('Asia/Jakarta')->format('d M Y, H:i:s') . "."); 

            return response()->json([
                'success' => true,
                'message' => __('alerts.report_submitted_success')
            ]);
         } catch (\Exception $e) {
            Log::error("Error submitting report for worker transaction {$transactionId}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => __('alerts.error_submitting_report') . ' ' . $e->getMessage()
            ], 500);
        }
    }

    public function storeReview(Request $request, Transaction $transaction) 
    {
        
        $request->validate([
            'transaction_id' => 'required|exists:transactions,id',
            'reviewer_id' => 'required|exists:users,id',
            'reviewee_id' => 'required|exists:users,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000', 
        ]);

        
        if (Auth::id() !== $transaction->worker_id || $request->reviewer_id != Auth::id() || $request->reviewee_id != $transaction->requester_id) {
            activity()
                ->inLog('Security')
                ->on($transaction)
                ->causedBy(Auth::user())
                ->log("Percobaan ulasan tidak sah transaksi #{$transaction->order_number} oleh user bukan pekerja pada " . Carbon::now('Asia/Jakarta')->format('d M Y, H:i:s') . "."); 
           return response()->json([
                'success' => false,
                'message' => __('alerts.not_authorized_to_review')
            ], 403);
        }

        
        $existingReview = Review::where('transaction_id', $transaction->id)
            ->where('reviewer_id', Auth::id())
            ->where('reviewee_id', $transaction->requester_id)
            ->first();

        if ($existingReview) {
          return response()->json([
                'success' => false,
                'message' => __('alerts.review_already_given')
            ], 409);
        }

        try {
            Review::create([
                'transaction_id' => $request->transaction_id,
                'reviewer_id' => $request->reviewer_id,
                'reviewee_id' => $request->reviewee_id,
                'rating' => $request->rating,
                'comment' => $request->comment,
            ]);

            
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
                ->log("Pekerja telah memberikan ulasan ({$request->rating} bintang) untuk klien transaksi #{$transaction->order_number} pada " . Carbon::now('Asia/Jakarta')->format('d M Y, H:i:s') . "."); 

           return response()->json([
                'success' => true,
                'message' => __('alerts.review_saved_success')
            ]);
        } catch (\Exception $e) {
            Log::error("Error storing review for worker transaction {$transaction->id}: " . $e->getMessage());
           return response()->json([
                'success' => false,
                'message' => __('alerts.error_saving_review') . ' ' . $e->getMessage()
            ], 500);
        }
    }
}
