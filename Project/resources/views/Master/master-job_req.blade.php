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
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.4.1/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/landingInfo.css') }}">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <style>
        .popup-error-card {
            position: absolute;
            top: calc(100% + 0.25rem);
            /* below input */
            left: 0;
            width: 100%;
            z-index: 10;
            background-color: #fff;
            border: 1px solid #dc3545;
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            color: #dc3545;
            font-size: 0.875rem;
        }

        .popup-error-card ul {
            margin: 0;
            padding-left: 1.25rem;
        }

        .d-none {
            display: none !important;
        }

        @media (max-width: 480px) {
            .otp-container {
                flex-direction: column;
                align-items: stretch;
                /* make both items full width */
            }

            .otp-container input,
            .otp-container button {
                width: 100%;
            }

            .otp-container button {
                margin-top: 0.1rem;
                /* add some space between input and button */
            }

            .bottom-column {
                margin-top: 1rem;
            }

            .loginEmailErrorDiv,
            .loginPasswordErrorDiv {
                margin-top: 0.5rem;
            }
        }
    </style>


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
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0" id="navbarCollapse">
                        <li class="nav-item">
                            @auth
                                <a class="nav-link {{ request()->is('job-req/beranda') ? 'active' : '' }}"
                                    href="/job-req/beranda">Beranda</a>
                            @else
                                <a class="nav-link" href="#" data-bs-toggle="modal"
                                    data-bs-target="#loginModal">Beranda</a>
                            @endauth
                        </li>
                        <li class="nav-item">
                            @auth
                                <a class="nav-link {{ request()->is('job-req/tawarkan-kerja') ? 'active' : '' }}"
                                    href="/job-req/tawarkan-kerja">Tawarkan Kerja</a>
                            @else
                                <a class="nav-link" href="#" data-bs-toggle="modal"
                                    data-bs-target="#loginModal">Tawarkan Kerja</a>
                            @endauth
                        </li>
                        <li class="nav-item">
                            @auth
                                <a class="nav-link {{ request()->is('job-req/pesan') ? 'active' : '' }}"
                                    href="/job-req/pesan">Pesan</a>
                            @else
                                <a class="nav-link" href="#" data-bs-toggle="modal"
                                    data-bs-target="#loginModal">Pesan</a>
                            @endauth
                        </li>
                        <li class="nav-item">
                            @auth
                                <a class="nav-link {{ request()->is('job-req/riwayat') ? 'active' : '' }}"
                                    href="/job-req/riwayat">Riwayat</a>
                            @else
                                <a class="nav-link" href="#" data-bs-toggle="modal"
                                    data-bs-target="#loginModal">Riwayat</a>
                            @endauth
                        </li>
                    </ul>

                </ul>

                <hr class="d-lg-none my-2">

                <ul class="navbar-nav ms-auto mb-lg-0 d-flex align-items-lg-center">
                    <!-- Dropdown Bahasa -->
                    <li class="nav-item dropdown" id="dropLang">
                        <a class="nav-link" id="dropdownLang" data-bs-toggle="dropdown" role="button">
                            <img src="{{ asset('Image/Flag/flag-id.png') }}" alt="Bahasa" id="langFlag">
                            <span>Bahasa</span>
                            <i class="bi bi-chevron-down" id="langIcon"></i>
                        </a>

                        <ul class="dropdown-menu m-0" aria-labelledby="dropdownLang">
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

                        <ul class="dropdown-menu dropdown-menu-end m-0" aria-labelledby="dropdownProfile">
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
                                </li>
                                <li><a class="dropdown-item d-flex align-items-center gap-1" href="/joinworker"><img
                                            src="{{ asset('Image/Icon/icon-join.svg') }}" alt="Icon Menjadi Mitra"
                                            class="navIcon">Menjadi Mitra</a></li>
                                <li><a class="dropdown-item d-flex align-items-center gap-1" href="/job-taker/beranda"><img
                                            src="{{ asset('Image/Icon/icon-change-role.svg') }}" alt="Icon Ganti Peran"
                                            class="navIcon">Ganti Peran</a></li>
                            @endauth
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
        <div class="modal-dialog" style="max-width: 400px;">
            <div class="modal-content bg-white rounded" style="box-shadow: none !important; border: none !important;">
                <div class="modal-header border-0 position relative">
                    <h1 class="modal-title w-100 text-center mb-0 fs-4">Login</h1>
                    <button type="button" class="btn-close position-absolute end-0 me-3" data-bs-dismiss="modal"
                        aria-label="Tutup"></button>
                </div>
                <form id="login-form" method="POST" action="{{ route('login') }}" style="padding: 30px">
                    @csrf
                    <!-- Login Email -->
                    <div class="mb-3 position-relative" style="max-height: 75px; height: 100%;">
                        <label for="email-login" class="form-label">Email</label>
                        <input id="email-login" class="form-control is-invalid" type="email" name="email"
                            autocomplete="new-email" required>
                        <div id="Loginemail-error" class="popup-error-card d-none"></div>
                    </div>

                    <!-- Login Password -->
                    <div class="mb-3 position-relative" style="max-height: 75px; height: 100%;">
                        <label for="password-login" class="form-label">Kata Sandi</label>
                        <input id="password-login" class="form-control is-invalid" type="password" name="password"
                            autocomplete="new-password" required>
                        <div id="Loginpassword-error" class="popup-error-card d-none"></div>
                    </div>

                    <!-- Remember Me & Lupa Password -->
                    <div class="d-flex justify-content-between mb-4">
                        <div class="form-check">
                            <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
                            <label class="form-check-label">Ingat Saya</label>
                        </div>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}">Lupa kata sandi?</a>
                        @endif
                    </div>

                    <!-- Tombol Login -->
                    <button type="submit" class="btn btn-primary w-100 mb-3 py-2">Masuk</button>

                    <!-- Footer -->
                    <div class="text-center">
                        <p class="mb-2">Belum punya akun?
                            <button type="button" class="btn btn-link p-0" data-bs-toggle="modal"
                                data-bs-target="#logoutModal">
                                Daftar
                            </button>
                        </p>
                        <p class="mb-2">Atau masuk dengan:</p>
                        <a href="{{ route('auth-google-redirect') }}">
                            <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google"
                                width="24">
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- End Pop Up Register --}}

    {{-- Pop Up Register --}}
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="max-width: 800px;">
            <div class="modal-content bg-white rounded" style="box-shadow: none !important; border: none !important;">
                <div class="modal-header border-0 position-relative d-flex justify-content-center align-items-center">
                    <h1 class="mb-0 fs-4" style="font-weight: 500">Daftar</h1>
                    <button type="button" class="btn-close position-absolute end-0 me-3" data-bs-dismiss="modal"
                        aria-label="Tutup"></button>
                </div>
                <form method="POST" action="{{ route('register') }}" style="padding: 30px">
                    @csrf
                    <div class="row">
                        <!-- Left Column -->
                        <div class="col-md-6 mb-4">
                            <!-- Nama Depan input -->
                            <div class="mb-3 position-relative" style="max-height: 75px; height: 100%;">
                                <label class="form-label" for="first_name">Nama Depan</label>
                                <input type="text" id="first_name" class="form-control is-invalid"
                                    name="first_name" required />
                                <div id="first_name-error" class="popup-error-card d-none"></div>
                            </div>

                            <!-- Nama Belakang input -->
                            <div class="mb-3 position-relative" style="max-height: 75px; height: 100%;">
                                <label class="form-label" for="last_name">Nama Belakang</label>
                                <input type="text" id="last_name" class="form-control is-invalid"
                                    name="last_name" required />
                                <div id="last_name-error" class="popup-error-card d-none"></div>
                            </div>

                            <!-- Register Email input -->
                            <div class="mb-3 position-relative" style="max-height: 75px; height: 100%;">
                                <label class="form-label" for="email-register">Email</label>
                                <input class="form-control is-invalid" id="email-register" type="email"
                                    name="email" required autocomplete="email" />
                                <div id="email-error" class="popup-error-card d-none"></div>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="col-md-6 mb-4">
                            <!-- Password input -->
                            <div class="mb-3 position-relative" style="max-height: 75px; height: 100%;">
                                <label class="form-label" for="password">Kata Sandi</label>
                                <input id="password" class="form-control is-invalid" type="password"
                                    name="password" required />
                                <div id="password-error" class="popup-error-card d-none"></div>
                            </div>

                            <!-- Konfirmasi Password input -->
                            <div class="mb-3 position-relative" style="max-height: 75px; height: 100%;">
                                <label class="form-label" for="password_confirmation">Konfirmasi Kata Sandi</label>
                                <input class="form-control is-invalid" id="password_confirmation" type="password"
                                    name="password_confirmation" required />
                                <div id="confirm_password-error" class="popup-error-card d-none"></div>
                            </div>

                            <!-- OTP -->
                            <div class="mb-3 position-relative" style="max-height: 75px; height: 100%;">
                                <label class="form-label" for="otp">Kode OTP</label>
                                <div class="d-flex align-items-center gap-2 otp-container">
                                    <input id="otp" class="form-control is-invalid" type="text"
                                        name="otp" maxlength="6" placeholder="Masukkan Kode OTP" required />
                                    <button type="button" id="send-otp-button"
                                        class="btn btn-primary send-otp-button"
                                        onmouseover="this.style.backgroundColor='#D3FA0D'; this.style.color='#000000';"
                                        onmouseout="this.style.backgroundColor='#309FFF'; this.style.color='white';"
                                        onfocus="this.style.boxShadow='0 0 0 2px #294287';"
                                        onblur="this.style.boxShadow='none';" style="max-width: 120px; width: 100%;">
                                        {{ __('Kirim OTP') }}
                                    </button>
                                </div>
                                <div id="otp-error" class="popup-error-card d-none"></div>
                            </div>

                        </div>

                        <!-- Bottom Column -->
                        <div class="col-12 bottom-column">
                            <div class="relative h-5"> <!-- fixed height container -->
                                <div id="otp-message" class="absolute inset-0 text-sm hidden text-center mb-2"
                                    style="color: #16a34a;">
                                </div>
                            </div>

                            <!-- Submit button -->
                            <button type="submit" class="btn btn-primary w-100 mb-3 py-2">Daftar</button>

                            <!-- Daftar buttons -->
                            <div class="text-center">
                                <p>Atau daftar dengan:</p>
                                <a href="{{ route('auth-google-redirect') }}" class="btn btn-link btn-floating mx-1">
                                    <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google"
                                        style="width: 24px; height: 24px;">
                                </a>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
    {{-- End Pop Up Register --}}

    <script>
        const loginEmailInput = document.getElementById('email-login');
        const loginEmailErrorDiv = document.getElementById('Loginemail-error');
        const loginPasswordInput = document.getElementById('password-login');
        const loginPasswordErrorDiv = document.getElementById('Loginpassword-error');
        const emailInput = document.getElementById('email-register');
        const emailErrorDiv = document.getElementById('email-error');
        const passwordInput = document.getElementById('password');
        const passwordErrorDiv = document.getElementById('password-error');
        const firstNameInput = document.getElementById('first_name');
        const firstNameErrorDiv = document.getElementById('first_name-error');
        const lastNameInput = document.getElementById('last_name');
        const lastNameErrorDiv = document.getElementById('last_name-error');
        const confirmPasswordInput = document.getElementById('password_confirmation');
        const confirmPasswordErrorDiv = document.getElementById('confirm_password-error');
        const otpInput = document.getElementById('otp');
        const otpErrorDiv = document.getElementById('otp-error');
        const rememberMeCheckbox = document.getElementById('remember_me');

        const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_])[^\s]{8,16}$/;
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        // === Login Email ===
        loginEmailInput.addEventListener('blur', function() {
            loginEmailInput.classList.remove('is-invalid');
            loginEmailErrorDiv.classList.add('d-none');
        });

        loginEmailInput.addEventListener('input', function() {
            const errors = [];

            if (loginEmailInput.value.trim() === '') {
                errors.push('Email harus diisi.');
            } else if (!emailRegex.test(loginEmailInput.value.trim())) {
                errors.push('Silakan masukkan alamat email yang valid.');
            }

            if (errors.length > 0) {
                loginEmailErrorDiv.innerHTML = `
            <ul class="mb-0">
                ${errors.map(err => `<li>${err}</li>`).join('')}
            </ul>
        `;
                loginEmailErrorDiv.classList.remove('d-none');
                loginEmailInput.classList.add('is-invalid');
            } else {
                loginEmailErrorDiv.innerHTML = '';
                loginEmailErrorDiv.classList.add('d-none');
                loginEmailInput.classList.remove('is-invalid');
            }
        });

        // === Login Password ===
        loginPasswordInput.addEventListener('blur', function() {
            loginPasswordInput.classList.remove('is-invalid');
            loginPasswordErrorDiv.classList.add('d-none');
        });

        loginPasswordInput.addEventListener('input', function() {
            const errors = [];

            if (loginPasswordInput.value.trim() === '') {
                errors.push('Password harus diisi.');
            } else if (!passwordRegex.test(loginPasswordInput.value.trim())) {
                errors.push('Silakan masukkan password yang valid.');
            }

            if (errors.length > 0) {
                loginPasswordErrorDiv.innerHTML = `
            <ul class="mb-0">
                ${errors.map(err => `<li>${err}</li>`).join('')}
            </ul>
        `;
                loginPasswordErrorDiv.classList.remove('d-none');
                loginPasswordInput.classList.add('is-invalid');
            } else {
                loginPasswordErrorDiv.innerHTML = '';
                loginPasswordErrorDiv.classList.add('d-none');
                loginPasswordInput.classList.remove('is-invalid');
            }
        });


        const loginModal = document.getElementById('loginModal');
        const loginForm = loginModal.querySelector('form');

        loginForm.addEventListener('submit', async function(event) {
            event.preventDefault();

            // Clear previous server errors
            loginEmailErrorDiv.classList.add('d-none');
            loginPasswordErrorDiv.classList.add('d-none');
            loginEmailInput.classList.remove('is-invalid');
            loginPasswordInput.classList.remove('is-invalid');

            const formData = new FormData(loginForm);

            try {
                const response = await fetch("{{ route('login') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: formData
                });

                const result = await response.json();

                if (response.ok) {
                    window.location.href = result.redirect_url ?? '/dashboard';
                } else {
                    if (result.errors) {
                        if (result.errors.email) {
                            loginEmailErrorDiv.innerHTML = `
                        <ul class="mb-0">
                            ${result.errors.email.map(err => `<li>${err}</li>`).join('')}
                        </ul>
                    `;
                            loginEmailErrorDiv.classList.remove('d-none');
                            loginEmailInput.classList.add('is-invalid');
                        }

                        if (result.errors.password) {
                            loginPasswordErrorDiv.innerHTML = `
                        <ul class="mb-0">
                            ${result.errors.password.map(err => `<li>${err}</li>`).join('')}
                        </ul>
                    `;
                            loginPasswordErrorDiv.classList.remove('d-none');
                            loginPasswordInput.classList.add('is-invalid');
                            loginPasswordInput.value = '';
                            loginEmailInput.value = '';
                            rememberMeCheckbox.checked = false;
                        }
                    }
                }

            } catch (error) {
                console.error('Login error:', error);
                alert('Terjadi kesalahan saat login.');
            }
        });



        document.addEventListener('DOMContentLoaded', function() {
            // === OTP ===
            otpInput.addEventListener('blur', function() {
                otpInput.classList.remove('is-invalid');
                otpErrorDiv.classList.add('d-none');
            });

            otpInput.addEventListener('input', function() {
                const errors = [];

                if (otpInput.value.trim() === '') {
                    errors.push('OTP harus diisi.');
                } else if (!/^\d{6}$/.test(otpInput.value.trim())) {
                    errors.push('OTP harus berupa 6 digit angka.');
                }

                if (errors.length > 0) {
                    otpErrorDiv.innerHTML = `
            <ul class="mb-0">
                ${errors.map(err => `<li>${err}</li>`).join('')}
            </ul>
        `;
                    otpErrorDiv.classList.remove('d-none');
                    otpInput.classList.add('is-invalid');
                } else {
                    otpErrorDiv.innerHTML = '';
                    otpErrorDiv.classList.add('d-none');
                    otpInput.classList.remove('is-invalid');
                }
            });

            // === Register Email ===
            emailInput.addEventListener('blur', function() {
                emailInput.classList.remove('is-invalid');
                emailErrorDiv.classList.add('d-none');
            });

            emailInput.addEventListener('input', function() {
                const errors = [];

                if (emailInput.value.trim() === '') {
                    errors.push('Email harus diisi.');
                } else if (!emailRegex.test(emailInput.value.trim())) {
                    errors.push('Silakan masukkan alamat email yang valid.');
                }

                if (errors.length > 0) {
                    emailErrorDiv.innerHTML = `
            <ul class="mb-0">
                ${errors.map(err => `<li>${err}</li>`).join('')}
            </ul>
        `;
                    emailErrorDiv.classList.remove('d-none');
                    emailInput.classList.add('is-invalid');
                } else {
                    emailErrorDiv.innerHTML = '';
                    emailErrorDiv.classList.add('d-none');
                    emailInput.classList.remove('is-invalid');
                }
            });

            // === Register Password ===
            const rules = {
                length: {
                    test: value => value.length >= 8 && value.length <= 16,
                    message: 'Minimal 8 dan maksimal 16 karakter.',
                },
                lowercase: {
                    test: value => /[a-z]/.test(value),
                    message: 'Mengandung huruf kecil (a–z).',
                },
                uppercase: {
                    test: value => /[A-Z]/.test(value),
                    message: 'Mengandung huruf besar (A–Z).',
                },
                digit: {
                    test: value => /\d/.test(value),
                    message: 'Mengandung angka (0–9).',
                },
                special: {
                    test: value => /[\W_]/.test(value),
                    message: 'Mengandung karakter spesial (contoh: !@#%).',
                },
                noSpaces: {
                    test: value => /^\S+$/.test(value),
                    message: 'Tidak boleh mengandung spasi.',
                }
            };

            passwordInput.addEventListener('blur', function() {
                passwordInput.classList.remove('is-invalid');
                passwordErrorDiv.classList.add('d-none');
            });

            passwordInput.addEventListener('input', function() {
                const value = passwordInput.value.trim();
                let errors = [];

                if (value === '') {
                    passwordErrorDiv.innerHTML = 'Password harus diisi.';
                    passwordErrorDiv.classList.remove('d-none');
                    passwordInput.classList.add('is-invalid');
                    return;
                }

                for (const key in rules) {
                    if (!rules[key].test(value)) {
                        errors.push(rules[key].message);
                    }
                }

                if (errors.length > 0) {
                    passwordErrorDiv.innerHTML = `
            <ul class="mb-0">
                ${errors.map(err => `<li>${err}</li>`).join('')}
            </ul>
        `;
                    passwordErrorDiv.classList.remove('d-none');
                    passwordInput.classList.add('is-invalid');
                } else {
                    passwordErrorDiv.innerHTML = '';
                    passwordErrorDiv.classList.add('d-none');
                    passwordInput.classList.remove('is-invalid');
                }
            });


            // === Nama Depan ===

            firstNameInput.addEventListener('blur', function() {
                firstNameInput.classList.remove('is-invalid');
                firstNameErrorDiv.classList.add('d-none');
            });

            firstNameInput.addEventListener('input', function() {
                const errors = [];

                if (firstNameInput.value.trim() === '') {
                    errors.push('Nama depan diperlukan.');
                }

                if (errors.length > 0) {
                    firstNameErrorDiv.innerHTML = `
            <ul class="mb-0">
                ${errors.map(err => `<li>${err}</li>`).join('')}
            </ul>
        `;
                    firstNameErrorDiv.classList.remove('d-none');
                    firstNameInput.classList.add('is-invalid');
                } else {
                    firstNameErrorDiv.innerHTML = '';
                    firstNameErrorDiv.classList.add('d-none');
                    firstNameInput.classList.remove('is-invalid');
                }
            });

            // === Nama Belakang ===

            lastNameInput.addEventListener('blur', function() {
                lastNameInput.classList.remove('is-invalid');
                lastNameErrorDiv.classList.add('d-none');
            });

            lastNameInput.addEventListener('input', function() {
                const errors = [];

                if (lastNameInput.value.trim() === '') {
                    errors.push('Nama belakang diperlukan.');
                }

                if (errors.length > 0) {
                    lastNameErrorDiv.innerHTML = `
            <ul class="mb-0">
                ${errors.map(err => `<li>${err}</li>`).join('')}
            </ul>
        `;
                    lastNameErrorDiv.classList.remove('d-none');
                    lastNameInput.classList.add('is-invalid');
                } else {
                    lastNameErrorDiv.innerHTML = '';
                    lastNameErrorDiv.classList.add('d-none');
                    lastNameInput.classList.remove('is-invalid');
                }
            });

            // === Konfirmasi Password ===
            confirmPasswordInput.addEventListener('blur', function() {
                confirmPasswordInput.classList.remove('is-invalid');
                confirmPasswordErrorDiv.classList.add('d-none');
            });
            confirmPasswordInput.addEventListener('input', function() {
                const originalPassword = passwordInput.value;
                const confirmPassword = confirmPasswordInput.value;

                const errors = [];

                if (confirmPassword === '') {
                    errors.push('Konfirmasi kata sandi harus diisi.');
                } else if (confirmPassword !== originalPassword) {
                    errors.push('Kata sandi tidak cocok.');
                }

                if (errors.length > 0) {
                    confirmPasswordErrorDiv.innerHTML = `
            <ul class="mb-0">
                ${errors.map(err => `<li>${err}</li>`).join('')}
            </ul>
        `;
                    confirmPasswordErrorDiv.classList.remove('d-none');
                    confirmPasswordInput.classList.add('is-invalid');
                } else {
                    confirmPasswordErrorDiv.innerHTML = '';
                    confirmPasswordErrorDiv.classList.add('d-none');
                    confirmPasswordInput.classList.remove('is-invalid');
                }
            });

        });

        const otpMessage = document.getElementById('otp-message');
        let countdownInterval = null; // Store interval to clear it

        const sendButton = document.getElementById('send-otp-button');
        sendButton.addEventListener('click', function() {
            const email = document.getElementById('email-register').value.trim();
            if (!email) {
                return;
            } else if (emailInput.classList.contains('is-invalid')) {
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
                    otpMessage.classList.add('!success');
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
            otpMessage.style.color = success ? '#16a34a' : '#dc2626';
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
