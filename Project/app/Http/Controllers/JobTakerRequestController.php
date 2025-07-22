<?php

namespace App\Http\Controllers;

use App\Models\Request as JobRequest;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class JobTakerRequestController extends Controller
{
    public function acceptRequest($id)
    {
        // 1. Ambil data request
        $jobRequest = JobRequest::findOrFail($id);
        // 2. Panggil static function dari model Request
        $transaction = JobRequest::hireAndFinalize($jobRequest, Auth::user()); // Assuming Auth::user() returns the logged-in job taker

        // 3. Periksa apakah transaction berhasil dibuat dan lakukan redirect
        if ($transaction instanceof Transaction) {
            return response()->json([
                'success'      => true,
                'message'      => 'Pekerjaan berhasil diterima.',
                'redirect_url' => route('job-taker.accepted-work-request', $transaction->id)
            ]);
        } else {
            // Handle jika terjadi kesalahan saat membuat transaction
            // Changed to custom alert
            return back()->with('custom_error_alert', 'Terjadi kesalahan saat menerima pekerjaan.');
        }
    }
}
