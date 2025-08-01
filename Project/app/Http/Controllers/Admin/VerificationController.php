<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon; 

class VerificationController extends Controller
{
    /**
     * Menampilkan daftar permintaan verifikasi berdasarkan status dan kueri pencarian.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $status (opsional)
     * @return \Illuminate\View\View
     */
    public function index(Request $request, $status = 'pending')
    {
        $search = $request->query('search');

        $verificationRequestsQuery = VerificationRequest::query();

        if ($search) {
            $verificationRequestsQuery->where(function ($query) use ($search) {
                $query->where('id', 'like', '%' . $search . '%')
                    ->orWhere('nik', 'like', '%' . $search . '%')
                    ->orWhere(DB::raw('CONCAT(first_name, " ", last_name)'), 'like', '%' . $search . '%');
            });
            $verificationRequestsQuery->orderBy('created_at', 'asc');
        } else {
            switch ($status) {
                case 'pending':
                    $verificationRequestsQuery->where('status', 'pending')
                        ->orderBy('created_at', 'asc');
                    break;
                case 'approved':
                    $verificationRequestsQuery->where('status', 'approved')
                        ->orderBy('verified_at', 'asc');
                    break;
                case 'rejected':
                    $verificationRequestsQuery->where('status', 'rejected')
                        ->orderBy('updated_at', 'asc');
                    break;
                default:
                    $verificationRequestsQuery->orderBy('created_at', 'asc');
                    break;
            }
        }

        $verificationRequests = $verificationRequestsQuery->get();

        $pendingVerificationsCount = VerificationRequest::where('status', 'pending')->count();

        
        $breadcrumbs = [
            'mainPageTitle' => 'Admin',
            'currentPageTitle' => 'Verifikasi ' . ucfirst($status), 
            'filterStatus' => $status,
        ];

        return view('admin.verifications.index', compact('verificationRequests', 'status', 'pendingVerificationsCount', 'search', 'breadcrumbs'));
    }

    /**
     * Menampilkan detail permintaan verifikasi tertentu.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show(Request $request, $id)
    {
        $verificationRequest = VerificationRequest::with('user')->find($id);

        if (!$verificationRequest) {
            return redirect()->route('admin.verifications.index')->with('error', 'Permintaan verifikasi tidak ditemukan.');
        }

        $sameStatusRequests = VerificationRequest::where('status', $verificationRequest->status)
            ->orderBy('created_at', 'asc')
            ->get();

        $currentIndex = $sameStatusRequests->search(function ($item) use ($id) {
            return $item->id == $id;
        });

        $previousRequest = null;
        $nextRequest = null;

        if ($currentIndex !== false) {
            if ($currentIndex > 0) {
                $previousRequest = $sameStatusRequests->get($currentIndex - 1);
            }
            if ($currentIndex < $sameStatusRequests->count() - 1) {
                $nextRequest = $sameStatusRequests->get($currentIndex + 1);
            }
        }

        $search = $request->query('search');

        
        $breadcrumbs = [
            'mainPageTitle' => 'Admin',
            'currentPageTitle' => 'Verifikasi ' . ucfirst($verificationRequest->status), 
            'filterStatus' => $verificationRequest->status,
            'currentSectionTitle' => 'Detail Verifikasi',
            'requestId' => $verificationRequest->id,
        ];

        return view('admin.verifications.show', compact('verificationRequest', 'search', 'sameStatusRequests', 'previousRequest', 'nextRequest', 'breadcrumbs'));
    }

    /**
     * Endpoint AJAX untuk pencarian rekomendasi pengguna.
     *
     * (Metode ini tidak merender tampilan, jadi tidak perlu breadcrumbs)
     */
    public function searchUsersForShow(Request $request)
    {
        $query = $request->input('query');
        $results = [];

        if ($query) {
            $results = VerificationRequest::select('id', 'first_name', 'last_name', 'nik', 'status')
                ->where(function ($q) use ($query) {
                    $q->where('id', 'like', '%' . $query . '%')
                        ->orWhere('nik', 'like', '%' . $query . '%')
                        ->orWhere(DB::raw('CONCAT(first_name, " ", last_name)'), 'like', '%' . $query . '%');
                })
                ->limit(10)
                ->get();
        }

        return response()->json($results);
    }

    /**
     * Menyetujui permintaan verifikasi.
     *
     * (Metode ini melakukan redirect, jadi tidak perlu breadcrumbs)
     */
    public function approve(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $verificationRequest = VerificationRequest::find($id);

            if (!$verificationRequest) {
                return redirect()->route('admin.verifications.index')->with('error', 'Permintaan verifikasi tidak ditemukan.');
            }

            if ($verificationRequest->status !== 'pending') {
                return redirect()->route('admin.verifications.show', $id)->with('error', 'Permintaan verifikasi sudah diproses.');
            }

            $verificationRequest->status = 'approved';
            $verificationRequest->verified_at = Carbon::now('UTC'); 
            $verificationRequest->save();


            $user = User::find($verificationRequest->user_id);
            if ($user) {
                $user->is_worker = 1;
                $user->photo_url_worker = $verificationRequest->photo_url;
                $user->save();
            } else {
                Log::warning("Pengguna dengan ID {$verificationRequest->user_id} tidak ditemukan untuk permintaan verifikasi {$id}.");
            }

            DB::commit();
            return redirect()->route('admin.verifications.show', $id)->with('success', 'Permintaan verifikasi berhasil disetujui.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error menyetujui permintaan verifikasi {$id}: " . $e->getMessage());
            return redirect()->route('admin.verifications.show', $id)->with('error', 'Gagal menyetujui permintaan verifikasi.');
        }
    }

    /**
     * Menolak permintaan verifikasi.
     *
     * (Metode ini melakukan redirect, jadi tidak perlu breadcrumbs)
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

        $verificationRequest->status = 'rejected';
        $verificationRequest->rejection_reason = $request->rejection_reason;
        $verificationRequest->save();

        return redirect()->route('admin.verifications.show', ['status' => 'rejected', $id])
            ->with('success', 'Permintaan verifikasi berhasil ditolak. Alasan: ' . $request->rejection_reason);
    }
}
