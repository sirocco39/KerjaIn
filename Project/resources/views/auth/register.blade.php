<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- First Name -->
        <div>
            <x-input-label for="first_name" :value="__('First Name')" />
            <x-text-input id="first_name" class="block mt-1 w-full" type="text" name="first_name" :value="old('first_name')" required autofocus autocomplete="given-name" />
            <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
        </div>

        <!-- Last Name -->
        <div class="mt-4">
            <x-input-label for="last_name" :value="__('Last Name')" />
            <x-text-input id="last_name" class="block mt-1 w-full" type="text" name="last_name" :value="old('last_name')" required autocomplete="family-name" />
            <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
        </div>

        <!-- Email -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="email" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- OTP -->
        <div class="mt-4">
        <x-input-label for="otp" :value="__('OTP (One Time Password)')" />
        <div class="flex justify-between items-center mt-1">
            <x-text-input id="otp" class="flex-shrink max-w-[75%] mr-4" type="text" name="otp" maxlength="6" autocomplete="one-time-code" placeholder="Masukkan OTP disini" />
            <button type="button" id="send-otp-button" class="flex-shrink-0 inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 w-40 justify-center">
            {{ __('Kirim OTP') }}
            </button>
        </div>
        <x-input-error :messages="$errors->get('otp')" class="mt-2" />
        <div id="otp-message" class="mt-2 text-sm text-green-600 hidden"></div>
        </div>

        <!-- Link -->
        <div class="w-full flex justify-center items-center mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Sudah punya akun?') }}
            </a>
        </div>

        <!-- Submit -->
        <div class="w-full flex justify-center items-center mt-4">
            <x-primary-button class="ms-4">
                {{ __('Daftar') }}
            </x-primary-button>
        </div>

        <!-- OR Divider -->
        <div class="mt-6 relative">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-300"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="bg-white px-2 text-gray-500">atau daftar dengan</span>
            </div>
        </div>

        <!-- Google Login Button -->
        <div class="mt-4 flex justify-center">
            <a href="{{ route('auth-google-redirect') }}"
            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-100">
                <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google" class="w-5 h-5 mr-3">
                Masuk dengan Google
            </a>
        </div>
    </form>

    <!-- JavaScript for Sending OTP -->
    <script>
        const sendButton = document.getElementById('send-otp-button');
        const emailInput = document.getElementById('email');
        const otpMessage = document.getElementById('otp-message');

        sendButton.addEventListener('click', function () {
            const email = emailInput.value.trim();
            if (!email) {
                alert('Silakan masukkan email terlebih dahulu.');
                emailInput.focus();
                return;
            }

            sendButton.disabled = true;
            sendButton.textContent = 'Mengirim...';

            fetch("{{ route('send.otp') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ email: email })
            })
            .then(response => {
                if (!response.ok) throw response;
                return response.json();
            })
            .then(data => {
                showOtpMessage(data.message, true);
                startCountdown(60);
            })
            .catch(async (error) => {
                let message = 'Gagal mengirim OTP';
                if (error.json) {
                    const errData = await error.json();
                    message = errData.message || message;
                }
                alert(message);
                sendButton.disabled = false;
                sendButton.textContent = 'Kirim OTP ke Email';
            });
        });

        function showOtpMessage(message, success = true) {
            otpMessage.textContent = message;
            otpMessage.classList.remove('hidden');
            otpMessage.classList.toggle('text-green-600', success);
            otpMessage.classList.toggle('text-red-600', !success);
        }

        function startCountdown(seconds) {
            let remaining = seconds;
            sendButton.textContent = `Tunggu ${remaining}s...`;

            const countdown = setInterval(() => {
                remaining--;
                sendButton.textContent = `Tunggu ${remaining}s...`;

                if (remaining <= 0) {
                    clearInterval(countdown);
                    sendButton.textContent = 'Kirim OTP ke Email';
                    sendButton.disabled = false;
                }
            }, 1000);
        }
    </script>
</x-guest-layout>
<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- TOP ROW: Two Columns -->
        <div class="flex flex-col md:flex-row gap-x-6">
            <!-- Left Column -->
            <div class="md:w-1/2">
                <!-- First Name -->
                <div>
                    <x-input-label for="first_name" :value="__('First Name')" />
                    <x-text-input id="first_name" class="block mt-1 w-full" type="text" name="first_name" :value="old('first_name')" required autofocus autocomplete="given-name" />
                    <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                </div>

                <!-- Last Name -->
                <div class="mt-4">
                    <x-input-label for="last_name" :value="__('Last Name')" />
                    <x-text-input id="last_name" class="block mt-1 w-full" type="text" name="last_name" :value="old('last_name')" required autocomplete="family-name" />
                    <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                </div>

                <!-- Email -->
                <div class="mt-4">
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="email" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>
            </div>

            <!-- Right Column -->
            <div class="md:w-1/2">
                <!-- Password -->
                <div>
                    <x-input-label for="password" :value="__('Password')" />
                    <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Confirm Password -->
                <div class="mt-4">
                    <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                    <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <!-- OTP -->
                <div class="mt-4">
                    <x-input-label for="otp" :value="__('OTP (One Time Password)')" />
                    <div class="flex justify-between items-center mt-1">
                        <x-text-input id="otp" class="flex-shrink max-w-[75%] mr-4" type="text" name="otp" maxlength="6" autocomplete="one-time-code" placeholder="Masukkan OTP disini" />
                        <button type="button" id="send-otp-button" class="flex-shrink-0 inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 w-40 justify-center">
                            {{ __('Kirim OTP') }}
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('otp')" class="mt-2" />
                    <div id="otp-message" class="mt-2 text-sm text-green-600 hidden"></div>
                </div>
            </div>
        </div>

        <!-- BOTTOM ROW: Link + Submit + OR + Google Login -->
        <div class="w-full flex justify-center items-center mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Sudah punya akun?') }}
            </a>
        </div>

        <div class="w-full flex justify-center items-center mt-4">
            <x-primary-button class="ms-4">
                {{ __('Daftar') }}
            </x-primary-button>
        </div>

        <!-- OR Divider -->
        <div class="mt-6 relative">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-300"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="bg-white px-2 text-gray-500">atau daftar dengan</span>
            </div>
        </div>

        <!-- Google Login Button -->
        <div class="mt-4 flex justify-center">
            <a href="{{ route('auth-google-redirect') }}"
            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-100">
                <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google" class="w-5 h-5 mr-3">
                Daftar dengan Google
            </a>
        </div>
    </form>

    <!-- Your JavaScript remains unchanged -->
</x-guest-layout>
