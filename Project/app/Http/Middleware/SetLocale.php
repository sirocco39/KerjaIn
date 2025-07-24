<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah ada session 'locale'
        if (session()->has('locale')) {
            // Atur bahasa aplikasi sesuai session
            App::setLocale(session()->get('locale'));
        }

        return $next($request);
    }
}