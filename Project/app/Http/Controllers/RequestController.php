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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
            'description' => 'Membuat tawaran pekerjaan dengan judul: ' . $workRequest->title,
        ]);

        // d. Buat catatan di tabel payments untuk escrow

        $result = $workRequest->save();
        Payment::create([
            'request_id' => $workRequest->id,
            'amount' => $jobCost,
            'status' => 'holding',
        ]);
        if ($result) {
            return redirect()->to('/job-req/beranda');
        } else {
            return "request error";
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
        //
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
        $workRequest = RequestModel::where('slug', $slug)->firstOrFail();
        $workRequest->title = $request->workTitleLabel;
        $workRequest->slug = Str::slug($workRequest->title);
        $workRequest->description = $request->workDetailLabel;
        $workRequest->price = $request->workPriceLabel;
        $workRequest->final_price = $request->workPriceLabel;
        $workRequest->location = $request->workAddressLabel;
        $startDatetime = new \DateTime("{$request->workStartDateLabel} {$request->workStartTimeLabel}:00");
        $endDatetime = new \DateTime("{$request->workEndDateLabel} {$request->workEndTimeLabel}:00");
        // Check if start is after end
        if ($startDatetime > $endDatetime) {
            return back()->withErrors([
                'datetime' => 'Start time must not be after end time.'
            ])->withInput();
        }
        $workRequest->start_time = $startDatetime;
        $workRequest->end_time = $endDatetime;
        //updated:
        $workRequest->updated_at = date("Y-m-d h:i:sa", time());

        // now update the request on database
        $result = $workRequest->save();
        if ($result) {
            return redirect()->to('/job-req/beranda');
        } else {
            return "request update error";
        }
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
                    abort(403, 'Unauthorized action.'); // Hentikan jika bukan pemilik
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
                    'type' => 'credit',
                    'description' => 'Pengembalian dana dari pembatalan pekerjaan: ' . $workRequest->title,
                ]);

                // 6. Update status terkait
                $workRequest->payment->update(['status' => 'refunded_to_requester']); // Update status escrow
                $workRequest->chatRooms()->update(['is_open' => false]); // Tutup chat room

                // 7. Hapus request (Soft Delete cara Laravel)
                $workRequest->delete();
            });
        } catch (\Exception $e) {
            // Jika ada error di tengah jalan, kembalikan pesan error
            return back()->with('error', $e->getMessage());
        }

        // 8. Jika semua berhasil, redirect dengan pesan sukses
        return redirect()->route('job-req.beranda')->with('success', 'Pekerjaan berhasil dibatalkan dan dana telah dikembalikan.');
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
}
