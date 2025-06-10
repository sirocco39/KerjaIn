<!DOCTYPE html>
<html lang="en">

<head>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>KerjaIn</title>
    <link rel="icon" href="{{ asset('Image/Icon/Icon Kerjain.png') }}" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/landingInfo.css') }}">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">

    <!-- Manrope dan Inter Font-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Manrope:wght@200..800&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">

    <!-- FontAwesome Icon -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>


<body>
    {{-- Navbar Section --}}
    <nav class="navbar navbar-expand-lg bg-light fixed-top" id="mainNavbar">
        <div class="container-fluid pembatas-x">
            <a class="navbar-brand" href="/">
                <img src="{{ asset('Image/Logo/Logo Kerjain - LightBackground.png') }}" alt="Logo Kerjain"
                    id="logoNavbar">
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapseFull"
                aria-controls="navbarCollapseFull" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarCollapseFull">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0" id="navbarCollapse">
                    <li class="nav-item"><a class="nav-link {{ request()->is('job-req/beranda') ? 'active' : '' }}"
                            href="/job-req/beranda">Beranda</a></li>
                    <li class="nav-item"><a
                            class="nav-link {{ request()->is('job-req/tawarkan-kerja') ? 'active' : '' }}"
                            href="/job-req/tawarkan-kerja">Tawarkan Kerja</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->is('job-req/pesan') ? 'active' : '' }}"
                            href="/job-req/pesan">Pesan</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->is('job-req/riwayat') ? 'active' : '' }}"
                            href="/job-req/riwayat">Riwayat</a></li>
                </ul>

                <hr class="d-lg-none my-2">

                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 d-flex align-items-lg-center">
                    <!-- Dropdown Bahasa -->
                    <li class="nav-item dropdown" id="dropLang">
                        <a class="nav-link" id="dropdownLang" data-bs-toggle="dropdown" role="button">
                            <img src="{{ asset('Image/Flag/flag-id.png') }}" alt="Bahasa" id="langFlag">
                            <span>Bahasa</span>
                            <i class="bi bi-chevron-down" id="langIcon"></i>
                        </a>

                        <ul class="dropdown-menu" aria-labelledby="dropdownLang">
                            <li><a class="dropdown-item d-flex align-items-center" href="#"><img
                                        src="{{ asset('Image/Flag/flag-id.png') }}" alt="Indonesia's Flag"
                                        class="flag"> Bahasa</a></li>
                            <li><a class="dropdown-item d-flex align-items-center" href="#"><img
                                        src="{{ asset('Image/Flag/flag-uk.png') }}" alt="England's Flag"
                                        class="flag"> English</a></li>
                        </ul>
                    </li>

                    <!-- Dropdown Profile -->
                    <li class="nav-item dropdown" id="dropProfile">
                        <a class="nav-link" id="dropdownProfile" data-bs-toggle="dropdown" role="button">
                            <img src="{{ asset('Image/Icon/user-circle.svg') }}" alt="Profil" id="profileIcon">
                            <span class="d-lg-none">Profil</span>
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownProfile">
                            @guest
                                <li>
                                    <button type="button" class="dropdown-item d-flex align-items-center gap-1"
                                        data-bs-toggle="modal" data-bs-target="#loginModal">
                                        <img src="{{ asset('Image/Icon/icon-login.svg') }}" alt="Icon Login"
                                            class="navIcon">
                                        Masuk
                                    </button>
                                </li>
                            @endguest
                            @auth <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item d-flex align-items-center gap-1"
                                            onclick="event.preventDefault(); this.closest('form').submit();">
                                            <img src="{{ asset('Image/Icon/icon-logout.svg') }}" alt="Icon Logout"
                                                class="navIcon">
                                            Keluar
                                        </button>
                                    </form>
                                @endauth
                            </li>
                            <li><a class="dropdown-item d-flex align-items-center gap-1" href="/joinworker"><img
                                        src="{{ asset('Image/Icon/icon-join.svg') }}" alt="Icon Menjadi Mitra"
                                        class="navIcon">Menjadi Mitra</a></li>
                            <li><a class="dropdown-item d-flex align-items-center gap-1" href="/job_taker"><img
                                        src="{{ asset('Image/Icon/icon-change-role.svg') }}" alt="Icon Ganti Peran"
                                        class="navIcon">Ganti Peran</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    {{-- End Navbar Section --}}


    {{-- Main Section --}}
    <main class="main-content">
        @yield('content')
    </main>
    {{-- End Main Section --}}


    {{-- Footer --}}
    <footer class="container-fluid pembatas-x footer">
        <div class="row d-flex flex-wrap justify-content-between align-items-start" id="foot-content">
            <div class="col-md-12 col-lg-3 foot-content-detail" id="foot-1">
                <a class="navbar-brand" href="/">
                    <img src="{{ asset('Image/Logo/Logo Kerjain - DarkBackground.png') }}" alt="Logo Kerjain"
                        id="logoNavbar-footer">
                </a>
                <p class="m-0 p-0" id="foot-quotes">
                    Kami selalu mengusahakan yang terbaik buat pelanggan dan memberikan pelayanan terbaik yang kami
                    bisa.
                </p>
            </div>

            <div class="col-6 col-md-3 col-lg-2 foot-content-detail" id="foot-2">
                <h4>Fitur</h4>
                <div class="list-group gap-2">
                    <a href="/job-req/beranda" class="foot-list">Beranda</a>
                    <a href="/job-req/tawarkan-kerja" class="foot-list">Tawarkan Kerja</a>
                    <a href="/job-req/pesan" class="foot-list">Pesan</a>
                    <a href="/job-req/riwayat" class="foot-list">Riwayat</a>
                </div>
            </div>

            <div class="col-6 col-md-3 col-lg-2 foot-content-detail" id="foot-3">
                <h4>Penawaran</h4>
                <div class="list-group gap-2">
                    <a href="#" class="foot-list">Acara</a>
                    <a href="#" class="foot-list">Promo</a>
                    <a href="#" class="foot-list">Ajukan Percobaan</a>
                </div>
            </div>

            <div class="col-6 col-md-3 col-lg-2 foot-content-detail" id="foot-4">
                <h4>Bantuan</h4>
                <div class="list-group gap-2">
                    <a href="#" class="foot-list">Akun</a>
                    <a href="#" class="foot-list">Laporkan</a>
                    <a href="#" class="foot-list">Saran</a>
                </div>
            </div>

            <div class="col-6 col-md-auto col-lg-auto foot-content-detail" id="foot-5">
                <h4>Contact Us</h4>
                <p class="m-0 p-0" id="foot-email">kerjain@gmail.com</p>
                <div class="d-flex flex-row" id="list-foot-icon">
                    <img src="{{ asset('Image/Icon/icon-instagram.png') }}" alt="Logo Instagram" class="foot-icon">
                    <img src="{{ asset('Image/Icon/icon-facebook.png') }}" alt="Logo Facebook" class="foot-icon">
                    <img src="{{ asset('Image/Icon/icon-thread.png') }}" alt="Logo Twitter/Thread"
                        class="foot-icon">
                </div>
            </div>
        </div>

        <div class="row text-center" id="foot-copyright">
            <p class="m-0 p-0" id="text-copyright">Copyright © 2025 Kerjain. All right reserved</p>
        </div>
    </footer>
    {{-- End Footer --}}


    {{-- Pop Up Login --}}
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <h1 style="margin-left: 200px; margin-bottom: 25px; font-family: 'Manrope'; font-size: 25px">
                            Login</h1>
                        <!-- Email input -->
                        <div data-mdb-input-init class="form-outline mb-4" style="padding: 0px 40px; margin: center">
                            <label class="form-label" for="email-login" :value="__('Email')">Email address</label>
                            <input id="email-login" class="form-control" type="email" name="email"
                                :value="old('email')" required autofocus autocomplete="username" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Password input -->
                        <div data-mdb-input-init class="form-outline mb-4" style="padding: 0px 40px; margin: center">
                            <label class="form-label" for="password" :value="__('Password')">Password</label>
                            <input id="password" class="form-control" type="password" name="password" required
                                autocomplete="current-password" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <!-- 2 column grid layout for inline styling -->
                        <div class="row mb-4">
                            <div class="col d-flex justify-content-center">
                                <!-- Checkbox -->
                                <div class="form-check" for="remember_me">
                                    <input id="remember_me" type="checkbox" class="form-check-input"
                                        name="remember">
                                    <label class="form-check-label" for="form2Example31"> Remember me </label>
                                </div>
                            </div>

                            <div class="col">
                                <!-- Simple link -->
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" style="margin-left: 60px">
                                        Lupa kata sandi?
                                    </a>
                                @endif
                            </div>
                        </div>

                        <!-- Submit button -->
                        <button type="submit" data-mdb-button-init data-mdb-ripple-init
                            class="btn btn-primary btn-block mb-4"
                            style="padding: 10px 170px; margin-left: 9%">Login</button>

                        <!-- Register buttons -->
                        <div class="text-center">
                            <p>Belum punya akun? <button type="button" class="btn btn-link w-auto p-0 m-0"
                                    data-bs-toggle="modal" data-bs-target="#logoutModal">Daftar</button></p>
                            <p>Atau masuk dengan:</p>
                            <button type="button" data-mdb-button-init data-mdb-ripple-init
                                class="btn btn-link btn-floating mx-1">
                                <a href="{{ route('auth-google-redirect') }}">
                                    <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google"
                                        style="width: 24px; height: 24px;">
                                </a>
                            </button>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- End Pop Up Login --}}


    {{-- Pop Up Register --}}
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        <h1 style="margin-left: 200px; margin-bottom: 25px; font-family: 'Manrope'; font-size: 25px">
                            Daftar</h1>
                        <!-- Nama input -->
                        <div data-mdb-input-init class="form-outline mb-4" style="padding: 0px 40px; margin: center">
                            <label class="form-label" for="first_name" :value="__('First Name')">Nama Depan</label>
                            <input type="text" id="first_name" class="form-control" name="first_name"
                                :value="old('first_name')" required autofocus autocomplete="given-name" />
                        </div>

                        <div data-mdb-input-init class="form-outline mb-4" style="padding: 0px 40px; margin: center">
                            <label class="form-label" for="last_name" :value="__('Last Name')">Nama Belakang</label>
                            <input type="text" id="last_name" class="form-control" name="last_name"
                                :value="old('last_name')" required autofocus autocomplete="family-name" />
                        </div>

                        <!-- Email input -->
                        <div data-mdb-input-init class="form-outline mb-4" style="padding: 0px 40px; margin: center">
                            <label class="form-label" for="email" :value="__('Email')">Email</label>
                            <input class="form-control" id="email" type="email" name="email"
                                :value="old('email')" required autocomplete="email" />
                        </div>

                        <!-- Password input -->
                        <div data-mdb-input-init class="form-outline mb-4" style="padding: 0px 40px; margin: center">
                            <label class="form-label" for="password" :value="__('Password')">Kata Sandi</label>
                            <input id="password" class="form-control" type="password" name="password" required
                                autocomplete="new-password" />
                        </div>

                        <!-- Konfirmasi Password input -->
                        <div data-mdb-input-init class="form-outline mb-4" style="padding: 0px 40px; margin: center">
                            <label class="form-label" for="password_confirmation"
                                :value="__('Confirm Password')">Konfirmasi Kata Sandi</label>
                            <input class="form-control" id="password_confirmation"type="password"
                                name="password_confirmation" required autocomplete="new-password" />
                        </div>

                        <!-- OTP -->
                        <div data-mdb-input-init class="form-outline mb-4"
                            style="padding: 0 40px; margin: 0 auto; max-width: 600px;">
                            <label class="form-label" for="otp" :value="__('OTP (One Time Password)')">Kode
                                OTP</label>
                            <div
                                style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.25rem;">
                                <input id="otp" class="form-control" type="text" name="otp"
                                    maxlength="6" autocomplete="one-time-code" placeholder="Masukkan OTP disini"
                                    style="
                                        flex-basis: 60%;
                                        max-width: 60%;
                                        padding: 0.5rem;
                                        font-size: 1rem;
                                        border: 1px solid #ccc;
                                        border-radius: 0.375rem;
                                    " />
                                <button type="button" id="send-otp-button"
                                    style="
                                        display: inline-flex;
                                        align-items: center;
                                        justify-content: center;
                                        padding: 0.5rem 1rem;
                                        background-color: #309FFF;
                                        color: white;
                                        border-radius: 0.375rem;
                                        border: none;
                                        width: 10rem;
                                        cursor: pointer;
                                        flex-shrink: 0;
                                        outline: none;
                                        transition: background-color 0.2s, color 0.2s, box-shadow 0.2s;
                                    "
                                    onmouseover="this.style.backgroundColor='#D3FA0D'; this.style.color='#000000';"
                                    onmouseout="this.style.backgroundColor='#309FFF'; this.style.color='white';"
                                    onfocus="this.style.boxShadow='0 0 0 2px #294287';"
                                    onblur="this.style.boxShadow='none';">
                                    {{ __('Kirim OTP') }}
                                </button>
                            </div>
                            <x-input-error :messages="$errors->get('otp')" class="mt-2" />
                            <div id="otp-message" class="mt-2 text-sm text-green-600 hidden"></div>
                        </div>


                        <!-- Submit button -->
                        <button type="submit" data-mdb-button-init data-mdb-ripple-init
                            class="btn btn-primary btn-block mb-4"
                            style="padding: 10px 170px; margin-left: 9%">Daftar</button>

                        <!-- Daftar buttons -->
                        <div class="text-center">
                            <p>Atau daftar dengan:</p>

                            <button type="button" data-mdb-button-init data-mdb-ripple-init
                                class="btn btn-link btn-floating mx-1">
                                <a href="{{ route('auth-google-redirect') }}">
                                    <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google"
                                        style="width: 24px; height: 24px;">
                                </a>
                            </button>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- End Pop Up Register --}}

    <script>
        const sendButton = document.getElementById('send-otp-button');
        const emailInput = document.getElementById('email');
        const otpMessage = document.getElementById('otp-message');
        let countdownInterval = null; // Store interval to clear it

        sendButton.addEventListener('click', function() {
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
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        email: email
                    })
                })
                .then(response => {
                    if (!response.ok) throw response;
                    return response.json();
                })
                .then(data => {
                    showOtpMessage(data.message, true);
                    startCountdown(
                        {{ \App\Http\Controllers\Auth\RegisteredUserController::OTP_COOLDOWN_SECONDS }});
                })
                .catch(async (error) => {
                    let message = 'Terjadi kesalahan saat mengirim OTP.';
                    if (error.json) {
                        const errJson = await error.json();
                        message = errJson.message || message;
                    }
                    showOtpMessage(message, false);
                    sendButton.disabled = false;
                    sendButton.textContent = 'Kirim OTP';
                });
        });

        function showOtpMessage(message, success) {
            otpMessage.textContent = message;
            otpMessage.classList.remove('hidden');
            otpMessage.classList.toggle('text-green-600', success);
            otpMessage.classList.toggle('text-red-600', !success);
        }

        function startCountdown(seconds) {
            if (countdownInterval) {
                clearInterval(countdownInterval); // Clear previous interval if any
            }

            let remaining = seconds;
            sendButton.disabled = true;
            sendButton.textContent = `Tunggu ${remaining} dtk`;

            countdownInterval = setInterval(() => {
                remaining--;
                if (remaining <= 0) {
                    clearInterval(countdownInterval);
                    sendButton.disabled = false;
                    sendButton.textContent = 'Kirim OTP';
                } else {
                    sendButton.textContent = `Tunggu ${remaining} dtk`;
                }
            }, 1000);
        }
    </script>
</body>

</html>
