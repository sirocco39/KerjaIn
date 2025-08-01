<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Models\VerificationRequest; 

class PreventReRegistration
{
    public function handle(Request $request, Closure $next): Response
    {
        
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        
        if ($user->is_worker) {
            
            activity()
                ->inLog('Security')
                ->causedBy($user)
                ->log('Pekerja yang sudah terverifikasi mencoba mengakses kembali halaman pendaftaran.');
            

            return redirect()->route('job-taker.home')->with('custom_error_alert', __('alerts.sudah_jadi_pekerja'));
        }

        
        $hasPendingOrApprovedRequest = VerificationRequest::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        if ($hasPendingOrApprovedRequest) {
            
            if (!$request->routeIs('worker.register.pending') && !$request->routeIs('worker.register.success')) {
                activity()
                    ->inLog('Security')
                    ->causedBy($user)
                    ->log('Pengguna dengan verifikasi pending/approved mencoba mengakses kembali halaman pendaftaran.');

                return redirect()->route('worker.register.pending')->with('custom_info_alert', __('alerts.verifikasi_diproses'));
            }
        }

        
        return $next($request);
    }
}
