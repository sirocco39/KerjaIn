<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VerificationController extends Controller
{
    /**
     * Display a list of verification requests based on status.
     *
     * @param  string  $status  (optional)
     * @return \Illuminate\View\View
     */
    public function index($status = 'pending') // Default to 'pending'
    {
        $verificationRequests = collect(); // Initialize an empty collection

        switch ($status) {
            case 'pending':
                $verificationRequests = VerificationRequest::where('status', 'pending') // [cite: 82]
                    ->orderBy('created_at', 'asc')
                    ->get();
                break;
            case 'approved':
                $verificationRequests = VerificationRequest::where('status', 'approved') // [cite: 82]
                    ->orderBy('verified_at', 'asc') // Order by verified_at for approved requests
                    ->get();
                break;
            case 'rejected':
                $verificationRequests = VerificationRequest::where('status', 'rejected') // [cite: 82]
                    ->orderBy('updated_at', 'asc') // Order by updated_at for rejected requests
                    ->get();
                break;
            default:
                // Fallback or show all if status is invalid
                $verificationRequests = VerificationRequest::orderBy('created_at', 'asc')
                    ->get();
                break;
        }
        $pendingVerificationsCount = VerificationRequest::where('status', 'pending')->count();


        return view('admin.verification.index', compact('verificationRequests', 'status', 'pendingVerificationsCount'));
    }

    /**
     * Display the details of a specific verification request.
     *
     * @param  int  $id
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show($id)
    {
        $user = User::all();
        $verificationRequest = VerificationRequest::with('user')->find($id);

        if (!$verificationRequest) {
            return redirect()->route('admin.verification.index')->with('error', 'Verification request not found.');
        }
        //  
        //dd($verificationRequest->user);

        return view('admin.verification.show', compact('verificationRequest'));
    }

    /**
     * Approve a verification request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $verificationRequest = VerificationRequest::find($id); // [cite: 82]

            if (!$verificationRequest) {
                return redirect()->route('admin.verifications.show')->with('error', 'Verification request not found.');
            }

            if ($verificationRequest->status !== 'pending') { // [cite: 82]
                return redirect()->route('admin.verifications.show', $id)->with('error', 'Verification request has already been processed.');
            }

            $verificationRequest->status = 'approved'; // [cite: 82]
            $verificationRequest->verified_at = now(); // [cite: 82]
            $verificationRequest->save();

            $user = User::find($verificationRequest->user_id); // [cite: 76, 82]
            if ($user) {
                $user->is_worker = 1; // [cite: 77]
                $user->save();
            } else {
                Log::warning("User with ID {$verificationRequest->user_id} not found for verification request {$id}.");
            }

            DB::commit();
            return redirect()->route('verifications.show', $id)->with('success', 'Verification request approved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error approving verification request {$id}: " . $e->getMessage());
            return redirect()->route('verifications.show', $id)->with('error', 'Failed to approve verification request.');
        }
    }

    /**
     * Reject a verification request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ], [
            'rejection_reason.required' => 'Alasan penolakan tidak boleh kosong.',
            'rejection_reason.max' => 'Alasan penolakan tidak boleh lebih dari 500 karakter.',
        ]);

        $verificationRequest = VerificationRequest::findOrFail($id);

        if ($verificationRequest->status !== 'pending') {
            return redirect()->back()->with('error', 'Permintaan verifikasi ini sudah tidak dalam status "pending".');
        }

        // Update status verifikasi
        $verificationRequest->status = 'rejected';
        $verificationRequest->rejection_reason = $request->rejection_reason; // Asumsi ada kolom 'rejection_reason'
        $verificationRequest->save();

        return redirect()->route('admin.verifications.show', ['status' => 'rejected'])
            ->with('success', 'Permintaan verifikasi berhasil ditolak. Alasan: ' . $request->rejection_reason);
    }
}
