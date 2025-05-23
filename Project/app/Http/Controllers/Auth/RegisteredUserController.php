<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage; // Make sure this is imported
use Illuminate\Validation\Rules;
use Illuminate\View\View;

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
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class], // Added lowercase back as it's good practice
            'phone_number' => ['required', 'string', 'numeric', 'min:10', 'max:15', 'unique:'.User::class],
            // 'ktp_number' => ['required', 'string', 'numeric', 'digits:16', 'unique:'.User::class], // KTP number validation removed
            'ktp_image' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'], // Max 2MB
            'selfie_with_ktp_image' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'], // Max 2MB
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $ktpImagePath = null;
        if ($request->hasFile('ktp_image')) {
            // Store in 'public/ktp_images' directory, returns 'ktp_images/filename.jpg'
            $ktpImagePath = $request->file('ktp_image')->store('ktp_images', 'public');
        }

        $selfieKtpImagePath = null;
        if ($request->hasFile('selfie_with_ktp_image')) {
            // Store in 'public/selfie_ktp_images' directory
            $selfieKtpImagePath = $request->file('selfie_with_ktp_image')->store('selfie_ktp_images', 'public');
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            // 'ktp_number' => $request->ktp_number, // KTP number saving removed
            'ktp_image_path' => $ktpImagePath,
            'selfie_with_ktp_image_path' => $selfieKtpImagePath,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        // It's good practice to use route names for redirects if possible
        // return redirect(route('dashboard', absolute: false)); // This was from the first store method
        return redirect('/dashboard'); // Or your desired redirect path from the second store method
    }
}
