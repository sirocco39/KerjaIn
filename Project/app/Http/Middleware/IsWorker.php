<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsWorker
{
    public function handle(Request $request, Closure $next): Response
    {
        
        if (Auth::check() && Auth::user()->is_worker) {
            
            return $next($request);
        }

        
        activity()
            ->inLog('Security')
            ->causedBy(Auth::user()) 
            ->log("Percobaan akses tidak sah ke halaman khusus pekerja.");

        
       return redirect('/')->with('custom_error_alert', __('alerts.harus_jadi_pekerja'));
    }
}