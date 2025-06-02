<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\SendOtpMail;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    // ─────────────────────────────────────────────────────────────────────────────
    // Configuration
    // ─────────────────────────────────────────────────────────────────────────────
    private const OTP_COOLDOWN_SECONDS = 60;   // user must wait 60 s before next OTP
    private const OTP_LIFETIME_MINUTES = 5;   // OTP valid for 5 minutes


    // ─────────────────────────────────────────────────────────────────────────────
    // Show the registration page
    // ─────────────────────────────────────────────────────────────────────────────
    public function create(): View
    {
        return view('auth.register');
    }


    // ─────────────────────────────────────────────────────────────────────────────
    // AJAX endpoint: send OTP
    // ─────────────────────────────────────────────────────────────────────────────
    public function sendOtp(Request $request)
    {
        // 1) Basic email validation (unique among users)
        $request->validate([
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
        ]);

        // 2) Throttle by session
        $lastSent = $request->session()->get('otp_data.sent_at');
        if ($lastSent && now()->diffInSeconds($lastSent) < self::OTP_COOLDOWN_SECONDS) {
            $remaining = self::OTP_COOLDOWN_SECONDS - now()->diffInSeconds($lastSent);

            return response()->json([
                'message' => "Tunggu $remaining detik sebelum meminta OTP lagi."
            ], 429); // HTTP 429 = Too Many Requests
        }

        // 3) Generate a 6-digit OTP (keeps leading zeros)
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // 4) Persist OTP data in the session
        $request->session()->put('otp_data', [
            'email'      => $request->email,
            'otp'        => $otp,
            'sent_at'    => now(),
            'expires_at' => now()->addMinutes(self::OTP_LIFETIME_MINUTES),
        ]);

        // 5) Send the e-mail
        Mail::to($request->email)->send(new SendOtpMail($otp));

        return response()->json(['message' => 'OTP berhasil dikirim.']);
    }


    // ─────────────────────────────────────────────────────────────────────────────
    // Final registration
    // ─────────────────────────────────────────────────────────────────────────────
    public function store(Request $request): RedirectResponse
    {
        // 1) Validate all fields including OTP
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name'  => ['required', 'string', 'max:255'],
            'email'      => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password'   => ['required', 'confirmed', Rules\Password::defaults()],
            'otp'        => ['required', 'digits:6'],
        ]);

        // 2) Retrieve OTP data from session
        $otpData = $request->session()->get('otp_data');

        // a) Was an OTP ever sent?
        if (!$otpData) {
            return back()->withErrors(['otp' => 'OTP belum diminta.'])->withInput();
        }

        // b) Does the e-mail match the one that requested the OTP?
        if ($otpData['email'] !== $request->email) {
            return back()->withErrors(['email' => 'Email berubah; silakan minta OTP lagi.'])
                         ->withInput();
        }

        // c) Has the OTP expired?
        if (now()->greaterThan($otpData['expires_at'])) {
            return back()->withErrors(['otp' => 'OTP sudah kedaluwarsa.'])->withInput();
        }

        // d) Does the OTP match?
        if ($otpData['otp'] !== $request->otp) {
            return back()->withErrors(['otp' => 'OTP tidak valid.'])->withInput();
        }

        // 3) Create the user
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
        ]);

        // 4) Clean the session & log the user in
        $request->session()->forget('otp_data');
        event(new Registered($user));
        Auth::login($user);

        // 5) Redirect wherever you like, e.g. dashboard
        return redirect()->route('dashboard');
    }
}
