<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Transaction;
use App\Models\Request as JobRequest; // Alias Request to JobRequest to avoid conflict with Illuminate\Http\Request
use App\Models\WalletTransaction;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request as HttpRequest; // Alias Request to HttpRequest
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class TransactionController extends Controller
{
    public function index()
    {
        // Get the authenticated user's ID
        $userId = Auth::id();

        // Fetch all orders where the authenticated user is the REQUIESTER
        // Eager load the 'userReview' relationship
        $transactions = Transaction::withTrashed()
            ->with(['request', 'requester', 'worker', 'userReview']) // Eager load the NEW userReview relationship
            ->where('requester_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Attach a flag to each order indicating if a review exists and load the review data
        $allOrders = $transactions->map(function ($order) {
            // Check if the specific userReview exists for this order
            $order->has_review = $order->userReview()->exists();
            $order->user_review = $order->userReview; // Get the actual userReview object (will be null if no review)
            return $order;
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
        return view('job-requester.history', compact('allOrders', 'pendingOrders', 'completedOrders', 'cancelledOrders'));
    }

    // ... (rest of your controller methods remain unchanged) ...

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

        // Authorization check: only the requester of the transaction can view this page
        if (Auth::id() !== $transaction->requester_id) {
            // Changed to custom alert
            activity()
                ->inLog('Security') // Kelompokkan ke log 'Security'
                ->on($transaction)  // Targetnya adalah transaksi yang coba diakses
                ->causedBy(Auth::user()) // Pelakunya adalah user yang mencoba akses
                ->log("Percobaan akses tidak sah ke halaman transaksi on-going #{$transaction->order_number}.");
            return redirect()->route('job-req.home')->with('custom_error_alert', 'Anda tidak berwenang melihat halaman ini.');
        }

        // Ambil data request yang berhubungan dengan transaction
        $request = JobRequest::findOrFail($transaction->request_id);

        // Ambil pekerja yang melakukan pekerjaan berdasarkan relasi
        $worker = $transaction->worker;
        $room = \App\Models\ChatRoom::where('request_id', $request->id)
            ->where('worker_id', $worker->id)
            ->first();

        // Ambil completion proof terkait
        $completionProof = $transaction->completionProof;

        // NEW: Check if a review already exists from the current requester for this transaction
        $hasReview = $transaction->userReview()->exists();
        $userReview = $transaction->userReview; // This will be null if no review exists

        // Kirim data ke view
        return view('job-requester.on-going-work-request', compact('transaction', 'request', 'worker', 'completionProof', 'room', 'hasReview', 'userReview'));
    }


    public function cancel($id, HttpRequest $request)
    {
        // Cari transaction berdasarkan id
        $transaction = Transaction::findOrFail($id);

        // Ubah status transaction menjadi cancelled
        $transaction->status = 'cancelled';
        $transaction->save(); // Pastikan status transaction tersimpan
        $requester = $transaction->requester;
        $payment = $transaction->request->payment;
        $refundAmount = $payment->amount;

        // 5. Proses pengembalian dana (refund) ke requester
        $requester->balance += $refundAmount;
        $requester->locked_balance -= $refundAmount;
        $requester->save();

        WalletTransaction::create([
            'user_id' => $requester->id,
            'amount' => $refundAmount,
            'type' => 'debit',
            'description' => 'Pengembalian saldo dari pembatalan pekerjaan: ' . $transaction->request->title,
        ]);

        $canceller = Auth::user();
        activity()
            ->inLog('Finance')
            ->on($transaction)
            ->causedBy($canceller) // Pelakunya adalah requester yang membatalkan
            ->withProperties(['amount' => $refundAmount])
            ->log("Dana sebesar Rp" . number_format($refundAmount) . " telah dikembalikan ke requester {$requester->first_name} karena pembatalan transaksi #{$transaction->order_number} oleh {$canceller->first_name}.");

        // If the request status should also be updated when cancelled by requester
        // Assuming there's a status on the Request model too
        if ($transaction->request) {
            $transaction->request->status = 'closed'; // Or 'closed' if you prefer
            $transaction->request->save();
        }

        // Changed to custom alert
        $userId = Auth::id();
        $formattedRefundAmount = 'Rp' . number_format($refundAmount, 0, ',', '.');
        if ($userId === $requester->id) {
            $alertMessage = 'Pekerjaan dibatalkan dan dana sebesar ' . $formattedRefundAmount . ' telah dikembalikan.';
        } else {
            $alertMessage = 'Pekerjaan telah berhasil dibatalkan';
        }
        $redirectRoute = $request->input('redirect_to', 'landing');

        return redirect()->route($redirectRoute)->with('custom_info_alert', $alertMessage);
    }

    public function markComplete(Transaction $transaction)
    {
        if (in_array($transaction->status, ['in progress', 'submitted'])) {
            $transaction->status = 'completed';
            $transaction->save();

            // Update the associated request status if needed
            if ($transaction->request) {
                $transaction->request->status = 'closed'; // Or 'closed'
                $transaction->request->save();
            }
        }
        DB::transaction(function () use ($transaction) {
            // 3. Ambil semua data yang dibutuhkan
            $workRequest = $transaction->request;
            $requester = $transaction->requester;
            $worker = $transaction->worker;
            $payment = $workRequest->payment;
            $payoutAmount = $payment->amount; // Jumlah yang akan dibayarkan

            activity()
                ->inLog('Finance')
                ->on($transaction) // Targetnya adalah transaksi ini
                ->causedBy($requester) // Pelakunya adalah requester yang menekan tombol "selesai"
                ->withProperties(['amount' => $payoutAmount, 'worker_id' => $worker->id])
                ->log("Dana sebesar Rp" . number_format($payoutAmount) . " telah dilepaskan ke pekerja {$worker->first_name} untuk transaksi #{$transaction->order_number}.");

            // 4. Proses pelepasan dana (payout)
            // a. Kurangi saldo tertahan milik Requester
            $requester->locked_balance -= $payoutAmount;
            $requester->save();

            // b. Tambah saldo aktif milik Worker
            $worker->balance += $payoutAmount;
            $worker->save();

            // 5. Catat riwayat transaksi untuk kedua belah pihak
            // a. Catatan untuk Requester (uang keluar)
            // b. Catatan untuk Worker (uang masuk)
            WalletTransaction::create([
                'user_id' => $worker->id,
                'amount' => $payoutAmount,
                'type' => 'debit',
                'description' => 'Pembayaran diterima dari pekerjaan: ' . $workRequest->title,
            ]);

            // 6. Update status di semua tabel terkait
            $payment->update(['status' => 'released_to_worker']);
        });
        return response()->json([
            'success' => true,
            'message' => 'Pekerjaan berhasil ditandai selesai!'
        ]);
    }

    public function storeReport(HttpRequest $request)
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
    public function getTransactionDetails($id)
    {
        // Temukan transaksi berdasarkan ID
        $transaction = Transaction::findOrFail($id);
        // Otorisasi: Pastikan hanya worker yang bersangkutan yang bisa akse
        // Kirim kembali data yang dibutuhkan dalam format JSON
        return response()->json([
            'success' => true,
            'finish_work' => $transaction->finish_work ? date('d M Y H:i', strtotime($transaction->finish_work)) : '-',
            // Anda bisa tambahkan data lain di sini jika perlu di masa depan
            // 'status_text' => $transaction->status_text,
        ]);
    }
}
