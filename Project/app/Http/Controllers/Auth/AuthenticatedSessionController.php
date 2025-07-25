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

        activity()
            ->inLog('Authentication') // Mengelompokkan log ke kategori 'Authentication'
            ->causedBy(Auth::user())  // Pelakunya adalah user yang baru saja login
            ->log('User telah login menggunakan email dan password');

        // Get the authenticated user's first name
        $firstName = Auth::user()->first_name ?? 'Pengguna';

        if ($request->expectsJson()) {
            return response()->json([
                'message' => "Login berhasil! Selamat datang, {$firstName}!",
                'redirect_url' => route('job-req.beranda') // Changed redirect URL for JSON response
            ]);
        }

        // Changed to custom alert
        return redirect()->route('job-req.beranda')->with('custom_info_alert', "Login berhasil! Selamat datang, {$firstName}!");
    }


    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {

        $user = Auth::user();
        activity()
            ->inLog('Authentication')
            ->causedBy($user)
            ->log('User telah logout');

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('custom_info_alert', 'Anda telah berhasil keluar.');
    }
}
