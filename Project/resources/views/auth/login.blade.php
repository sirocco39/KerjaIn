@if (session('status'))
    <div style="margin-bottom: 1em; color: green;">
        {{ session('status') }}
    </div>
@endif

<form method="POST" action="{{ route('login') }}">
    @csrf

    <div>
        <label for="email">{{ __('Email') }}</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" />
        @error('email')
            <div style="color: red;">{{ $message }}</div>
        @enderror
    </div>

    <div style="margin-top: 1em;">
        <label for="password">{{ __('Password') }}</label>
        <input id="password" type="password" name="password" required autocomplete="current-password" />
        @error('password')
            <div style="color: red;">{{ $message }}</div>
        @enderror
    </div>

    <div style="margin-top: 1em;">
        <label for="remember_me">
            <input id="remember_me" type="checkbox" name="remember">
            <span>{{ __('Remember me') }}</span>
        </label>
    </div>

    <div style="margin-top: 1em;">
        @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}">
                {{ __('Forgot your password?') }}
            </a>
        @endif
    </div>
    <div style="margin-top: 0.5em;">
        <button type="submit">
            {{ __('Log in') }}
        </button>
    </div>

    <div style="margin-top: 1.5em;">
        <p>{{ __('Or login with') }}</p>
        <div style="margin-top: 0.5em;">
            <a href="{{ route('social.redirect', 'google') }}">
                {{ __('Login with Google') }}
            </a>
        </div>
    </div>
</form>
