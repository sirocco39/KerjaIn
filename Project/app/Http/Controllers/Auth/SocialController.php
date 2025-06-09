<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Mail\SendRandomPasswordMail;
use Illuminate\Support\Facades\Mail;

class SocialController extends Controller
{
    public function google_redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function google_callback()
    {
        $googleUser = Socialite::driver('google')->user();

        $user = User::where('email', $googleUser->getEmail())->first();

        if ($user) {
            if (!$user->google_id) {
                $user->google_id = $googleUser->getId();
                $user->save();
            }
        } else {
            // Generate random password (plain)
            $randomPassword = uniqid('pass_', true);

            $user = User::create([
                'first_name' => $googleUser->user['given_name'] ?? $googleUser->getName(),
                'last_name'  => $googleUser->user['family_name'] ?? '',
                'email'      => $googleUser->getEmail(),
                'password'   => Hash::make($randomPassword),
                'google_id'  => $googleUser->getId(),
            ]);

            // // Send the random password email
            // Mail::to($user->email)->send(new SendRandomPasswordMail($randomPassword));

            // // Optionally: show password reset popup
            // Session::flash('showPasswordResetPopup', true);
        }

        Auth::login($user);

        return redirect('/')
            ->with('success', 'Login berhasil! Selamat datang di aplikasi kami.');
    }

}