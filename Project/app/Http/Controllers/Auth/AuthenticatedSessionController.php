<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
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
    public function create(): View
    {
        return view('auth.login');
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

            return back()->withErrors([
                'password' => 'Email atau kata sandi salah.',
            ])->onlyInput('email');
        }


        // Login success
        $request->session()->regenerate();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Login berhasil!',
                'redirect_url' => url('/')
            ]);
        }

        return redirect('/')->with('success', 'Login berhasil! Selamat datang di aplikasi kami.');
    }


    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
