<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserVerification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class EmailVerificationController extends Controller
{
    public function verify(Request $request)
    {
        $request->validate([
            'email' => ['required','email','exists:users'],
            'otp'   => ['required','digits:6'],
        ]);

        $user = User::where('email', $request->email)->first();

        $record = UserVerification::where('user_id', $user->id)->latest()->first();

        if (!$record
            || Carbon::now()->greaterThan($record->expires_at)
            || !Hash::check($request->otp, $record->code)) {
            throw ValidationException::withMessages([
                'otp' => 'Kode salah atau sudah kadaluarsa.',
            ]);
        }

        $user->email_verified_at = now();
        $user->save();
        $record->delete();

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
