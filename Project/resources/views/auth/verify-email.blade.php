<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Kami telah mengirim 6-digit kode ke :email. Masukkan untuk memverifikasi.', ['email' => session('email')]) }}
    </div>

    <form method="POST" action="{{ route('verification.verify') }}">
        @csrf

        <div>
            <x-input-label for="otp" :value="__('Kode OTP')" />
            <x-text-input id="otp" class="block mt-1 w-full"
                          type="text" name="otp" maxlength="6" required autofocus />
            <x-input-error :messages="$errors->get('otp')" class="mt-2" />
        </div>

        <input type="hidden" name="email" value="{{ session('email') }}"/>

        <div class="mt-4">
            <x-primary-button class="w-full justify-center">
                {{ __('Verifikasi') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
