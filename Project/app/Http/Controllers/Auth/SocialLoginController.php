<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class SocialLoginController extends Controller
{
    protected $providers = ['google', 'github', 'facebook']; // Add providers you support

    public function redirectToProvider($provider)
    {
        if (!in_array($provider, $this->providers)) {
            return redirect('/login')->withErrors(['provider' => 'Unsupported social provider.']);
        }

        return Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback($provider)
    {
        if (!in_array($provider, $this->providers)) {
            return redirect('/login')->withErrors(['provider' => 'Unsupported social provider.']);
        }

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (Exception $e) {
            // You can log $e->getMessage() for debugging
            return redirect('/login')->withErrors(['provider' => 'Failed to authenticate with ' . ucfirst($provider) . '. Please try again.']);
        }

        // Check if user with this provider_id already exists
        $authUser = User::where('provider_name', $provider)
                        ->where('provider_id', $socialUser->getId())
                        ->first();

        if ($authUser) {
            Auth::login($authUser, true); // Log them in (true for "remember me")
            return redirect()->intended('/dashboard'); // Or your desired redirect path
        }

        // If no user with this provider_id, check by email
        // This is important to link accounts if the user previously signed up with email
        $existingUserByEmail = User::where('email', $socialUser->getEmail())->first();

        if ($existingUserByEmail) {
            // User with this email exists. Link the social account to the existing user.
            $existingUserByEmail->update([
                'provider_name' => $provider,
                'provider_id' => $socialUser->getId(),
                'avatar' => $existingUserByEmail->avatar ?? $socialUser->getAvatar(), // Use existing or update
                'provider_token' => $socialUser->token,
                'provider_refresh_token' => $socialUser->refreshToken,
            ]);
            Auth::login($existingUserByEmail, true);
            return redirect()->intended('/dashboard');
        }

        // If no user with this provider_id AND no user with this email, create a new user
        $newUser = User::create([
            'name' => $socialUser->getName() ?? $socialUser->getNickname(), // Use name or nickname
            'email' => $socialUser->getEmail(),
            'provider_name' => $provider,
            'provider_id' => $socialUser->getId(),
            'avatar' => $socialUser->getAvatar(),
            'provider_token' => $socialUser->token,
            'provider_refresh_token' => $socialUser->refreshToken,
            'email_verified_at' => now(), // Social emails are generally considered verified
            'password' => Hash::make(Str::random(24)), // Create a random secure password as 'password' is required by default User model, even if nullable in DB for social users. Or ensure your logic handles a null password.
        ]);

        Auth::login($newUser, true);
        return redirect()->intended('/dashboard');
    }
}