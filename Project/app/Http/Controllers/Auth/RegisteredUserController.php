<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\SendOtpMail;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisteredUserController extends Controller
{
    public const OTP_COOLDOWN_SECONDS = 60;   // user must wait 60 s before next OTP
    public const OTP_LIFETIME_MINUTES = 5;    // OTP valid for 5 minutes

    public function create(): View
    {
        return view('master.master-job-req', [
            'otpCooldown' => (int) self::OTP_COOLDOWN_SECONDS
        ]);
    }

    public function sendOtp(Request $request)
    {
        // Validate manually so we can return JSON response
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            // If the unique email rule fails, send custom error message
            if ($errors->has('email') && str_contains($errors->first('email'), 'unique')) {
               throw new HttpResponseException(response()->json([
                    'message' => __('alerts.email_already_registered')
                ], 422));
            }

            // Generic validation error
            throw new HttpResponseException(response()->json([
                'message' => $errors->first('email')
            ], 422));
        }

        $email = $request->email;
        $cooldownKey = 'otp_cooldown_' . $email;
        $otpKey = 'otp_code_' . $email;

        // Check cooldown
        if (Cache::has($cooldownKey)) {
            $secondsLeft = Cache::ttl($cooldownKey);
          return response()->json([
                'message' => __('alerts.otp_cooldown', ['seconds' => $secondsLeft])
            ], 429);
        }

        // Generate OTP with leading zeros
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store OTP in cache with expiry
        // OTP valid for 5 minutes, stored as UTC implicitly.
        Cache::put($otpKey, $otp, now()->addMinutes(self::OTP_LIFETIME_MINUTES));

        // Store cooldown key
        // Cooldown for 60 seconds, stored as UTC implicitly.
        Cache::put($cooldownKey, true, now()->addSeconds(self::OTP_COOLDOWN_SECONDS));

        // Send OTP email
        Mail::to($email)->send(new SendOtpMail($otp));

       return response()->json(['message' => __('alerts.otp_sent_success')]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name'  => ['required', 'string', 'max:255'],
            'email'      => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password'   => ['required', 'confirmed', Rules\Password::defaults()],
            'otp'        => ['required', 'digits:6'],
        ]);

        $email = $request->email;
        $otpKey = 'otp_code_' . $email;
        $cachedOtp = Cache::get($otpKey);

        if (!$cachedOtp) {
            // Changed to custom alert
            return back()->with('custom_error_alert', __('alerts.otp_kadaluwarsa'))->withInput();
        }

        if ($cachedOtp !== $request->otp) {
            // Changed to custom alert
            return back()->with('custom_error_alert', __('alerts.otp_tidak_valid'))->withInput();
        }

        // Create user
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $email,
            'password'   => Hash::make($request->password),
        ]);

        // Clear OTP cache after successful verification
        Cache::forget($otpKey);

        event(new Registered($user));
        Auth::login($user);

        // Changed to custom alert, including the user's first name
        return redirect('/job-req/beranda')->with('custom_blue_alert', __('alerts.daftar_berhasil', ['nama' => $user->first_name]));
    }
}
