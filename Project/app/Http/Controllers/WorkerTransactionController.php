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
use Illuminate\Support\Facades\Auth;





class WorkerTransactionController extends Controller
{

    public function show($id)
    {
        // Ambil data transaction berdasarkan ID
        $transaction = Transaction::findOrFail($id);

        $request = JobRequest::findOrFail($transaction->request_id);

        // Ambil pekerja yang melakukan pekerjaan berdasarkan relasi
        $worker = $transaction->worker; // Pastikan relasi sudah ada di model Transaction

        // Generate nomor pesanan random (misalnya 12 digit)
        $orderNumber = '#' . str_pad(rand(0, 999999999999), 12, '0', STR_PAD_LEFT);

        // Ambil completion proof terkait
        $completionProof = $transaction->completionProof;

        // Kirim ke view
        return view('Job_Taker.accepted-work-request', compact('transaction', 'request', 'worker', 'orderNumber', 'completionProof'));
    }

    public function startWork($id, Request $request)
    {
        $transaction = Transaction::findOrFail($id);

        // Update status menjadi in_progress
        $transaction->status = 'in progress';
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
        $transaction->status = 'submitted';
        $transaction->save();

        // Update submitted_at pada completion_proofs yang terkait
        $completionProof = CompletionProof::where('transaction_id', $transaction->id)->first();

        if ($completionProof) {
            $completionProof->submitted_at = Carbon::now();
            $completionProof->save();
        }

        // Ambil data yang dibutuhkan untuk pop-up rating
        $job = Request::find($transaction->job->request_id);
        $requester = $transaction->job->requester;

        // Generate nomor pesanan
        $orderNumber = '#' . Str::random(12);

        // Kirim data ke view via session flash
        return back()->with([
            'show_rating_modal' => true,
            'rating_data' => [
                'title' => $job->title,
                'order_number' => $orderNumber,
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

    public function finishWork(Request $request, Transaction $transaction)
    {
        $request->validate([
            'photo' => 'required|image|max:2048',
            'note' => 'nullable|string|max:500',
        ]);

        // Simpan foto ke storage
        $path = $request->file('photo')->store('report_photos', 'public');
        $photoUrl = Storage::url($path);

        // Simpan ke tabel reports
        Report::create([
            'transaction_id' => $transaction->id,
            'reporter_id' => Auth::id(),
            'reported_id' => $transaction->request->user_id, // requester sebagai reported
            'reasons' => $request->note ?? '-',
            'photo_url' => $photoUrl,
            'status' => 'submitted',
        ]);

        // Update status transaction
        $transaction->status = 'submitted';
        $transaction->save();

        return back()->with('success', 'Pekerjaan berhasil diselesaikan dan laporan telah dikirim.');
    }

    public function storeReport(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|exists:transactions,id',
            'reporter_id' => 'required|exists:users,id',
            'reported_id' => 'required|exists:users,id',
            'reasons' => 'required|string',
            'photo' => 'required|image|max:2048',

        ]);

        // Simpan foto ke storage
        $path = $request->file('photo')->store('report_photos', 'public');
        $photoUrls = Storage::url($path);

        Report::create([
            'transaction_id' => $request->transaction_id,
            'reporter_id' => $request->reporter_id,
            'reported_id' => $request->reported_id,
            'reasons' => $request->reasons,
            'photo_url' => $photoUrls,
            'status' => 'Not Reviewed',
        ]);

        return back()->with('success', 'Pekerjaan berhasil diselesaikan dan laporan telah dikirim.');


    }
}
