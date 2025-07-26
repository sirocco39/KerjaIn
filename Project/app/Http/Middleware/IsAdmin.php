<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->role === 'admin') {
            return $next($request);
        }

        // Redirect atau abort jika bukan admin
        // Anda bisa pilih antara 403 Forbidden, redirect ke home, atau redirect ke halaman login
        return redirect()->route('landing')->with('error', 'Anda tidak memiliki akses sebagai administrator.');
        // abort(403, 'Unauthorized access.'); // Atau ini
    }
}
