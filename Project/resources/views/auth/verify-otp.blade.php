<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('We sent a 6-digit OTP code to :email. Enter it below to verify and login.', ['email' => session('email')]) }}
    </div>

    <form method="POST" action="{{ route('login.otp') }}">
        @csrf

        <div>
            <x-input-label for="otp" :value="__('OTP Code')" />
            <x-text-input id="otp" class="block mt-1 w-full"
                          type="text" name="otp" maxlength="6" required autofocus />
            <x-input-error :messages="$errors->get('otp')" class="mt-2" />
        </div>

        <input type="hidden" name="email" value="{{ session('email') }}"/>

        <div class="mt-4">
            <x-primary-button class="w-full justify-center">
                {{ __('Verify OTP') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
