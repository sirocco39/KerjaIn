<!DOCTYPE html>
<html lang="en">

<head>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>KerjaIn</title>
    <link rel="icon" href="{{ asset('Image/Icon/Icon Kerjain.png') }}" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/rating.css') }}">

    <style>
        /* Main container for the tabs - now acts as the background for inactive tabs */
        .tabs-wrapper {
            height: 3.5rem;
            border-radius: 1.5rem;
            /* Rounded corners for the container */
            /* Subtle shadow for the container */
            position: relative;
            /* For z-index and positioning active tab */
            z-index: 0;
            display: flex;
            /* Ensure flex behavior */
            flex-wrap: wrap;
        }

        /* Base styles for all tab buttons */
        .tab-button {
            border-radius: 0.5rem;
            /* Slightly rounded for inactive tabs */
            color: #6b7280;
            /* text-gray-500 for inactive tabs */
            font-weight: 500;
            /* font-medium */
            transition: all 0.2s ease-in-out;
            background-color: transparent;
            /* Inactive tabs show container background */
            border: none;
            cursor: pointer;
            white-space: nowrap;
            position: relative;
            /* For z-index */
            z-index: 1;
            /* Inactive tabs are above the container background */
            display: flex;
            /* To center content if needed */
            align-items: center;
            justify-content: center;
        }

        .tab-button:hover {
            color: #4f46e5;
            /* text-indigo-600 on hover */
        }

        /* Active tab styling - this is the complex part */
        .tab-button.active {
            background-color: #ffffff;
            /* Solid white background for the active tab */
            color: #1f2937;
            /* Dark text for active tab */
            font-weight: 600;
            /* font-semibold */
            z-index: 10;
            /* Bring active tab to front, above container and other tabs */

            /* Custom shape using border-radius */
            border-top-left-radius: 1.5rem;
            /* Rounded top-left */
            border-top-right-radius: 1.5rem;
            /* Rounded top-right */
            border-bottom-left-radius: 0;
            /* Straight bottom-left */
            border-bottom-right-radius: 0;
            /* Straight bottom-right */

            /* Create the top lime-green border and bottom dark-blue border */
            border-top: 8px solid #bfff00;
            /* Thick lime green top border */

            /* Adjust padding to account for the borders */
            padding-top: calc(0.5rem - 8px);
            /* Reduce top padding by border thickness */
            padding-bottom: calc(0.5rem - 2px);
            /* Reduce bottom padding by border thickness */

            /* Adjust margin to make it slightly overlap the container's bottom edge */
            margin-bottom: -4px;
            /* Pulls it down by container's padding */
        }

        /* Styling for the status badges */
        .status-badge {
            border-radius: 9999px;
            /* Full rounded */
            font-size: 0.75rem;
            /* text-xs */
            font-weight: 500;
            /* font-medium */
            display: inline-block;
        }

        .hoverable-row {
            cursor: pointer;
            /* Indicates it's clickable */
            transition: background-color 0.2s ease, box-shadow 0.2s ease;
            /* Smooth transition for hover */
            border-radius: 0.25rem;
            /* Slightly rounded corners */
        }

        .hoverable-row:hover {
            background-color: #f8f9fa;
            /* Light grey background on hover */
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.175);
            /* Subtle shadow on hover */
        }
    </style>
</head>

<body>
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

                        <ul class="dropdown-menu dropdown-menu-end m-0" aria-labelledby="dropdownProfile">
                            @guest
                                <li><button type="button" class="dropdown-item d-flex align-items-center gap-1"
                                        data-bs-toggle="modal" data-bs-target="#loginModal"><img
                                            src="{{ asset('Image/Icon/icon-login.svg') }}" alt="Icon Login"
                                            class="navIcon">Masuk</button></li>
                            @endguest
                            @auth
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
                                        href="{{ route('job-req.beranda') }}">
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
                <a class="navbar-brand" href="/job_taker">
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
                    <a href="/job-taker/beranda" class="foot-list">Beranda</a>
                    <a href="/job-taker/cari-kerja" class="foot-list">Cari Kerja</a>
                    <a href="/job-taker/pesan" class="foot-list">Pesan</a>
                    <a href="/job-taker/riwayat" class="foot-list">Riwayat</a>
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
                    Ini Pop Up Login
                </div>
            </div>
        </div>
    </div>
    {{-- End Pop Up Login --}}


    {{-- Pop Up Logout --}}
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    Ini Pop Up Logout
                </div>
            </div>
        </div>
    </div>
    {{-- End Pop Up Logout --}}
</body>

</html>
