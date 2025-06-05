<!DOCTYPE html>
<html lang="en">
<head>
    @vite(["resources/sass/app.scss","resources/js/app.js"])

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>KerjaIn</title>
    <link rel="icon" href="{{ asset('Image/Icon/Icon Kerjain.png') }}" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>

<body>
    {{-- Navbar Section --}}
    <nav class="navbar navbar-expand-lg bg-light">
        <div class="container-fluid pembatas-x">
            <a class="navbar-brand" href="/">
                <img src="{{ asset('Image/Logo/Logo Kerjain - LightBackground.png') }}" alt="Logo Kerjain" id="logoNavbar">
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapseFull" aria-controls="navbarCollapseFull" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarCollapseFull">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0" id="navbarCollapse">
                    <li class="nav-item"><a class="nav-link {{ request()->is('job-req/beranda') ? 'active' : '' }}" href="/job-req/beranda">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->is('job-req/tawarkan-kerja') ? 'active' : '' }}" href="/job-req/tawarkan-kerja">Tawarkan Kerja</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->is('job-req/pesan') ? 'active' : '' }}" href="/job-req/pesan">Pesan</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->is('job-req/riwayat') ? 'active' : '' }}" href="/job-req/riwayat">Riwayat</a></li>
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
                            <li><a class="dropdown-item d-flex align-items-center" href="#"><img src="{{ asset('Image/Flag/flag-id.png') }}" alt="Indonesia's Flag" class="flag"> Bahasa</a></li>
                            <li><a class="dropdown-item d-flex align-items-center" href="#"><img src="{{ asset('Image/Flag/flag-uk.png') }}" alt="England's Flag" class="flag"> English</a></li>
                        </ul>
                    </li>

                    <!-- Dropdown Profile -->
                    <li class="nav-item dropdown" id="dropProfile">
                        <a class="nav-link" id="dropdownProfile" data-bs-toggle="dropdown" role="button">
                            <img src="{{ asset('Image/Icon/user-circle.svg') }}" alt="Profil" id="profileIcon">
                            <span class="d-lg-none">Profil</span>
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownProfile">
                            <li><button type="button" class="dropdown-item d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#loginModal"><img src="{{ asset('Image/Icon/icon-login.svg') }}" alt="Icon Login" class="navIcon">Masuk</button></li>
                            <li><button type="button" class="dropdown-item d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#logoutModal"><img src="{{ asset('Image/Icon/icon-logout.svg') }}" alt="Icon Logout" class="navIcon">Keluar</button></li>
                            <li><a class="dropdown-item d-flex align-items-center gap-1" href="#"><img src="{{ asset('Image/Icon/icon-join.svg') }}" alt="Icon Menjadi Mitra" class="navIcon">Menjadi Mitra</a></li>
                            <li><a class="dropdown-item d-flex align-items-center gap-1" href="/job_taker"><img src="{{ asset('Image/Icon/icon-change-role.svg') }}" alt="Icon Ganti Peran" class="navIcon">Ganti Peran</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    {{-- End Navbar Section --}}


    {{-- Main Section --}}
    @yield('content')
    {{-- End Main Section --}}


    {{-- Footer --}}
    <footer class="container-fluid pembatas-x footer">
        <div class="row d-flex flex-wrap justify-content-between align-items-start" id="foot-content">
            <div class="col-md-12 col-lg-3 foot-content-detail" id="foot-1">
                <a class="navbar-brand" href="/">
                    <img src="{{ asset('Image/Logo/Logo Kerjain - DarkBackground.png') }}" alt="Logo Kerjain" id="logoNavbar-footer">
                </a>
                <p class="m-0 p-0" id="foot-quotes">
                    Kami selalu mengusahakan yang terbaik buat pelanggan dan memberikan pelayanan terbaik yang kami bisa.
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
                    <img src="{{ asset('Image/Icon/icon-thread.png') }}" alt="Logo Twitter/Thread" class="foot-icon">
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
