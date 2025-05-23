{{-- This file will have no styling from Breeze or Tailwind --}}
{{-- It's assumed you have a basic HTML structure (<html>, <head>, <body>) elsewhere if this is part of a larger layout --}}

<form method="POST" action="{{ route('register') }}" enctype="multipart/form-data"> {{-- Added enctype for file uploads --}}
    @csrf

    <div>
        <label for="name">{{ __('Name') }}</label>
        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" />
        @error('name')
            <div style="color: red;">{{ $message }}</div>
        @enderror
    </div>

    <div style="margin-top: 1em;">
        <label for="email">{{ __('Email') }}</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" />
        @error('email')
            <div style="color: red;">{{ $message }}</div>
        @enderror
    </div>

    <div style="margin-top: 1em;">
        <label for="phone_number">{{ __('Phone Number') }}</label>
        <input id="phone_number" type="text" name="phone_number" value="{{ old('phone_number') }}" required autocomplete="tel" />
        @error('phone_number')
            <div style="color: red;">{{ $message }}</div>
        @enderror
    </div>

    {{-- KTP Number field removed --}}

    <div style="margin-top: 1em;">
        <label for="ktp_image">{{ __('KTP Card Image') }}</label>
        <input id="ktp_image" type="file" name="ktp_image" required />
        @error('ktp_image')
            <div style="color: red;">{{ $message }}</div>
        @enderror
    </div>

    <div style="margin-top: 1em;">
        <label for="selfie_with_ktp_image">{{ __('Image Holding KTP Card (Selfie)') }}</label>
        <input id="selfie_with_ktp_image" type="file" name="selfie_with_ktp_image" required />
        @error('selfie_with_ktp_image')
            <div style="color: red;">{{ $message }}</div>
        @enderror
    </div>

    <div style="margin-top: 1em;">
        <label for="password">{{ __('Password') }}</label>
        <input id="password" type="password" name="password" required autocomplete="new-password" />
        @error('password')
            <div style="color: red;">{{ $message }}</div>
        @enderror
    </div>

    <div style="margin-top: 1em;">
        <label for="password_confirmation">{{ __('Confirm Password') }}</label>
        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" />
        @error('password_confirmation')
            <div style="color: red;">{{ $message }}</div>
        @enderror
    </div>

    <div style="margin-top: 1em;">
        <a href="{{ route('login') }}">
            {{ __('Already registered?') }}
        </a>
    </div>
    <div style="margin-top: 0.5em;">
        <button type="submit">
            {{ __('Register') }}
        </button>
    </div>


    <div style="margin-top: 1.5em;">
        <p>{{ __('Or continue with') }}</p>
        <div style="margin-top: 0.5em;">
            <a href="{{ route('social.redirect', 'google') }}">
                {{ __('Sign in with Google') }}
            </a>
        </div>
        {{-- Example for GitHub if you add it later --}}
        {{--
        <div style="margin-top: 0.5em;">
            <a href="{{ route('social.redirect', 'github') }}">
                {{ __('Sign in with GitHub') }}
            </a>
        </div>
        --}}
    </div>
</form>
