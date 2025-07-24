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
        // Cek apakah pengguna sudah login DAN status 'is_worker'-nya true
        if (Auth::check() && Auth::user()->is_worker) {
            // Jika ya, izinkan akses ke halaman berikutnya
            return $next($request);
        }

        // Jika tidak, buat log keamanan
        activity()
            ->inLog('Security')
            ->causedBy(Auth::user()) // Pelakunya adalah user yang mencoba akses
            ->log("Percobaan akses tidak sah ke halaman khusus pekerja.");

        // Alihkan pengguna dengan pesan error
        return redirect('/')->with('custom_error_alert', 'Anda harus terverifikasi sebagai pekerja untuk mengakses halaman ini.');
    }
}