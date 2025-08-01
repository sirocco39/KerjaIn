<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Transaction;
use App\Models\Request as JobRequest; 
use App\Models\WalletTransaction;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request as HttpRequest; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class TransactionController extends Controller
{
    public function index()
    {
        
        $userId = Auth::id();

        
        
        
        $transactions = Transaction::withTrashed()
            ->with(['request', 'requester', 'worker', 'userReview', 'userReport'])
            ->where('requester_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        
        $allOrders = $transactions->map(function ($order) {
            
            $order->has_review = $order->userReview()->exists();
            $order->user_review = $order->userReview; 

            
            
            
            $userReport = $order->userReport; 

            $order->has_user_report = ($userReport !== null); 

            
            $order->report_decoded_photo_urls = [];
            $order->report_reasons = null;

            if ($userReport) { 
                $order->report_decoded_photo_urls = json_decode($userReport->photo_url, true) ?? [];
                $order->report_reasons = $userReport->reasons;
            }

            return $order;
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

        
        return view('job-requester.history', compact('allOrders', 'pendingOrders', 'completedOrders', 'cancelledOrders'));
    }

    

    public function show(string $id)
    {
        
        $workRequest = Transaction::where('id', $id)->with('requester', 'request')->firstOrFail();
        
        
        if (!$workRequest || $workRequest->deleted_at) {
            abort(404, 'Request not found or has been deleted.');
        }

        return response()->json($workRequest);
    }

    public function showOngoing($transactionId)
    {
        
        $transaction = Transaction::findOrFail($transactionId);

        
        if (Auth::id() !== $transaction->requester_id) {
            activity()
                ->inLog('Security') 
                ->on($transaction)  
                ->causedBy(Auth::user()) 
                ->log("Percobaan akses tidak sah ke halaman transaksi on-going #{$transaction->order_number}.");
            return redirect()->route('job-req.home')->with('custom_error_alert', __('alerts.anda_tidak_berwenang_melihat'));
        }

        
        $request = JobRequest::findOrFail($transaction->request_id);

        
        $worker = $transaction->worker;
        $room = \App\Models\ChatRoom::where('request_id', $request->id)
            ->where('worker_id', $worker->id)
            ->first();

        
        $completionProof = $transaction->completionProof;

        
        $hasReview = $transaction->userReview()->exists();
        $userReview = $transaction->userReview; 

        
        
        
        $hasUserReport = $transaction->userReport()->exists();
        $userReport = $transaction->userReport; 

        
        if ($userReport) { 
            $userReport->decoded_photo_urls = json_decode($userReport->photo_url, true) ?? [];
        } else {
            
            
            $dummyUserReport = (object)[
                'decoded_photo_urls' => [],
                'reasons' => null, 
            ];
            $userReport = $dummyUserReport;
        }

        
        return view('job-requester.on-going-work-request', compact('transaction', 'request', 'worker', 'completionProof', 'room', 'hasReview', 'userReview', 'hasUserReport', 'userReport'));
    }


    public function cancel($id, HttpRequest $request)
    {
        
        $transaction = Transaction::findOrFail($id);

        
        $transaction->status = 'cancelled';
        $transaction->save(); 
        $requester = $transaction->requester;
        $payment = $transaction->request->payment;
        $refundAmount = $payment->amount;

        
        $requester->balance += $refundAmount;
        $requester->locked_balance -= $refundAmount;
        $requester->save();

        WalletTransaction::create([
            'user_id' => $requester->id,
            'amount' => $refundAmount,
            'type' => 'debit',
            'description_id' => 'Pengembalian saldo dari pembatalan pekerjaan: ' . $$transaction->request->title,
            'description_en' => 'Balance refund from job cancellation: ' . $transaction->request->title,
        ]);

        $canceller = Auth::user();
        activity()
            ->inLog('Finance')
            ->on($transaction)
            ->causedBy($canceller) 
            ->withProperties(['amount' => $refundAmount])
            ->log("Dana sebesar Rp" . number_format($refundAmount) . " telah dikembalikan ke requester {$requester->first_name} karena pembatalan transaksi #{$transaction->order_number} oleh {$canceller->first_name}.");

        
        
        if ($transaction->request) {
            $transaction->request->status = 'closed'; 
            $transaction->request->save();
        }

        
        $userId = Auth::id();
        $formattedRefundAmount = 'Rp' . number_format($refundAmount, 0, ',', '.');
        if ($userId === $requester->id) {
            $alertMessage = __('alerts.pekerjaan_dibatalkan_dana_kembali', ['amount' => $formattedRefundAmount]);
        } else {
            $alertMessage = __('alerts.pekerjaan_berhasil_dibatalkan');
        }
        $redirectRoute = $request->input('redirect_to', 'landing');

        return redirect()->route($redirectRoute)->with('custom_info_alert', $alertMessage);
    }

    public function markComplete(Transaction $transaction)
    {
        if (in_array($transaction->status, ['in progress', 'submitted'])) {
            $transaction->status = 'completed';
            $transaction->save();

            
            if ($transaction->request) {
                $transaction->request->status = 'closed'; 
                $transaction->request->save();
            }
        }
        DB::transaction(function () use ($transaction) {
            
            $workRequest = $transaction->request;
            $requester = $transaction->requester;
            $worker = $transaction->worker;
            $payment = $workRequest->payment;
            $payoutAmount = $payment->amount; 

            activity()
                ->inLog('Finance')
                ->on($transaction) 
                ->causedBy($requester) 
                ->withProperties(['amount' => $payoutAmount, 'worker_id' => $worker->id])
                ->log("Dana sebesar Rp" . number_format($payoutAmount) . " telah dilepaskan ke pekerja {$worker->first_name} untuk transaksi #{$transaction->order_number}.");

            
            
            $requester->locked_balance -= $payoutAmount;
            $requester->save();

            
            $worker->balance += $payoutAmount;
            $worker->save();

            
            
            
            WalletTransaction::create([
                'user_id' => $worker->id,
                'amount' => $payoutAmount,
                'type' => 'debit',
                'description_id' => 'Pembayaran diterima dari pekerjaan: ' . $workRequest->title,
                'description_en' => 'Payment received from job: ' . $workRequest->title,
            ]);

            
            $payment->update(['status' => 'released_to_worker']);
        });
        return response()->json([
            'success' => true,
            'message' => __('alerts.job_marked_completed_success')
        ]);
    }

    public function storeReport(HttpRequest $request, $transactionId)
    {
        $request->validate([
            'reasons' => 'required|string|max:2000',
            'photo' => 'required|array|min:1|max:7',
            'photo.*' => 'image|mimes:jpg,jpeg,png,gif,webp|max:5120',
            'reporter_id' => 'required|exists:users,id',
            'reported_id' => 'required|exists:users,id',
        ]);

        $transaction = Transaction::findOrFail($transactionId);

        if (Auth::id() !== $transaction->requester_id || $request->reporter_id != Auth::id()) {
            activity()
                ->inLog('Security')
                ->on($transaction)
                ->causedBy(Auth::user())
                ->log("Percobaan laporan tidak sah transaksi #{$transaction->order_number} oleh user bukan requester.");
            return response()->json([
                'success' => false,
                'message' => __('alerts.anda_tidak_berwenang')
            ], 403);
        }

        DB::transaction(function () use ($request, $transaction) {
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
        });

        
        return response()->json([
            'success' => true,
            'message' => __('alerts.laporan_berhasil_dikirim')
        ]);
    }

    public function getTransactionDetails($id)
    {
        
        $transaction = Transaction::findOrFail($id);
        
        
        return response()->json([
            'success' => true,
            'finish_work' => $transaction->finish_work ? date('d M Y H:i', strtotime($transaction->finish_work)) : '-',
            
            
        ]);
    }
}
