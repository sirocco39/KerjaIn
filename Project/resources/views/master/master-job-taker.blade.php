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
        xintegrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/landingInfo.css') }}">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link rel="stylesheet" href="{{ asset('css/rating.css') }}">
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
    <!-- Custom Alert Container (Non-Modal) -->
    <div id="custom-alert-container">
        <div id="custom-alert">
            <span id="custom-alert-message"></span>
            <button type="button" id="custom-alert-close" aria-label="Close">&times;</button>
        </div>
    </div>

    {{-- Navbar Section --}}
    <nav class="navbar navbar-expand-lg bg-light fixed-top" id="mainNavbar">
        <div class="container-fluid pembatas-x">
            <a class="navbar-brand" href="/job_taker">
                <img src="{{ asset('Image/Logo/Logo Kerjain - LightBackground.png') }}" alt="Logo Kerjain"
                    id="logoNavbar">
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapseFull"
                aria-controls="navbarCollapseFull" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarCollapseFull">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0" id="navbarCollapse">
                    <li class="nav-item"><a class="nav-link {{ request()->is('job-taker/beranda') ? 'active' : '' }}"
                            href="/job-taker/beranda">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->is('job-taker/cari-kerja') ? 'active' : '' }}"
                            href="/job-taker/cari-kerja">Cari Kerja</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->is('job-taker/pesan') ? 'active' : '' }}"
                            href="/job-taker/pesan">Pesan</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->is('job-taker/riwayat') ? 'active' : '' }}"
                            href="/job-taker/riwayat">Riwayat</a></li>
                </ul>

                <hr class="d-lg-none my-2">

                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 d-flex align-items-lg-center">
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

                    <li class="nav-item dropdown" id="dropProfile">
                        <a class="nav-link" id="dropdownProfile" data-bs-toggle="dropdown" role="button">
                            <img src="{{ asset('Image/Icon/user-circle.svg') }}" alt="Profil" id="profileIcon">
                            <span class="d-lg-none">Profil</span>
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end m-0" aria-labelledby="dropdownProfile">
                            @auth
                                <li>
                                    {{-- Ganti href="#" dengan link ke halaman saldo jika ada --}}
                                    <a class="dropdown-item d-flex align-items-center"
                                        href="{{ route('balance.job-taker') }}">
                                        {{-- Sisi Kiri: Ikon dan Teks --}}
                                        <div class="d-flex align-items-center gap-2">
                                            {{-- Pastikan Anda punya ikon untuk saldo, contoh: icon-wallet.svg --}}
                                            <img src="{{ asset('Image/Icon/icon-wallet.svg') }}" alt="Icon Saldo"
                                                class="navIcon">
                                            <span>Saldo</span>
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
                                            Keluar
                                        </button>
                                    </form>
                                </li>
                                {{-- JIKA SUDAH JADI WORKER: Tampilkan tombol "Ganti Peran" --}}
                                <li>
                                    <a class="dropdown-item d-flex align-items-center gap-1"
                                        href="{{ route('switch.to.requester') }}">
                                        <img src="{{ asset('Image/Icon/icon-change-role.svg') }}" alt="Icon Ganti Peran"
                                            class="navIcon">
                                        Ganti Peran
                                    </a>
                                </li>
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
                    @auth
                        <a href="/job-req/beranda" class="foot-list">Beranda</a>
                    @else
                        <a href="#" data-bs-toggle="modal" data-bs-target="#loginModal"
                            class="foot-list">Beranda</a>
                    @endauth


                    @auth
                        <a href="/job-req/tawarkan-kerja" class="foot-list">Tawarkan Kerja</a>
                    @else
                        <a href="#" data-bs-toggle="modal" data-bs-target="#loginModal" class="foot-list">Tawarkan
                            Kerja</a>
                    @endauth


                    @auth
                        <a href="/job-req/pesan" class="foot-list">Pesan</a>
                    @else
                        <a href="#" data-bs-toggle="modal" data-bs-target="#loginModal"
                            class="foot-list">Pesan</a>
                    @endauth


                    @auth
                        <a href="/job-req/riwayat" class="foot-list">Riwayat</a>
                    @else
                        <a href="#" data-bs-toggle="modal" data-bs-target="#loginModal"
                            class="foot-list">Riwayat</a>
                    @endauth
                </div>
            </div>

            <div class="col-6 col-md-3 col-lg-2 foot-content-detail" id="foot-3">
                <h4>Penawaran</h4>
                <div class="list-group gap-2">
                    @auth
                        <a href="#" class="foot-list">Acara</a>
                        <a href="#" class="foot-list">Promo</a>
                    @else
                        <a class="foot-list" href="#" data-bs-toggle="modal"
                            data-bs-target="#loginModal">Acara</a>
                        <a class="foot-list" href="#" data-bs-toggle="modal"
                            data-bs-target="#loginModal">Promo</a>
                    @endauth
                </div>
            </div>

            <div class="col-6 col-md-3 col-lg-2 foot-content-detail" id="foot-4">
                <h4>Bantuan</h4>
                <div class="list-group gap-2">
                    @auth
                        <a href="#" class="foot-list">Akun</a>
                        <a href="#" class="foot-list">Laporkan</a>
                        <a href="#" class="foot-list">Saran</a>
                    @else
                        <a class="foot-list" href="#" data-bs-toggle="modal" data-bs-target="#loginModal">Akun</a>
                        <a class="foot-list" href="#" data-bs-toggle="modal"
                            data-bs-target="#loginModal">Laporkan</a>
                        <a class="foot-list" href="#" data-bs-toggle="modal"
                            data-bs-target="#loginModal">Saran</a>
                    @endauth
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

    <script defer>
        // Custom Alert function (copied from master-job-req.blade.php's original)
        window.showCustomAlert = function(message, type = 'info', duration = 3000) {
            const alertContainer = document.getElementById('custom-alert-container');
            const customAlert = document.getElementById('custom-alert');
            const alertMessageSpan = document.getElementById('custom-alert-message');
            const alertCloseButton = document.getElementById('custom-alert-close');

            // Clear previous classes and reset state
            customAlert.classList.remove('alert-success-bg', 'alert-error-bg', 'alert-info-bg', 'show');
            customAlert.style.display = 'none'; // Hide it initially for transition

            // Set message and type-specific background
            alertMessageSpan.textContent = message;
            if (type === 'success') {
                customAlert.classList.add('alert-success-bg');
            } else if (type === 'error') {
                customAlert.classList.add('alert-error-bg');
            } else if (type === 'blue') {
                customAlert.classList.add('alert-blue-bg');
            } else if (type === 'info') {
                customAlert.classList.add('alert-info-bg');
            }

            // Show the alert with a slight delay for CSS transition to work
            alertContainer.style.pointerEvents = 'auto'; // Make container clickable when visible
            customAlert.style.display = 'flex'; // Make it visible
            setTimeout(() => {
                customAlert.classList.add('show');
            }, 10); // Small delay

            // Set timeout to hide the alert
            setTimeout(() => {
                customAlert.classList.remove('show');
                setTimeout(() => {
                    customAlert.style.display = 'none';
                    alertContainer.style.pointerEvents =
                        'none'; // Make container unclickable when hidden
                }, 300); // Match CSS transition duration
            }, duration);

            // Close button functionality
            alertCloseButton.onclick = () => {
                customAlert.classList.remove('show');
                setTimeout(() => {
                    customAlert.style.display = 'none';
                    alertContainer.style.pointerEvents = 'none'; // Make container unclickable when hidden
                }, 300); // Match CSS transition duration
            };
        };
    </script>

    {{-- SCRIPT TO AUTO-SHOW MODAL BASED ON SESSION FLASH --}}
    <script defer>
        document.addEventListener('DOMContentLoaded', function() {
            // Check for flash messages from Laravel
            const successMessage = "{{ session('custom_success_alert') }}";
            const errorMessage = "{{ session('custom_error_alert') }}";
            const infoMessage = "{{ session('custom_info_alert') }}";
            const blueMessage = "{{ session('custom_blue_alert') }}"; // New: Check for blue alert message

            if (successMessage) {
                console.log('Flash message detected: Success -', successMessage);
                showCustomAlert(successMessage, 'success'); // Changed to 'success' type for success messages
            } else if (errorMessage) {
                console.log('Flash message detected: Error -', errorMessage);
                showCustomAlert(errorMessage, 'error');
            } else if (blueMessage) { // New: Condition for custom blue alert
                console.log('Flash message detected: Blue -', blueMessage);
                showCustomAlert(blueMessage, 'blue'); // Use 'blue' type for the new color
            } else if (infoMessage) {
                console.log('Flash message detected: Info -', infoMessage);
                showCustomAlert(infoMessage, 'info');
            }

            // Expose showAlert globally if needed by other scripts (e.g., for AJAX responses)
            window.showCustomAlert = showCustomAlert;
        });
    </script>
</body>

</html>
