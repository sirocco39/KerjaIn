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

        // NEW: Parse input strings directly as UTC
        $startDateTimeString = "{$request->workStartDateLabel} {$request->workStartTimeLabel}";
        $endDateTimeString = "{$request->workEndDateLabel} {$request->workEndTimeLabel}";

        $startDatetime = Carbon::createFromFormat('Y-m-d H:i', $startDateTimeString, 'UTC');
        $endDatetime = Carbon::createFromFormat('Y-m-d H:i', $endDateTimeString, 'UTC');

        // NEW: Check if start_time is in the past (UTC comparison)
        if ($startDatetime->isPast('UTC')) {
            return back()->withErrors([
                'workStartDateLabel' => 'Waktu mulai tidak boleh di masa lalu (UTC).'
            ])->withInput();
        }

        // Check if start is after or equal to end time (UTC comparison)
        if ($startDatetime->gte($endDatetime)) {
            return back()->withErrors([
                'datetime' => 'Waktu mulai harus sebelum waktu selesai.'
            ])->withInput();
        }

        $user = User::find(Auth::id());
        $jobCost = $request->workPriceLabel + 2500;
        $escrowAmount = $request->workPriceLabel;

        // Ensure user has sufficient balance for jobCost + service_fee (2500)
        if ($user->balance < $jobCost) {
            return back()->withErrors(['workPriceLabel' => __('alerts.saldo_tidak_cukup_untuk_pekerjaan')])->withInput();
        }

        $user->balance -= $jobCost;
        $user->locked_balance += $escrowAmount;
        $user->save();

        activity()
            ->inLog('Finance')
            ->on($user)
            ->causedBy($user)
            ->withProperties(['amount' => $escrowAmount, 'request_title' => $request->workTitleLabel])
            ->log("Dana dari {$user->first_name} sebesar Rp" . number_format($escrowAmount) . " telah ditahan untuk pekerjaan baru '{$request->workTitleLabel}'.");


        //from handling post
        $workRequest->title = $request->workTitleLabel;
        $workRequest->slug = Str::slug($workRequest->title);
        $workRequest->description = $request->workDetailLabel;
        $workRequest->price = $request->workPriceLabel;
        $workRequest->final_price = $request->workPriceLabel;
        $workRequest->service_fee = 2500;
        $workRequest->location = $request->workAddressLabel;
        $workRequest->start_time = $startDatetime; // Carbon instance (UTC)
        $workRequest->end_time = $endDatetime;   // Carbon instance (UTC)

        WalletTransaction::create([
            'user_id' => $user->id,
            'amount' => $jobCost,
            'type' => 'credit',
            'description_id' => 'Penahanan saldo untuk pekerjaan: ' . $workRequest->title,
            'description_en' => 'Balance reserved for the job: ' . $workRequest->title,
        ]);

        $result = $workRequest->save();
        Payment::create([
            'request_id' => $workRequest->id,
            'amount' => $request->workPriceLabel,
            'status' => 'holding',
        ]);
        if ($result) {
            return redirect()->route('job-req.home')->with('custom_success_alert', __('alerts.pekerjaan_berhasil_dibuat'));
        } else {
            return back()->with('custom_error_alert', __('alerts.terjadi_kesalahan'));
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

        // NEW: Prevent editing if the original start_time has passed (UTC comparison)
        if (Carbon::parse($workRequest->start_time)->isPast('UTC')) {
            activity()
                ->inLog('Security')
                ->on($workRequest)
                ->causedBy(Auth::user())
                ->log("Percobaan akses tidak sah ke halaman edit pekerjaan '{$workRequest->id}'. Pekerjaan sudah dimulai/lewat waktu.");
            return redirect()->route('job-req.home')->with('custom_error_alert', __('alerts.pekerjaan_sudah_dimulai'));
        }

        if (Auth::id() !== $workRequest->requester_id) {
            // Buat log keamanan
            activity()
                ->inLog('Security')
                ->on($workRequest) // Targetnya adalah request yang coba diakses
                ->causedBy(Auth::user()) // Pelakunya adalah user yang mencoba
                ->log("Percobaan akses tidak sah ke halaman edit pekerjaan '{$workRequest->id}'.");

            // Alihkan dengan pesan error
            return redirect()->route('job-req.home')->with('custom_error_alert', __('alerts.anda_tidak_berwenang'));
        }
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

                // 3. Prevent editing if the original start_time has passed (UTC comparison)
                if (Carbon::parse($workRequest->start_time)->isPast('UTC')) {
                    throw new \Exception('Pekerjaan ini sudah dimulai atau telah melewati waktu mulai (UTC) dan tidak dapat diubah.');
                }

                // Existing validation for status: Don't allow editing if not 'open'
                if ($workRequest->status !== 'open') {
                    throw new \Exception('Pekerjaan yang sudah berjalan tidak dapat diubah.');
                }

                // Validate new inputs for the update
                $request->validate([
                    'workTitleLabel' => 'required|string|max:255',
                    'workDetailLabel' => 'required|string',
                    'workPriceLabel' => 'required|numeric|min:5000',
                    'workAddressLabel' => 'required|string',
                    'workStartDateLabel' => 'required|date',
                    'workEndDateLabel' => 'required|date',
                    'workStartTimeLabel' => 'required',
                    'workEndTimeLabel' => 'required',
                ]);


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
                        'description_id' => 'Penambahan saldo ditahan untuk perubahan harga pada: ' . $workRequest->title,
                        'description_en' => 'Extra balance on hold for price update on: ' . $workRequest->title,
                    ]);
                    activity()->inLog('Finance')->causedBy($user)->on($user)
                        ->log("Dana tambahan sebesar Rp" . number_format($priceDifference) . " ditahan dari {$user->first_name} karena perubahan harga.");
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
                        'description_id' => 'Pengembalian saldo ditahan untuk perubahan harga pada: ' . $workRequest->title,
                        'description_en' => 'Balance refund on hold due to price adjustment on: ' . $workRequest->title,
                    ]);
                    activity()->inLog('Finance')->causedBy($user)->on($user)
                        ->log("Dana sebesar Rp" . number_format($refundAmount) . " dikembalikan ke {$user->first_name} karena perubahan harga.");
                }

                // 5. Update Detail Pekerjaan (Request)
                // NEW: Parse updated input strings directly as UTC
                $startDateTimeString = "{$request->workStartDateLabel} {$request->workStartTimeLabel}";
                $endDateTimeString = "{$request->workEndDateLabel} {$request->workEndTimeLabel}";

                $startDatetime = Carbon::createFromFormat('Y-m-d H:i', $startDateTimeString, 'UTC');
                $endDatetime = Carbon::createFromFormat('Y-m-d H:i', $endDateTimeString, 'UTC');

                // NEW: The *updated* start time must not be in the past (UTC comparison)
                if ($startDatetime->isPast('UTC')) {
                    throw new \Exception('Waktu mulai yang diperbarui tidak boleh di masa lalu (UTC).');
                }

                if ($startDatetime->gte($endDatetime)) {
                    throw new \Exception('Waktu mulai harus sebelum waktu selesai.');
                }

                $workRequest->title = $request->workTitleLabel;
                $workRequest->slug = Str::slug($workRequest->title) . '-' . $workRequest->id; // Buat slug unik
                $workRequest->description = $request->workDetailLabel;
                $workRequest->price = $newPrice;
                $workRequest->final_price = $newPrice; // Update juga final_amount
                $workRequest->location = $request->workAddressLabel;
                $workRequest->start_time = $startDatetime; // Carbon instance (UTC)
                $workRequest->end_time = $endDatetime;   // Carbon instance (UTC)
                $workRequest->save();

                // 6. Update Catatan Escrow (Payment)
                $workRequest->payment->update(['amount' => $newPrice]);
            });
        } catch (\Exception $e) {
            return back()->with('custom_error_alert', $e->getMessage())->withInput();
        }

        // 7. Redirect jika berhasil
        return redirect()->route('job-req.home')->with('custom_success_alert', __('alerts.pekerjaan_berhasil_diperbarui'));
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
                    return back()->with('custom_error_alert', __('alerts.anda_tidak_berwenang'));
                }

                // 2. Validasi: Jangan biarkan request dihapus jika sudah ada offer diterima atau sedang berjalan
                // This logic is important to prevent deletion of active/completed jobs.
                if ($workRequest->status !== 'open') {
                    throw new \Exception('Pekerjaan yang sedang berjalan atau sudah selesai tidak dapat dibatalkan.');
                }

                // Note: Since the Request model uses SoftDeletes, calling ->delete() here will
                // always perform a soft delete, which fulfills the requirement for
                // requests with times that have passed.

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
                    'description_id' => 'Pengembalian saldo dari pembatalan pekerjaan: ' . $workRequest->title,
                    'description_en' => 'Balance refund from job cancellation: ' . $workRequest->title,
                ]);

                activity()
                    ->inLog('Finance') // Kelompokkan ke log 'Finance'
                    ->on($workRequest) // Targetnya adalah request yang dihapus
                    ->causedBy($user)  // Pelakunya adalah user yang menghapus
                    ->withProperties(['amount' => $refundAmount, 'refunded_to' => $user->id])
                    ->log("{$user->first_name} telah membatalkan pekerjaan '{$workRequest->title}', dan dana sebesar Rp" . number_format($refundAmount) . " telah dikembalikan.");

                // 6. Update status terkait
                $workRequest->payment->update(['status' => 'refunded_to_requester']); // Update status escrow
                $workRequest->chatRooms()->update(['is_open' => false]); // Tutup chat room

                // 7. Hapus request (Soft Delete cara Laravel)
                $workRequest->delete();

                // Pass refund amount to session for display in custom alert
                session()->flash('refund_amount', $refundAmount);
            });
        } catch (\Exception $e) {
            return back()->with('custom_error_alert', $e->getMessage());
        }

        // 8. Jika semua berhasil, redirect dengan pesan sukses
        $refundAmount = session('refund_amount', 0); // Get the flashed amount, default to 0
        $formattedRefundAmount = 'Rp' . number_format($refundAmount, 0, ',', '.');
        return redirect()->route('job-req.home')->with('custom_success_alert', __('alerts.pekerjaan_dibatalkan_refund', ['amount' => $formattedRefundAmount]));
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

        $validator->after(function ($validator) use ($request) {
            $startDate = $request->input('workStartDateLabel');
            $startTime = $request->input('workStartTimeLabel');
            $endDate = $request->input('workEndDateLabel');
            $endTime = $request->input('workEndTimeLabel');

            // HANYA jalankan validasi perbandingan jika SEMUA field sudah diisi
            if ($startDate && $startTime && $endDate && $endTime) {
                // NEW: Parse as UTC for validation
                $startDateTime = Carbon::createFromFormat('Y-m-d H:i', "{$startDate} {$startTime}", 'UTC');
                $endDateTime = Carbon::createFromFormat('Y-m-d H:i', "{$endDate} {$endTime}", 'UTC');

                // NEW: Check if start_time is in the past (UTC comparison)
                if ($startDateTime->isPast('UTC')) {
                    $validator->errors()->add(
                        'workStartDateLabel',
                        'Waktu mulai tidak boleh di masa lalu (UTC).'
                    );
                }

                // Ensure start time is strictly before end time (UTC comparison)
                if ($startDateTime->gte($endDateTime)) { // gte = greater than or equal
                    $validator->errors()->add(
                        'datetime',
                        __('validation.custom.datetime.after_start_time') // Assuming this exists for custom validation messages
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
