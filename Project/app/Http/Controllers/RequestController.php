<?php

namespace App\Http\Controllers;

use App\Models\ChatRoom;
use App\Models\Payment;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Request as JobRequest;

use App\Models\Request as RequestModel; // Avoid conflict with the Request facade
use App\Models\User;
use App\Models\WalletTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        #get 5 latest open requests and deleted_at is null

        $fiveLatestRequests = RequestModel::whereNull('deleted_at')
            ->where('status', 'open')
            ->latest()
            ->take(5)
            ->get();
        // $fiveLatestRequests = Request::latest()->where() take(5)->get();
        return view('job-requester.home', compact('fiveLatestRequests'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Show the form for creating a new request
        return view('job-requester.post-work');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'workTitleLabel' => 'required|string|max:255',
            'workDetailLabel' => 'required|string',
            'workPriceLabel' => 'required|numeric|min:5000',
            'workAddressLabel' => 'required|string',
            'workStartDateLabel' => 'required|date',
            'workEndDateLabel' => 'required|date',
            'workStartTimeLabel' => 'required',
            'workEndTimeLabel' => 'required',
        ]);

        $workRequest = new RequestModel();

        //from getting data from other
        $workRequest->requester_id = Auth::id();

        $startDatetime = new \DateTime("{$request->workStartDateLabel} {$request->workStartTimeLabel}:00");
        $endDatetime = new \DateTime("{$request->workEndDateLabel} {$request->workEndTimeLabel}:00");

        // Check if start is after end
        if ($startDatetime > $endDatetime) {
            return back()->withErrors([
                'datetime' => 'Start time must not be after end time.'
            ])->withInput();
        }

        $user = User::find(Auth::id());
        $jobCost = $request->workPriceLabel;
        $user->balance -= $jobCost;
        $user->locked_balance += $jobCost;
        $user->save();

        //from handling post
        $workRequest->title = $request->workTitleLabel;
        $workRequest->slug = Str::slug($workRequest->title);
        $workRequest->description = $request->workDetailLabel;
        $workRequest->price = $request->workPriceLabel;
        $workRequest->final_price = $request->workPriceLabel;
        $workRequest->location = $request->workAddressLabel;
        $workRequest->start_time = $startDatetime;
        $workRequest->end_time = $endDatetime;

        WalletTransaction::create([
            'user_id' => $user->id,
            'amount' => $jobCost,
            'type' => 'credit',
            'description' => 'Penahanan saldo untuk pekerjaan: ' . $workRequest->title,
        ]);

        // d. Buat catatan di tabel payments untuk escrow

        $result = $workRequest->save();
        Payment::create([
            'request_id' => $workRequest->id,
            'amount' => $jobCost,
            'status' => 'holding',
        ]);
        if ($result) {
            // Changed to custom alert
            return redirect()->to('/job-req/beranda')->with('custom_success_alert', 'Pekerjaan berhasil dibuat!');
        } else {
            // Changed to custom alert
            return back()->with('custom_error_alert', 'Terjadi kesalahan saat membuat permintaan pekerjaan.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug)
    {
        //get the request by slug
        $workRequest = RequestModel::where('slug', $slug)->with('transaction')->firstOrFail();
        // If the request is not found, it will throw a 404 error
        // Return the view with the request data
        if (!$workRequest || $workRequest->deleted_at) {
            abort(404, 'Request not found or has been deleted.');
        }

        return response()->json($workRequest);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $slug)
    {

        // Find the request by slug
        $workRequest = RequestModel::where('slug', $slug)->firstOrFail();
        // If the request is not found, it will throw a 404 error
        if (!$workRequest || $workRequest->deleted_at) {
            abort(404, 'Request not found or has been deleted.');
        }

        // Return the edit view with the request data
        return view('job-requester.edit', compact('workRequest'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $slug)
    {
        try {
            DB::transaction(function () use ($request, $slug) {
                $workRequest = RequestModel::where('slug', $slug)->firstOrFail();

                // 2. Otorisasi: Pastikan yang mengedit adalah pemilik
                if (Auth::id() !== $workRequest->requester_id) {
                    throw new \Exception('Anda tidak berwenang untuk mengubah pekerjaan ini.');
                }

                // 3. Validasi Status: Jangan izinkan edit jika pekerjaan sudah tidak 'open'
                if ($workRequest->status !== 'open') {
                    throw new \Exception('Pekerjaan yang sudah berjalan tidak dapat diubah.');
                }

                // Add check for past end_time
                if ($workRequest->end_time && $workRequest->end_time < Carbon::now()) {
                    throw new \Exception('Pekerjaan ini sudah melewati batas waktu dan tidak dapat diubah.');
                }

                // 4. Logika Penyesuaian Saldo
                $user = User::findOrFail(Auth::id());
                $originalPrice = $workRequest->price;
                $newPrice = $request->workPriceLabel;
                $priceDifference = $newPrice - $originalPrice;
                // Jika harga NAIK
                if ($priceDifference > 0) {
                    if ($user->balance < $priceDifference) {
                        throw new \Exception('Saldo Anda tidak cukup untuk menaikkan harga pekerjaan.');
                    }
                    $user->balance -= $priceDifference;
                    $user->locked_balance += $priceDifference;
                    $user->save();

                    WalletTransaction::create([
                        'user_id' => $user->id,
                        'amount' => $priceDifference,
                        'type' => 'credit',
                        'description' => 'Penambahan saldo ditahan untuk perubahan harga pada: ' . $workRequest->title,
                    ]);
                }
                // Jika harga TURUN
                else if ($priceDifference < 0) {
                    $refundAmount = abs($priceDifference);
                    $user->balance += $refundAmount;
                    $user->locked_balance -= $refundAmount;
                    $user->save();

                    WalletTransaction::create([
                        'user_id' => $user->id,
                        'amount' => $refundAmount,
                        'type' => 'debit',
                        'description' => 'Pengembalian saldo ditahan untuk perubahan harga pada: ' . $workRequest->title,
                    ]);
                }

                // 5. Update Detail Pekerjaan (Request)
                $startDatetime = new \DateTime("{$request->workStartDateLabel} {$request->workStartTimeLabel}");
                $endDatetime = new \DateTime("{$request->workEndDateLabel} {$request->workEndTimeLabel}");
                if ($startDatetime >= $endDatetime) {
                    throw new \Exception('Waktu mulai harus sebelum waktu selesai.');
                }

                $workRequest->title = $request->workTitleLabel;
                $workRequest->slug = Str::slug($workRequest->title) . '-' . $workRequest->id; // Buat slug unik
                $workRequest->description = $request->workDetailLabel;
                $workRequest->price = $newPrice;
                $workRequest->final_price = $newPrice; // Update juga final_amount
                $workRequest->location = $request->workAddressLabel;
                $workRequest->start_time = $startDatetime;
                $workRequest->end_time = $endDatetime;
                $workRequest->save();

                // 6. Update Catatan Escrow (Payment)
                $workRequest->payment->update(['amount' => $newPrice]);
            });
        } catch (\Exception $e) {
            // Changed to custom alert
            return back()->with('custom_error_alert', $e->getMessage())->withInput();
        }

        // 7. Redirect jika berhasil
        return redirect()->route('job-req.beranda')->with('custom_success_alert', 'Pekerjaan berhasil diperbarui!');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $slug)
    {
        // Lakukan semua operasi dalam satu transaksi yang aman
        try {
            DB::transaction(function () use ($slug) {
                $workRequest = RequestModel::where('slug', $slug)->firstOrFail();

                // 1. Otorisasi: Pastikan yang menghapus adalah pemilik request
                if (Auth::id() !== $workRequest->requester_id) {
                    // Changed to custom alert
                    return back()->with('custom_error_alert', 'Anda tidak berwenang untuk membatalkan pekerjaan ini.');
                }

                // 2. Validasi: Jangan biarkan request dihapus jika sudah ada offer diterima atau sedang berjalan
                // Anda bisa sesuaikan logika ini sesuai kebutuhan
                if ($workRequest->status !== 'open') {
                    throw new \Exception('Pekerjaan yang sedang berjalan atau sudah selesai tidak dapat dibatalkan.');
                }

                // 3. Ambil data yang dibutuhkan untuk proses refund
                $user = $workRequest->requester; // Ambil user melalui relasi
                $refundAmount = $workRequest->price; // Dana yang di-lock adalah harga awal

                // 4. Proses pengembalian dana (refund)
                $user->balance += $refundAmount;
                $user->locked_balance -= $refundAmount;
                $user->save();

                // 5. Catat transaksi refund di riwayat wallet
                WalletTransaction::create([
                    'user_id' => $user->id,
                    'amount' => $refundAmount,
                    'type' => 'debit',
                    'description' => 'Pengembalian saldo dari pembatalan pekerjaan: ' . $workRequest->title,
                ]);

                // 6. Update status terkait
                $workRequest->payment->update(['status' => 'refunded_to_requester']); // Update status escrow
                $workRequest->chatRooms()->update(['is_open' => false]); // Tutup chat room

                // 7. Hapus request (Soft Delete cara Laravel)
                $workRequest->delete();

                // Pass refund amount to session for display in custom alert
                session()->flash('refund_amount', $refundAmount);
            });
        } catch (\Exception $e) {
            // Changed to custom alert
            return back()->with('custom_error_alert', $e->getMessage());
        }

        // 8. Jika semua berhasil, redirect dengan pesan sukses
        // Changed to custom alert, using the flashed refund_amount
        $refundAmount = session('refund_amount', 0); // Get the flashed amount, default to 0
        $formattedRefundAmount = 'Rp' . number_format($refundAmount, 0, ',', '.');
        return redirect()->route('job-req.beranda')->with('custom_success_alert', 'Pekerjaan berhasil dibatalkan dan dana sebesar ' . $formattedRefundAmount . ' telah dikembalikan.');
    }
    
    public function showOngoing($id)
    {
        $request = JobRequest::findOrFail($id);
        return view('job-requester.on-going-work-request', compact('request'));
    }

    public function acceptRequest(RequestModel $request) // <-- PERUBAHAN DI SINI
    {
        $worker = Auth::user();
        // cari JobRequest berdasarkan ID
        $id = $request->id; // Ambil ID dari request yang diterima
        $jobrequest = JobRequest::findOrFail($id); // Pastikan request ditemukan

        // Ubah status transaction menjadi cancelled
        $jobrequest->status = 'closed';
        $jobrequest->save(); // Pastikan status transaction tersimpan


        // Pastikan worker ditemukan
        if (!$worker) {
            return response()->json(['success' => false, 'message' => 'User tidak terautentikasi.'], 401);
        }        // Panggil static method yang ada di model Request
        $winningChatRoom = RequestModel::hireAndFinalize($request, $worker);

        // Kembalikan response dalam format JSON
        return response()->json([
            'success' => true,
            'message' => 'Pekerjaan berhasil diterima! Anda akan diarahkan ke halaman chat.',
            'redirect_url' => route('job-taker.home')
        ]);
    }
    public function validateRequest(Request $request)
    {
        $rules = [
            'workTitleLabel' => 'required|string|max:255',
            'workDetailLabel' => 'required|string',
            'workPriceLabel' => 'required|numeric|min:5000',
            'workAddressLabel' => 'required|string',
            'workStartDateLabel' => 'required|date',
            'workEndDateLabel' => 'required|date',
            'workStartTimeLabel' => 'required',
            'workEndTimeLabel' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        // ==========================================================
        // LOGIKA KUSTOM YANG DIPERBAIKI
        // ==========================================================
        $validator->after(function ($validator) use ($request) {
            $startDate = $request->input('workStartDateLabel');
            $startTime = $request->input('workStartTimeLabel');
            $endDate = $request->input('workEndDateLabel');
            $endTime = $request->input('workEndTimeLabel');

            // HANYA jalankan validasi perbandingan jika SEMUA field sudah diisi
            if ($startDate && $startTime && $endDate && $endTime) {
                $startDateTime = Carbon::parse($startDate . ' ' . $startTime);
                $endDateTime = Carbon::parse($endDate . ' ' . $endTime);

                if ($startDateTime->gte($endDateTime)) { // gte = greater than or equal
                    $validator->errors()->add(
                        'datetime',
                        __('validation.custom.datetime.after_start_time')
                    );
                }
            }
        });

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        return response()->json(['success' => true]);
    }
}
