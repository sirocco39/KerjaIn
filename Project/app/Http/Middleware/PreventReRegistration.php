<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Models\VerificationRequest; // <-- Jangan lupa import

class PreventReRegistration
{
    public function handle(Request $request, Closure $next): Response
    {
        // Pastikan pengguna sudah login untuk melakukan pengecekan
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        // Kondisi 1: Cek apakah pengguna sudah menjadi pekerja
        if ($user->is_worker) {
            // --- 👇 TAMBAHKAN LOGGING DI SINI 👇 ---
            activity()
                ->inLog('Security')
                ->causedBy($user)
                ->log('Pekerja yang sudah terverifikasi mencoba mengakses kembali halaman pendaftaran.');
            // --- 👆 BATAS PENAMBAHAN KODE 👆 ---

            return redirect()->route('job-taker.home')->with('custom_error_alert', __('alerts.sudah_jadi_pekerja'));
        }

        // Kondisi 2: Cek apakah ada permintaan verifikasi yang sedang diproses atau sudah disetujui
        $hasPendingOrApprovedRequest = VerificationRequest::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        if ($hasPendingOrApprovedRequest) {
            // --- 👇 TAMBAHKAN LOGGING DI SINI JUGA 👇 ---
            if (!$request->routeIs('worker.register.pending') && !$request->routeIs('worker.register.success')) {
                activity()
                    ->inLog('Security')
                    ->causedBy($user)
                    ->log('Pengguna dengan verifikasi pending/approved mencoba mengakses kembali halaman pendaftaran.');

                return redirect()->route('worker.register.pending')->with('custom_info_alert', __('alerts.verifikasi_diproses'));
            }
        }

        // Jika semua kondisi tidak terpenuhi, izinkan akses ke halaman pendaftaran
        return $next($request);
    }
}
