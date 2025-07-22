<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): RedirectResponse
    {
        // Flash a session variable to indicate that the login modal should be shown
        session()->flash('loginModal', true);
        // Redirect to the landing page using its named route
        return redirect()->route('landing');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request)
    {
        // Manual validation
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Attempt login
        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            if ($request->expectsJson()) {
                return response()->json([
                    'errors' => [
                        'password' => ['Email atau kata sandi salah.'],
                    ]
                ], 422);
            }

            // Changed to custom alert
            return back()->with('custom_error_alert', 'Email atau kata sandi salah.')->onlyInput('email');
        }


        // Login success
        $request->session()->regenerate();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Login berhasil!',
                'redirect_url' => route('job-req.beranda') // Changed redirect URL for JSON response
            ]);
        }

        // Changed to custom alert
        return redirect()->route('job-req.beranda')->with('custom_success_alert', 'Login berhasil! Selamat datang di aplikasi kami.');
    }


    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        // Changed to custom alert
        return redirect('/')->with('custom_info_alert', 'Anda telah berhasil keluar.');
    }
}
