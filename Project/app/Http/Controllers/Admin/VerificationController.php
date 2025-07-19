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
     * Menampilkan daftar permintaan verifikasi berdasarkan status dan kueri pencarian.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $status (opsional)
     * @return \Illuminate\View\View
     */
    public function index(Request $request, $status = 'pending') // Default ke 'pending'
    {
        $search = $request->query('search'); // Dapatkan kueri pencarian dari request

        $verificationRequestsQuery = VerificationRequest::query(); // Mulai instance query builder baru

        // Terapkan filter pencarian terlebih dahulu jika ada
        if ($search) {
            $verificationRequestsQuery->where(function ($query) use ($search) {
                $query->where('id', 'like', '%' . $search . '%')
                    ->orWhere('nik', 'like', '%' . $search . '%')
                    // Cari berdasarkan first_name dan last_name yang digabungkan
                    ->orWhere(DB::raw('CONCAT(first_name, " ", last_name)'), 'like', '%' . $search . '%');
            });
            // Jika ada pencarian, tampilkan semua status, jadi tidak perlu filter status di sini.
            // Namun, kita tetap perlu mengurutkan.
            $verificationRequestsQuery->orderBy('created_at', 'asc');
        } else {
            // Jika tidak ada pencarian, terapkan filter status seperti biasa
            switch ($status) {
                case 'pending':
                    $verificationRequestsQuery->where('status', 'pending')
                        ->orderBy('created_at', 'asc');
                    break;
                case 'approved':
                    $verificationRequestsQuery->where('status', 'approved')
                        ->orderBy('verified_at', 'asc'); // Urutkan berdasarkan verified_at untuk permintaan yang disetujui
                    break;
                case 'rejected':
                    $verificationRequestsQuery->where('status', 'rejected')
                        ->orderBy('updated_at', 'asc'); // Urutkan berdasarkan updated_at untuk permintaan yang ditolak
                    break;
                default:
                    // Fallback atau tampilkan semua jika status tidak valid
                    $verificationRequestsQuery->orderBy('created_at', 'asc');
                    break;
            }
        }

        $verificationRequests = $verificationRequestsQuery->get(); // Jalankan kueri

        $pendingVerificationsCount = VerificationRequest::where('status', 'pending')->count();

        // Kirim istilah pencarian kembali ke tampilan untuk mengisi bilah pencarian
        return view('admin.verifications.index', compact('verificationRequests', 'status', 'pendingVerificationsCount', 'search'));
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

        // Dapatkan semua permintaan verifikasi dengan status yang sama untuk navigasi (dropdown, prev/next)
        $sameStatusRequests = VerificationRequest::where('status', $verificationRequest->status)
            ->orderBy('created_at', 'asc') // Urutkan untuk navigasi yang konsisten
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

        // Kirim istilah pencarian (jika ada) ke tampilan show juga
        $search = $request->query('search');

        return view('admin.verifications.show', compact('verificationRequest', 'search', 'sameStatusRequests', 'previousRequest', 'nextRequest'));
    }

    /**
     * Endpoint AJAX untuk pencarian rekomendasi pengguna.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
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
                ->limit(10) // Batasi jumlah rekomendasi
                ->get();
        }

        return response()->json($results);
    }

    /**
     * Menyetujui permintaan verifikasi.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
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
            $verificationRequest->verified_at = now();
            $verificationRequest->save();

            $user = User::find($verificationRequest->user_id);
            if ($user) {
                $user->is_worker = 1;
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

        $verificationRequest->status = 'rejected';
        $verificationRequest->rejection_reason = $request->rejection_reason;
        $verificationRequest->save();

        return redirect()->route('admin.verifications.show', ['status' => 'rejected'])
            ->with('success', 'Permintaan verifikasi berhasil ditolak. Alasan: ' . $request->rejection_reason);
    }
}
