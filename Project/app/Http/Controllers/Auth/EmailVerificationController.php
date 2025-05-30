<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class EmailVerificationController extends Controller
{
    public function verify(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'otp' => ['required', 'digits:6'],
        ]);

        $pending = session('pending_registration');

        if (!$pending) {
            throw ValidationException::withMessages([
                'otp' => 'No pending registration found. Please register again.',
            ]);
        }

        if ($pending['email'] !== $request->email) {
            throw ValidationException::withMessages([
                'email' => 'Email does not match registration data.',
            ]);
        }

        if (now()->greaterThan($pending['expires_at'])) {
            session()->forget('pending_registration');
            throw ValidationException::withMessages([
                'otp' => 'OTP expired. Please register again.',
            ]);
        }

        if (!Hash::check($request->otp, $pending['otp'])) {
            throw ValidationException::withMessages([
                'otp' => 'Invalid OTP.',
            ]);
        }

        // Create the user now
        $user = User::create([
            'first_name' => $pending['first_name'],
            'last_name' => $pending['last_name'],
            'email' => $pending['email'],
            'password' => $pending['password'], // already hashed
        ]);

        // Clear session
        session()->forget('pending_registration');

        // Log in user and redirect
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }
}
