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
        // Eager load the 'userReview' and 'userReport' relationships
        // userReport is the report specific to the *authenticated user*
        $transactions = Transaction::withTrashed()
            ->with(['request', 'requester', 'worker', 'userReview', 'userReport'])
            ->where('requester_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Attach flags and process report data for each order
        $allOrders = $transactions->map(function ($order) {
            // Check if the specific userReview exists for this order
            $order->has_review = $order->userReview()->exists();
            $order->user_review = $order->userReview; // Get the actual userReview object (will be null if no review)

            // NEW: Check for existing report and load report data
            $order->has_user_report = $order->userReport()->exists();

            // Corrected logic: Ensure $order->userReport is an object before accessing its properties.
            // If userReport() returns null, assign a new StdClass object to it
            // and then set the properties on that new object.
            if ($order->userReport) { // Check if the relationship loaded an actual report
                $order->user_report->decoded_photo_urls = json_decode($order->userReport->photo_url, true) ?? [];
                // Ensure 'reasons' is accessible, even if not explicitly stored in decoded_photo_urls
                // It should be a direct property of the Report model if it exists.
                // You might need to adjust your Report model's accessors/attributes if 'reasons' isn't directly available.
                // Assuming 'reasons' is a standard column, it will be available when $order->userReport is not null.
            } else {
                // If no userReport exists, create a dummy object to prevent errors in Blade
                $order->user_report = (object)[
                    'decoded_photo_urls' => [],
                    'reasons' => null, // Provide a default null for 'reasons' as well
                ];
            }

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
            activity()
                ->inLog('Security') // Kelompokkan ke log 'Security'
                ->on($transaction)  // Targetnya adalah transaksi yang coba diakses
                ->causedBy(Auth::user()) // Pelakunya adalah user yang mencoba akses
                ->log("Percobaan akses tidak sah ke halaman transaksi on-going #{$transaction->order_number}.");
            return redirect()->route('job-req.home')->with('custom_error_alert', __('alerts.anda_tidak_berwenang_melihat'));
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

        // Check if a review already exists from the current requester for this transaction
        $hasReview = $transaction->userReview()->exists();
        $userReview = $transaction->userReview; // This will be null if no review exists

        // Add this to check for an existing user report
        // Note: With multiple reports allowed, this only checks if *any* report by the user exists.
        // You might need to adjust logic if you need to fetch a specific 'latest' report.
        $hasUserReport = $transaction->userReport()->exists();
        $userReport = $transaction->userReport; // This will be null if no report exists

        // Corrected logic: Ensure $userReport is an object before trying to set properties
        if ($userReport) { // Check if the relationship loaded an actual report
            $userReport->decoded_photo_urls = json_decode($userReport->photo_url, true) ?? [];
        } else {
            // If no userReport exists, create a dummy object to prevent errors in Blade
            $userReport = (object)[
                'decoded_photo_urls' => [],
                'reasons' => null, // Also provide a default null for 'reasons'
            ];
        }

        // Kirim data ke view, including hasUserReport and userReport
        return view('job-requester.on-going-work-request', compact('transaction', 'request', 'worker', 'completionProof', 'room', 'hasReview', 'userReview', 'hasUserReport', 'userReport'));
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
           return redirect()->route('job-req.home')->with('custom_error_alert', __('alerts.anda_tidak_berwenang'));
        }

        // REMOVED: This block prevents multiple reports.
        /*
        $existingReport = Report::where('transaction_id', $transactionId)
            ->where('reporter_id', Auth::id())
            ->first();
        if ($existingReport) {
            // Changed to custom_error_alert for consistency
            return redirect()->route('job-req.history')->with('custom_error_alert', 'Anda sudah mengajukan laporan untuk transaksi ini.');
        }
        */

        DB::transaction(function () use ($request, $transaction) {
            $photoUrls = []; // Array to store public URLs of uploaded photos

            foreach ($request->file('photo') as $file) {
                $path = $file->store('reports/photos', 'public'); // Store in storage/app/public/reports/photos
                $photoUrls[] = Storage::url($path); // Get public URL for storage
            }

            Report::create([
                'transaction_id' => $transaction->id,
                'reporter_id' => $request->reporter_id,
                'reported_id' => $request->reported_id,
                'reasons' => $request->reasons,
                'photo_url' => json_encode($photoUrls), // Store JSON encoded array of URLs
                'status' => 'pending', // Default status for a new report
            ]);
        });

        // Changed to custom_success_alert for consistency
       return redirect()->route('job-req.history')->with('custom_success_alert', __('alerts.laporan_berhasil_dikirim'));
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
