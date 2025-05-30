<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use App\Notifications\VerifyEmailOtp;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => ['required','string','max:255'],
            'last_name'  => ['required','string','max:255'],
            'email'      => ['required','string','email','max:255','unique:users'],
            'password'   => ['required','confirmed',Rules\Password::defaults()],
        ]);

        // Generate OTP
        $otp = random_int(100000, 999999);

        // Store user registration data + hashed OTP + expiration in session
        session([
            'pending_registration' => [
                'first_name' => $request->first_name,
                'last_name'  => $request->last_name,
                'email'      => $request->email,
                'password'   => Hash::make($request->password),
                'otp'        => Hash::make($otp),
                'expires_at' => now()->addMinutes(10),
            ]
        ]);

        // Send OTP notification to the email provided
        // Use a temporary User model instance for notification
        $tempUser = new \App\Models\User([
            'email' => $request->email,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
        ]);
        $tempUser->notify(new VerifyEmailOtp($otp));

        // Redirect to OTP verification page
        return redirect()->route('verification.notice')
                         ->with('email', $request->email);
    }
}
