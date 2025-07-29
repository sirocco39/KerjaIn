<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    {{-- <script src="//unpkg.com/alpinejs" defer></script> --}}
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>KerjaIn</title>
    <link rel="icon" href="{{ asset('Image/Icon/Icon Kerjain.png') }}" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.4.1/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/landingInfo.css') }}">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link rel="stylesheet" href="{{ asset('css/rating.css') }}">

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../../plugins/fontawesome-free/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../../dist/css/adminlte.min.css">
    <!-- Optional: Google Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700">
    <!-- AdminLTE v3 Assets via CDN -->
    {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css"> --}}
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css">
    <style>
        .dropdown-profile-custom {
            min-width: 250px;
            /* Lebar minimum agar tidak sempit */
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border: none;
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
        }

        /* Ini adalah perbaikan utamanya */
        .dropdown-profile-custom .dropdown-item {
            padding: 0.75rem 1.25rem;
            /* Tambah padding kanan-kiri */
            font-weight: 500;
        }

        .dropdown-profile-custom .navIcon {
            width: 20px;
            /* Pastikan ukuran ikon seragam */
        }

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

        /* Custom Alert Styles for Top-Middle Positioning and Consistent Look */
        #custom-alert-container {
            position: fixed;
            top: 20px;
            /* Adjust as needed */
            left: 50%;
            transform: translateX(-50%);
            z-index: 99999;
            /* Ensure it's above other elements */
            width: 100%;
            max-width: 380px;
            /* Adjust max-width for a good size */
            padding: 0 15px;
            /* Padding on sides for smaller screens */
            box-sizing: border-box;
            pointer-events: none;
            /* Add this to prevent blocking clicks when not active */
        }

        #custom-alert {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 20px;
            border-radius: 8px;
            /* Rounded corners */
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            /* Soft shadow */
            opacity: 0;
            transform: translateY(-20px);
            transition: opacity 0.3s ease-out, transform 0.3s ease-out;
            color: white;
            /* Default text color, overridden by type classes */
        }

        #custom-alert.show {
            opacity: 1;
            transform: translateY(0);
        }

        /* Specific alert type styles */
        .alert-success-bg {
            background-color: #28a745;
            /* Bootstrap success green */
            color: white;
        }

        .alert-error-bg {
            background-color: #dc3545;
            /* Bootstrap danger red */
            color: white;
        }

        .alert-info-bg {
            background-color: #17a2b8;
            /* Bootstrap info blue */
            color: white;
        }

        /* New: Custom Blue Alert Style */
        .alert-blue-bg {
            background-color: #309FFF;
            color: white;
        }

        #custom-alert-close {
            background: none;
            border: none;
            font-size: 1.2em;
            cursor: pointer;
            color: inherit;
            /* Inherit color from parent */
            line-height: 1;
            padding: 0;
        }
    </style>


    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Manrope:wght@200..800&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>


<body>
    {{-- Elemen Audio untuk Soundtrack Logo --}}
    <audio id="logoSoundtrack" loop preload="auto" style="display: none;">
        <source src="{{ asset('audio/kerjain_dvorak.mp3') }}" type="audio/mpeg">
        {{-- Anda bisa menambahkan format lain seperti .ogg untuk kompatibilitas lebih luas --}}
        {{-- <source src="{{ asset('audio/kerjain_soundtrack.ogg') }}" type="audio/ogg"> --}}
        Your browser does not support the audio element.
    </audio>
    <div id="custom-alert-container">
        <div id="custom-alert">
            <span id="custom-alert-message"></span>
            <button type="button" id="custom-alert-close" aria-label="Close">&times;</button>
        </div>
    </div>

    {{-- Navbar Section --}}
    <nav class="navbar navbar-expand-lg bg-light fixed-top" id="mainNavbar">
        <div class="container-fluid pembatas-x">

            {{-- Tambahkan ID dan event listener ke elemen <a> yang membungkus logo --}}
            <a class="navbar-brand" href="/job-req/beranda" id="logoNavbarLink">
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
                                    href="/job-req/beranda">{{ __('master-job-req.beranda') }}</a>
                            @else
                                <a class="nav-link" href="#" data-bs-toggle="modal"
                                    data-bs-target="#loginModal">{{ __('master-job-req.beranda') }}</a>
                            @endauth
                        </li>
                        <li class="nav-item">
                            @auth
                                <a class="nav-link {{ request()->is('job-req/tawarkan-kerja') ? 'active' : '' }}"
                                    href="/job-req/tawarkan-kerja">{{ __('master-job-req.tawarkan_kerja') }}</a>
                            @else
                                <a class="nav-link" href="#" data-bs-toggle="modal"
                                    data-bs-target="#loginModal">{{ __('master-job-req.tawarkan_kerja') }}</a>
                            @endauth
                        </li>
                        <li class="nav-item">
                            @auth
                                <a class="nav-link {{ request()->is('job-req/pesan') ? 'active' : '' }}"
                                    href="/job-req/pesan">{{ __('master-job-req.pesan') }}</a>
                            @else
                                <a class="nav-link" href="#" data-bs-toggle="modal"
                                    data-bs-target="#loginModal">{{ __('master-job-req.pesan') }}</a>
                            @endauth
                        </li>
                        <li class="nav-item">
                            @auth
                                <a class="nav-link {{ request()->is('job-req/riwayat') ? 'active' : '' }}"
                                    href="/job-req/riwayat">{{ __('master-job-req.riwayat') }}</a>
                            @else
                                <a class="nav-link" href="#" data-bs-toggle="modal"
                                    data-bs-target="#loginModal">{{ __('master-job-req.riwayat') }}</a>
                            @endauth
                        </li>
                    </ul>

                </ul>

                <hr class="d-lg-none my-2">

                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 d-flex align-items-lg-center">
                    <li class="nav-item dropdown" id="dropLang">
                        <a class="nav-link" id="dropdownLang" data-bs-toggle="dropdown" role="button">

                            @if (App::getLocale() == 'id')
                                <img src="{{ asset('Image/Flag/flag-id.png') }}" alt="Bahasa Indonesia"
                                    id="langFlag">
                            @else
                                {{-- Pastikan Anda memiliki gambar bendera Inggris di path ini --}}
                                <img src="{{ asset('Image/Flag/flag-uk.png') }}" alt="English" id="langFlag">
                            @endif

                            <span>{{ __('master-job-req.bahasa') }}</span>
                            <i class="bi bi-chevron-down" id="langIcon"></i>
                        </a>

                        <ul class="dropdown-menu m-0" aria-labelledby="dropdownLang">
                            <li><a class="dropdown-item d-flex align-items-center"
                                    href="{{ route('language.switch', 'id') }}"><img
                                        src="{{ asset('Image/Flag/flag-id.png') }}" alt="Indonesia's Flag"
                                        class="flag">{{ __('master-job-req.indonesia') }}</a></li>
                            <li><a class="dropdown-item d-flex align-items-center"
                                    href="{{ route('language.switch', 'en') }}"><img
                                        src="{{ asset('Image/Flag/flag-uk.png') }}" alt="England's Flag"
                                        class="flag">{{ __('master-job-req.english') }}</a></li>
                        </ul>
                    </li>

                    <li class="nav-item dropdown" id="dropProfile">
                        <a class="nav-link" id="dropdownProfile" data-bs-toggle="dropdown" role="button">

                            <img 
                                src="{{ asset(Auth::user()->photo_url_user ?? asset('Image/Icon/user-circle.svg')) }}" 
                                alt="Profil" 
                                id="profileIcon"
                                onerror="this.onerror=null;this.src='{{ asset('Image/Icon/user-circle.svg') }}';"
                                style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;"
                            />
                            <span class="d-lg-none">{{ __('master-job-req.profil') }}</span>
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end m-0" aria-labelledby="dropdownProfile">
                            @guest
                                <li>
                                    <button type="button" class="dropdown-item d-flex align-items-center gap-1"
                                        data-bs-toggle="modal" data-bs-target="#loginModal">
                                        <img src="{{ asset('Image/Icon/icon-login.svg') }}" alt="Icon Login"
                                            class="navIcon">
                                        {{ __('master-job-req.masuk') }}
                                    </button>
                                </li>
                            @endguest
                            @auth
                            
                            <li>
                                    {{-- Jika user biasa, bisa diklik dan diarahkan ke halaman profile --}}
                                
                                    <a href="{{ route('profile') }}" class="dropdown-item d-flex align-items-center gap-2">
                                        <img 
                                            src="{{ asset(Auth::user()->photo_url_user) }}" 
                                            alt="Profil" 
                                            id="profileIcon"
                                            onerror="this.onerror=null;this.src='{{ asset('Image/Icon/user-circle.svg') }}';"
                                            style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;"
                                        />
                                        <div>
                                            <div class="fw-bold text-dark" style="font-size: 16px;">{{ auth()->user()->first_name . ' ' . auth()->user()->last_name }}</div>
                                            <div style="font-size: 13px; color: gray;">{{ __('master-job-taker.peran_pelanggan')}}</div>
                                        </div>
                                    </a>
                             
                                {{-- Ganti href="#" dengan link ke halaman saldo jika ada --}}
                                 <a class="dropdown-item d-flex align-items-center"
                                        href="{{ route('balance.job-req') }}">
                                        {{-- Sisi Kiri: Ikon dan Teks --}}
                                        <div class="d-flex align-items-center gap-2">
                                            {{-- Pastikan Anda punya ikon untuk saldo, contoh: icon-wallet.svg --}}
                                            <img src="{{ asset('Image/Icon/icon-wallet.svg') }}" alt="Icon Saldo"
                                                class="navIcon">
                                            <span>{{ __('master-job-req.saldo') }}</span>
                                        </div>
                                        {{-- Sisi Kanan: Jumlah Saldo --}}
                                        <span class="ms-auto fw-bold">
                                            {{-- Asumsi saldo tersimpan di kolom 'saldo' pada tabel user --}}
                                            {{-- Fungsi number_format untuk format Rupiah --}}
                                            Rp{{ number_format(auth()->user()->balance, 0, ',', '.') }}
                                        </span>
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item d-flex align-items-center gap-1"
                                            onclick="event.preventDefault(); this.closest('form').submit();">
                                            <img src="{{ asset('Image/Icon/icon-logout.svg') }}" alt="Icon Logout"
                                                class="navIcon">
                                            {{ __('master-job-req.keluar') }}
                                        </button>
                                    </form>
                                </li>
                                @if (auth()->user()->role === 'admin')
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center gap-1"
                                            href="{{ route('admin.dashboard') }}">
                                            <img src="{{ asset('Image/Icon/icon-change-role.svg') }}"
                                                alt="Icon Ganti Peran" class="navIcon">
                                            Admin
                                        </a>
                                    </li>
                                @endif
                                @if (auth()->user()->is_worker)
                                    {{-- JIKA SUDAH JADI WORKER: Tampilkan tombol "Ganti Peran" --}}
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center gap-1"
                                            href="{{ route('switch.to.taker') }}">
                                            <img src="{{ asset('Image/Icon/icon-change-role.svg') }}"
                                                alt="Icon Ganti Peran" class="navIcon">
                                            {{ __('master-job-req.ganti_peran') }}
                                        </a>
                                    </li>
                                @else
                                    {{-- JIKA BELUM JADI WORKER: Tampilkan tombol "Menjadi Mitra" --}}
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center gap-1"
                                            href="{{ route('worker.register.step1') }}">
                                            <img src="{{ asset('Image/Icon/icon-join.svg') }}" alt="Icon Menjadi Mitra"
                                                class="navIcon">
                                            {{ __('master-job-req.menjadi_mitra') }}
                                        </a>
                                    </li>
                                @endif
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
                    {{ __('master-job-req.footer_quote') }}
                </p>
            </div>

            <div class="col-6 col-md-3 col-lg-2 foot-content-detail" id="foot-2">
                <h4>{{ __('master-job-req.fitur') }}</h4>
                <div class="list-group gap-2">
                    @auth
                        <a href="/job-req/beranda" class="foot-list">{{ __('master-job-req.beranda') }}</a>
                    @else
                        <a href="#" data-bs-toggle="modal" data-bs-target="#loginModal"
                            class="foot-list">{{ __('master-job-req.beranda') }}</a>
                    @endauth


                    @auth
                        <a href="/job-req/tawarkan-kerja" class="foot-list">{{ __('master-job-req.tawarkan_kerja') }}</a>
                    @else
                        <a href="#" data-bs-toggle="modal" data-bs-target="#loginModal"
                            class="foot-list">{{ __('master-job-req.tawrakan_kerja') }}</a>
                    @endauth


                    @auth
                        <a href="/job-req/pesan" class="foot-list">{{ __('master-job-req.pesan') }}</a>
                    @else
                        <a href="#" data-bs-toggle="modal" data-bs-target="#loginModal"
                            class="foot-list">{{ __('master-job-req.pesan') }}</a>
                    @endauth


                    @auth
                        <a href="/job-req/riwayat" class="foot-list">{{ __('master-job-req.riwayat') }}</a>
                    @else
                        <a href="#" data-bs-toggle="modal" data-bs-target="#loginModal"
                            class="foot-list">{{ __('master-job-req.riwayat') }}</a>
                    @endauth
                </div>
            </div>

            <div class="col-6 col-md-3 col-lg-2 foot-content-detail" id="foot-3">
                <h4>{{ __('master-job-req.bantuan') }}</h4>
                <div class="list-group gap-2">
                    @auth
                        <a href="#" class="foot-list">{{ __('master-job-req.acara') }}</a>
                        <a href="#" class="foot-list">{{ __('master-job-req.profil') }}</a>
                    @else
                        <a class="foot-list" href="#" data-bs-toggle="modal"
                            data-bs-target="#loginModal">{{ __('master-job-req.acara') }}</a>
                        <a class="foot-list" href="#" data-bs-toggle="modal"
                            data-bs-target="#loginModal">{{ __('master-job-req.promo') }}</a>
                    @endauth
                </div>
            </div>

            <div class="col-6 col-md-3 col-lg-2 foot-content-detail" id="foot-4">
                <h4>{{ __('master-job-req.bantuan') }}</h4>
                <div class="list-group gap-2">
                    @auth
                        <a href="#" class="foot-list">{{ __('master-job-req.akun') }}</a>
                        <a href="#" class="foot-list">{{ __('master-job-req.laporkan') }}</a>
                        <a href="#" class="foot-list">{{ __('master-job-req.saran') }}</a>
                    @else
                        <a class="foot-list" href="#" data-bs-toggle="modal"
                            data-bs-target="#loginModal">{{ __('master-job-req.akun') }}</a>
                        <a class="foot-list" href="#" data-bs-toggle="modal"
                            data-bs-target="#loginModal">{{ __('master-job-req.laporkan') }}</a>
                        <a class="foot-list" href="#" data-bs-toggle="modal"
                            data-bs-target="#loginModal">{{ __('master-job-req.saran') }}</a>
                    @endauth
                </div>
            </div>

            <div class="col-6 col-md-auto col-lg-auto foot-content-detail" id="foot-5">
                <h4>{{ __('master-job-req.hubungi_kami')}}</h4>
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
            <p class="m-0 p-0" id="text-copyright">{{ __('master-job-req.copyright') }}</p>
        </div>
    </footer>
    {{-- End Footer --}}


    {{-- Pop Up Login --}}
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="max-width: 400px;">
            <div class="modal-content bg-white rounded" style="box-shadow: none !important; border: none !important;">
                <div class="modal-header border-0 position relative">
                    <h1 class="modal-title w-100 text-center mb-0 fs-4">{{ __('master-job-req.masuk') }}</h1>
                    <button type="button" class="btn-close position-absolute end-0 me-3" data-bs-dismiss="modal"
                        aria-label="Tutup"></button>
                </div>
                <form id="login-form" method="POST" action="{{ route('login') }}" style="padding: 30px">
                    @csrf
                    <div class="mb-3 position-relative" style="max-height: 75px; height: 100%;">
                        <label for="email-login" class="form-label">{{ __('master-job-req.email') }}</label>
                        <input id="email-login" class="form-control" type="email" name="email"
                            autocomplete="new-email" required>
                        <div id="Loginemail-error" class="popup-error-card d-none"></div>
                    </div>

                    <div class="mb-3 position-relative" style="max-height: 75px; height: 100%;">
                        <label for="password-login" class="form-label">{{ __('master-job-req.kata_sandi') }}</label>
                        <input id="password-login" class="form-control" type="password" name="password"
                            autocomplete="new-password" required>
                        <div id="Loginpassword-error" class="popup-error-card d-none"></div>
                    </div>

                    <div class="d-flex justify-content-between mb-4">
                        <div class="form-check">
                            <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
                            <label class="form-check-label">{{ __('master-job-req.ingat_saya') }}</label>
                        </div>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}">{{ __('master-job-req.lupa_kata_sandi') }}</a> @endif
                    </div>

                    <button type="submit"
        class="btn btn-primary w-100 mb-3 py-2">{{ __('master-job-req.masuk') }}</button>

    <div class="text-center">
        <p class="mb-2">{{ __('master-job-req.belum_punya_akun') }}
            <button type="button" class="btn btn-link p-0" data-bs-toggle="modal" data-bs-target="#logoutModal">
                {{ __('master-job-req.daftar') }}
            </button>
        </p>
        <p class="mb-2">{{ __('master-job-req.atau_masuk_dengan') }}:</p>
        <a href="{{ route('auth-google-redirect') }}">
            <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google" width="24">
        </a>
    </div>
    </form>
    </div>
    </div>
    </div>
    {{-- End Pop Up Login --}}

    {{-- Pop Up Register --}}
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="max-width: 800px;">
            <div class="modal-content bg-white rounded" style="box-shadow: none !important; border: none !important;">
                <div class="modal-header border-0 position-relative d-flex justify-content-center align-items-center">
                    <h1 class="mb-0 fs-4" style="font-weight: 500">{{ __('master-job-req.daftar') }}</h1>
                    <button type="button" class="btn-close position-absolute end-0 me-3" data-bs-dismiss="modal"
                        aria-label="Tutup"></button>
                </div>
                <form method="POST" action="{{ route('register') }}" style="padding: 30px">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="mb-3 position-relative" style="max-height: 75px; height: 100%;">
                                <label class="form-label"
                                    for="first_name">{{ __('master-job-req.nama_depan') }}</label>
                                <input type="text" id="first_name" class="form-control" name="first_name" required />
                                <div id="first_name-error" class="popup-error-card d-none"></div>
                            </div>

                            <div class="mb-3 position-relative" style="max-height: 75px; height: 100%;">
                                <label class="form-label"
                                    for="last_name">{{ __('master-job-req.nama_belakang') }}</label>
                                <input type="text" id="last_name" class="form-control" name="last_name" required />
                                <div id="last_name-error" class="popup-error-card d-none"></div>
                            </div>

                            <div class="mb-3 position-relative" style="max-height: 75px; height: 100%;">
                                <label for="email-register" class="form-label">{{ __('master-job-req.email') }}</label>
                                <input id="email-register" class="form-control" type="email" name="email"
                                    autocomplete="new-email" required>
                                <div id="email-error" class="popup-error-card d-none"></div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="mb-3 position-relative" style="max-height: 75px; height: 100%;">
                                <label class="form-label"
                                    for="password">{{ __('master-job-req.kata_sandi') }}</label>
                                <input id="password" class="form-control" type="password" name="password"
                                    required />
                                <div id="password-error" class="popup-error-card d-none"></div>
                            </div>

                            <div class="mb-3 position-relative" style="max-height: 75px; height: 100%;">
                                <label class="form-label"
                                    for="password_confirmation">{{ __('master-job-req.konfirmasi_kata_sandi') }}</label>
                                <input class="form-control" id="password_confirmation" type="password"
                                    name="password_confirmation" required />
                                <div id="confirm_password-error" class="popup-error-card d-none"></div>
                            </div>

                            <div class="mb-3 position-relative" style="max-height: 75px; height: 100%;">
                                <label class="form-label" for="otp">{{ __('master-job-req.kode_otp') }}</label>
                                <div class="d-flex align-items-center gap-2 otp-container">
                                    <input id="otp" class="form-control" type="text" name="otp"
                                        maxlength="6" placeholder="Masukkan Kode OTP" required />
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

                        <div class="col-12 bottom-column">
                            <div class="relative h-5">
                                <div id="otp-message" class="absolute inset-0 text-sm hidden text-center mb-2"
                                    style="color: #16a34a;">
                                </div>
                            </div>

                            <button type="submit"
                                class="btn btn-primary w-100 mb-3 py-2">{{ __('master-job-req.daftar') }}</button>

                            <div class="text-center">
                                <p class="mb-2">{{ __('master-job-req.sudah_punya_akun') }}
                                    <button type="button" class="btn btn-link p-0" data-bs-toggle="modal"
                                        data-bs-target="#loginModal">
                                        {{ __('master-job-req.masuk') }}
                                    </button>
                                </p>
                                <p>{{ __('master-job-req.atau_daftar_dengan') }}</p>
                                <a href="{{ route('auth-google-redirect') }}"
                                    class="btn btn-link btn-floating mx-1">
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


    <script defer>
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

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        const passwordRules = {
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

        // Modified validateInput: Now it ONLY handles the 'is-invalid' class and error text.
        // It returns true if valid, false if invalid.
        function validateInput(inputElement, errorDiv, validationLogic) {
            const errors = validationLogic(inputElement.value.trim());
            if (errors.length > 0) {
                errorDiv.innerHTML = `<ul class="mb-0">${errors.map(err => `<li>${err}</li>`).join('')}</ul>`;
                inputElement.classList.add('is-invalid'); // Add red border
                return false;
            } else {
                errorDiv.innerHTML = '';
                inputElement.classList.remove('is-invalid'); // Remove red border
                return true;
            }
        }


        // --- Validation Logic Functions (No change here) ---
        function validateFirstName(value) {
            const errors = [];
            if (value === '') {
                errors.push('Nama depan diperlukan.');
            }
            return errors;
        }

        function validateLastName(value) {
            const errors = [];
            if (value === '') {
                errors.push('Nama belakang diperlukan.');
            }
            return errors;
        }

        function validateRegisterEmail(value) {
            const errors = [];
            if (value === '') {
                errors.push('Email harus diisi.');
            } else if (!emailRegex.test(value)) {
                errors.push('Silakan masukkan alamat email yang valid.');
            }
            return errors;
        }

        function validatePasswordStrength(value) {
            const errors = [];
            if (value === '') {
                errors.push('Password harus diisi.');
            }
            for (const key in passwordRules) {
                if (!passwordRules[key].test(value)) {
                    errors.push(passwordRules[key].message);
                }
            }
            return errors;
        }

        function validateConfirmPassword(value) {
            const errors = [];
            const originalPassword = passwordInput.value;
            if (value === '') {
                errors.push('Konfirmasi kata sandi harus diisi.');
            } else if (value !== originalPassword) {
                errors.push('Kata sandi tidak cocok.');
            }
            return errors;
        }

        function validateOtp(value) {
            const errors = [];
            if (value === '') {
                errors.push('OTP harus diisi.');
            } else if (!/^\d{6}$/.test(value)) {
                errors.push('OTP harus berupa 6 digit angka.');
            }
            return errors;
        }

        // Function to set up common blur/focus/input listeners
        function setupValidationListeners(inputElement, errorDiv, validationLogic) {
            inputElement.addEventListener('blur', function() {
                // Validate on blur. If invalid, keep red border. Always hide text on blur.
                validateInput(inputElement, errorDiv, validationLogic);
                errorDiv.classList.add('d-none');
            });

            inputElement.addEventListener('focus', function() {
                // Validate on focus. If invalid, show error text.
                const isValid = validateInput(inputElement, errorDiv, validationLogic);
                if (!isValid) {
                    errorDiv.classList.remove('d-none');
                }
            });

            inputElement.addEventListener('input', function() {
                // Live validation. If invalid, show error text. If valid, hide error text.
                const isValid = validateInput(inputElement, errorDiv, validationLogic);
                if (!isValid) {
                    errorDiv.classList.remove('d-none'); // Keep error text visible while typing if invalid
                } else {
                    errorDiv.classList.add('d-none'); // Hide error text if it becomes valid while typing
                }
            });
        }

        // --- Apply listeners to Login Form Inputs ---
        setupValidationListeners(loginEmailInput, loginEmailErrorDiv, (value) => {
            const errors = [];
            if (value.trim() === '') { // Use trim() for consistency
                errors.push('Email harus diisi.');
            } else if (!emailRegex.test(value.trim())) {
                errors.push('Silakan masukkan alamat email yang valid.');
            }
            return errors;
        });
        setupValidationListeners(loginPasswordInput, loginPasswordErrorDiv, validatePasswordStrength);


        const loginModal = document.getElementById('loginModal');
        const loginForm = loginModal.querySelector('form');

        loginForm.addEventListener('submit', function(event) {
            let hasClientErrors = false;

            // Perform full validation for submission
            const isEmailValid = validateInput(loginEmailInput, loginEmailErrorDiv, (value) => {
                const errors = [];
                if (value.trim() === '') {
                    errors.push('Email harus diisi.');
                } else if (!emailRegex.test(value.trim())) {
                    errors.push('Silakan masukkan alamat email yang valid.');
                }
                return errors;
            });

            const isPasswordValid = validateInput(loginPasswordInput, loginPasswordErrorDiv,
                validatePasswordStrength);

            if (!isEmailValid || !isPasswordValid) {
                hasClientErrors = true;
                // Ensure error messages are shown on submission if there are errors
                if (!isEmailValid) loginEmailErrorDiv.classList.remove('d-none');
                if (!isPasswordValid) loginPasswordErrorDiv.classList.remove('d-none');
            }


            if (hasClientErrors) {
                event.preventDefault(); // Prevent form submission if client-side errors exist
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            // --- Apply listeners to Register Form Inputs ---
            setupValidationListeners(firstNameInput, firstNameErrorDiv, validateFirstName);
            setupValidationListeners(lastNameInput, lastNameErrorDiv, validateLastName);
            setupValidationListeners(emailInput, emailErrorDiv, validateRegisterEmail);
            setupValidationListeners(passwordInput, passwordErrorDiv, validatePasswordStrength);
            setupValidationListeners(confirmPasswordInput, confirmPasswordErrorDiv, validateConfirmPassword);
            setupValidationListeners(otpInput, otpErrorDiv, validateOtp);

            // Special handling for passwordInput's input event to re-validate confirmPasswordInput
            passwordInput.addEventListener('input', function() {
                // First, validate the password input itself
                validateInput(passwordInput, passwordErrorDiv, validatePasswordStrength);
                if (passwordInput.classList.contains('is-invalid')) {
                    passwordErrorDiv.classList.remove('d-none');
                } else {
                    passwordErrorDiv.classList.add('d-none');
                }

                // Then, re-validate confirm password if it has a value, to reflect password changes
                if (confirmPasswordInput.value.trim() !== '') {
                    validateInput(confirmPasswordInput, confirmPasswordErrorDiv, validateConfirmPassword);
                    if (confirmPasswordInput.classList.contains('is-invalid') && document.activeElement ===
                        confirmPasswordInput) {
                        confirmPasswordErrorDiv.classList.remove(
                            'd-none'); // Show if user is actively on it
                    } else {
                        confirmPasswordErrorDiv.classList.add('d-none'); // Hide if not focused
                    }
                }
            });

            confirmPasswordInput.addEventListener('input', function() {
                validateInput(confirmPasswordInput, confirmPasswordErrorDiv, validateConfirmPassword);
                if (confirmPasswordInput.classList.contains('is-invalid')) {
                    confirmPasswordErrorDiv.classList.remove('d-none');
                } else {
                    confirmPasswordErrorDiv.classList.add('d-none');
                }
            });


            // --- Register Form Submission Logic (logoutModal is actually the Register Modal) ---
            const registerModal = document.getElementById('logoutModal');
            const registerForm = registerModal.querySelector('form');

            registerForm.addEventListener('submit', function(event) {
                let hasErrors = false;

                // Perform full validation for submission, ensuring error messages are shown
                const isFirstNameValid = validateInput(firstNameInput, firstNameErrorDiv,
                    validateFirstName);
                if (!isFirstNameValid) {
                    firstNameErrorDiv.classList.remove('d-none');
                    hasErrors = true;
                }
                const isLastNameValid = validateInput(lastNameInput, lastNameErrorDiv, validateLastName);
                if (!isLastNameValid) {
                    lastNameErrorDiv.classList.remove('d-none');
                    hasErrors = true;
                }
                const isEmailValid = validateInput(emailInput, emailErrorDiv, validateRegisterEmail);
                if (!isEmailValid) {
                    emailErrorDiv.classList.remove('d-none');
                    hasErrors = true;
                }
                const isOtpValid = validateInput(otpInput, otpErrorDiv, validateOtp);
                if (!isOtpValid) {
                    otpErrorDiv.classList.remove('d-none');
                    hasErrors = true;
                }

                const isPasswordStrong = validateInput(passwordInput, passwordErrorDiv,
                    validatePasswordStrength);
                let isConfirmPasswordValid = true;

                if (!isPasswordStrong) {
                    hasErrors = true;
                    // Clear passwords if main password validation fails
                    passwordInput.value = '';
                    confirmPasswordInput.value = '';
                    // Ensure the password error is visible
                    passwordErrorDiv.classList.remove('d-none');
                    // Hide confirm password error text and ensure it's not marked invalid
                    confirmPasswordInput.classList.remove('is-invalid');
                    confirmPasswordErrorDiv.classList.add('d-none');
                    confirmPasswordErrorDiv.innerHTML = '';
                } else {
                    // Only validate confirm password if the main password is strong
                    isConfirmPasswordValid = validateInput(confirmPasswordInput, confirmPasswordErrorDiv,
                        validateConfirmPassword);
                    if (!isConfirmPasswordValid) {
                        hasErrors = true;
                        // Clear passwords if confirm password validation fails
                        passwordInput.value = '';
                        confirmPasswordInput.value = '';
                        // Ensure the confirm password error is visible
                        confirmPasswordErrorDiv.classList.remove('d-none');
                    }
                }

                if (hasErrors) {
                    event.preventDefault(); // Prevent form submission if any validation fails
                } else {
                    // If all client-side validations pass, allow the form to submit normally.
                }
            });
        });

        const otpMessage = document.getElementById('otp-message');
        let countdownInterval = null; // Store interval to clear it

        const sendButton = document.getElementById('send-otp-button');
        sendButton.addEventListener('click', function() {
            const email = document.getElementById('email-register').value.trim();
            // Only proceed if email field is not empty and has no client-side validation errors
            if (!email) {
                validateInput(emailInput, emailErrorDiv, validateRegisterEmail); // Show email error if empty
                emailErrorDiv.classList.remove('d-none'); // Ensure text error is shown immediately
                return;
            } else if (emailInput.classList.contains('is-invalid')) {
                emailErrorDiv.classList.remove('d-none'); // Ensure text error is shown immediately
                return; // Don't send OTP if email is invalid
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
                        {{ \App\Http\Controllers\Auth\RegisteredUserController::OTP_COOLDOWN_SECONDS }}
                    );
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

        function showCustomAlert(message, type = 'info', duration = 3000) {
            const alertContainer = document.getElementById('custom-alert-container');
            const customAlert = document.getElementById('custom-alert');
            const alertMessageSpan = document.getElementById('custom-alert-message');
            const alertCloseButton = document.getElementById('custom-alert-close');

            // Clear previous classes and reset state
            customAlert.classList.remove('alert-success-bg', 'alert-error-bg', 'alert-info-bg', 'alert-blue-bg',
                'show');
            customAlert.style.display = 'none';

            alertMessageSpan.textContent = message;
            if (type === 'success') {
                customAlert.classList.add('alert-success-bg');
            } else if (type === 'error') {
                customAlert.classList.add('alert-error-bg');
            } else if (type === 'info') {
                customAlert.classList.add('alert-info-bg');
            } else if (type === 'blue') {
                customAlert.classList.add('alert-blue-bg');
            }

            alertContainer.style.pointerEvents = 'auto';
            customAlert.style.display = 'flex';
            setTimeout(() => {
                customAlert.classList.add('show');
            }, 10);

            setTimeout(() => {
                customAlert.classList.remove('show');
                setTimeout(() => {
                    customAlert.style.display = 'none';
                    alertContainer.style.pointerEvents = 'none';
                }, 300);
            }, duration);

            alertCloseButton.onclick = () => {
                customAlert.classList.remove('show');
                setTimeout(() => {
                    customAlert.style.display = 'none';
                    alertContainer.style.pointerEvents = 'none';
                }, 300);
            };
        }

        // ***** NEW / MODIFIED CODE BELOW *****

        // Function to reset a single input field's state
        function resetInputField(inputElement, errorDiv) {
            inputElement.value = ''; // Clear input value
            inputElement.classList.remove('is-invalid'); // Remove red border
            errorDiv.innerHTML = ''; // Clear error message text
            errorDiv.classList.add('d-none'); // Hide error message div
        }

        // Get modal elements
        const loginModalElement = document.getElementById('loginModal');
        const registerModalElement = document.getElementById(
            'logoutModal'); // Still referring to this as logoutModal, but it's register

        // Add event listener for Login Modal close
        loginModalElement.addEventListener('hidden.bs.modal', function() {
            resetInputField(loginEmailInput, loginEmailErrorDiv);
            resetInputField(loginPasswordInput, loginPasswordErrorDiv);
            rememberMeCheckbox.checked = false; // Uncheck "Remember Me"
            loginForm.reset(); // Also good practice to reset the form itself
        });

        // Add event listener for Register Modal close
        registerModalElement.addEventListener('hidden.bs.modal', function() {
            resetInputField(firstNameInput, firstNameErrorDiv);
            resetInputField(lastNameInput, lastNameErrorDiv);
            resetInputField(emailInput, emailErrorDiv);
            resetInputField(passwordInput, passwordErrorDiv);
            resetInputField(confirmPasswordInput, confirmPasswordErrorDiv);
            resetInputField(otpInput, otpErrorDiv);

            // Also reset OTP message and button state if countdown is active
            if (countdownInterval) {
                clearInterval(countdownInterval);
                countdownInterval = null;
            }
            sendButton.disabled = false;
            sendButton.textContent = 'Kirim OTP';
            otpMessage.classList.add('hidden'); // Ensure OTP success/error message is hidden
            otpMessage.textContent = ''; // Clear OTP message text

            registerForm.reset(); // Reset the form itself
        });


        // ***** END NEW / MODIFIED CODE *****
    </script>

    {{-- SCRIPT TO AUTO-SHOW MODAL BASED ON SESSION FLASH --}}
    <script defer>
        document.addEventListener('DOMContentLoaded', function() {
            const successMessage = "{{ session('custom_success_alert') }}";
            const errorMessage = "{{ session('custom_error_alert') }}";
            const infoMessage = "{{ session('custom_info_alert') }}";
            const blueMessage = "{{ session('custom_blue_alert') }}";

            if (successMessage) {
                console.log('Flash message detected: Success -', successMessage);
                showCustomAlert(successMessage, 'success');
            } else if (errorMessage) {
                console.log('Flash message detected: Error -', errorMessage);
                showCustomAlert(errorMessage, 'error');
            } else if (blueMessage) {
                console.log('Flash message detected: Blue -', blueMessage);
                showCustomAlert(blueMessage, 'blue');
            } else if (infoMessage) {
                console.log('Flash message detected: Info -', infoMessage);
                showCustomAlert(infoMessage, 'info');
            }

            window.showCustomAlert = showCustomAlert;
        });

        // --- SCRIPT UNTUK MEMUTAR/MENGHENTIKAN LAGU LOGO ---
        document.addEventListener('DOMContentLoaded', function() {
            // CORRECTED: changed logoLink to logoNavbarLink
            const logoLink = document.getElementById('dropLang');
            const logoSoundtrack = document.getElementById('logoSoundtrack');

            if (logoLink && logoSoundtrack) {
                logoLink.addEventListener('click', function(event) {
                    // If you want to prevent the default navigation immediately, uncomment this line:
                    // event.preventDefault();

                    if (logoSoundtrack.paused) {
                        logoSoundtrack.play().catch(e => {
                            console.error("Autoplay diblokir atau error saat memutar:", e);
                            // If autoplay is blocked, still navigate to the link
                            // window.location.href = logoLink.href; // Only if you prevented default above
                        });
                    } else {
                        logoSoundtrack.pause();
                        logoSoundtrack.currentTime = 0; // Reset to start
                    }

                    // If you prevented default, you might want to navigate after a short delay
                    // to allow sound to start, or not at all depending on UX.
                    // For now, it will navigate as normal if event.preventDefault() is commented out.
                });
            }
        });
        // --- AKHIR SCRIPT UNTUK MEMUTAR/MENGHENTIKAN LAGU LOGO ---

        <
        !--jQuery-- >
        <
        script src = "../../plugins/jquery/jquery.min.js" >
    </script>
    <!-- Bootstrap 4 -->
    <script src="../../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <!-- AdminLTE for demo purposes -->
    <script src="../../dist/js/demo.js"></script>

    </body>

</html>
