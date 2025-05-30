<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Notifications\VerifyEmailOtp;
use Illuminate\View\View;
use Illuminate\Auth\Events\Verified;

class AuthenticatedSessionController extends Controller
{
    // Show login form (handled by Breeze)
    public function create(): View
    {
        return view('auth.login');
    }

    // Handle login request (email + password), send OTP, redirect to OTP form
    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ]);
        }

        // Generate and store OTP hash + expiry
        $otp = random_int(100000, 999999);

        UserVerification::updateOrCreate(
            ['user_id' => $user->id],
            [
                'code' => Hash::make($otp),
                'expires_at' => now()->addMinutes(10),
            ]
        );

        // Send OTP notification (email, sms, etc)
        $user->notify(new VerifyEmailOtp($otp));

        // Store user id & email in session for OTP verification step
        session(['otp_user_id' => $user->id, 'otp_email' => $user->email]);

        // Redirect to OTP verification form
        return redirect()->route('login.otp');
    }

    // Show OTP verification form
    public function showOtpForm(): View
    {
        // Pass email from session to prefill or show message
        $email = session('otp_email');
        return view('auth.verify-otp', compact('email'));
    }

    // Handle OTP verification
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'digits:6'],
        ]);

        $userId = session('otp_user_id');
        $email = session('otp_email');

        if (!$userId || !$email) {
            return redirect()->route('login')->withErrors(['otp' => 'Session expired, please login again.']);
        }

        $user = User::find($userId);

        if (!$user || $user->email !== $email) {
            return redirect()->route('login')->withErrors(['otp' => 'User not found, please login again.']);
        }

        $record = UserVerification::where('user_id', $user->id)->first();

        if ($record && Hash::check($request->otp, $record->code) && $record->expires_at->isFuture()) {
            Auth::login($user);

            // Mark email as verified if not verified
            if (! $user->hasVerifiedEmail()) {
                $user->markEmailAsVerified();
                event(new Verified($user));
            }

            session()->forget(['otp_user_id', 'otp_email']);

            // Redirect with verified=1 like in VerifyEmailController
            return redirect()->intended(route('dashboard', absolute: false) . '?verified=1');
        }

        return back()->withErrors(['otp' => 'Invalid or expired code.']);
    }

    // Logout user
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
