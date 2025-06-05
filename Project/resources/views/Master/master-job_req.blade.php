<!DOCTYPE html>
<html lang="en">
<head>
    @vite(["resources/sass/app.scss","resources/js/app.js"])

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>KerjaIn</title>
    <link rel="icon" href="../Image/Icon/Icon Kerjain.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

</head>

<body>
    {{-- Navbar Section --}}
    <nav class="navbar navbar-expand-lg bg-light">
        <div class="container-fluid pembatas">
            <a class="navbar-brand" href="/">
                <img src="../Image/Logo/Logo Kerjain - LightBackground.png" alt="Logo Kerjain" id="logoNavbar">
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapseFull" aria-controls="navbarCollapseFull" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarCollapseFull">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0" id="navbarCollapse">
                    <li class="nav-item"><a class="nav-link {{ request()->is('job-req/beranda') ? 'active' : '' }}" href="/job-req/beranda" aria-current="true">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->is('job-req/tawarkan-kerja') ? 'active' : '' }}" href="/job-req/tawarkan-kerja">Tawarkan Kerja</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->is('job-req/pesan') ? 'active' : '' }}" href="/job-req/pesan">Pesan</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->is('job-req/riwayat') ? 'active' : '' }}" href="/job-req/riwayat">Riwayat</a></li>
                </ul>

                <hr class="d-xl-none my-2">

                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 d-flex align-items-lg-center">
                    <!-- Dropdown Bahasa -->
                    <li class="nav-item dropdown" id="dropLang">
                        <a class="nav-link" id="dropdownLang" data-bs-toggle="dropdown" role="button">
                            <img src="../Image/Flag/flag-id.png" alt="Bahasa" id="langFlag">
                            <span>Bahasa</span>
                            <i class="bi bi-chevron-down" id="langIcon"></i>
                        </a>

                        <ul class="dropdown-menu" aria-labelledby="dropdownLang">
                            <li><a class="dropdown-item d-flex align-items-center" href="#"><img src="../Image/Flag/flag-id.png" alt="Indonesia's Flag" class="flag"> Bahasa</a></li>
                            <li><a class="dropdown-item d-flex align-items-center" href="#"><img src="../Image/Flag/flag-uk.png" alt="England's Flag" class="flag"> English</a></li>
                        </ul>
                    </li>

                    <!-- Dropdown Profile -->
                    <li class="nav-item dropdown" id="dropProfile">
                        <a class="nav-link" id="dropdownProfile" data-bs-toggle="dropdown" role="button">
                            <img src="../Image/Icon/user-circle.svg" alt="Profil" id="profileIcon">
                            <span class="d-lg-none">Profil</span>
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownProfile">
                            <li><a class="dropdown-item d-flex align-items-center gap-1" href="#"><img src="../Image/Icon/icon-login.svg" alt="Icon Login" class="navIcon">Masuk</a></li>
                            <li><a class="dropdown-item d-flex align-items-center gap-1" href="#"><img src="../Image/Icon/icon-logout.svg" alt="Icon Login" class="navIcon">Keluar</a></li>
                            <li><a class="dropdown-item d-flex align-items-center gap-1" href="#"><img src="../Image/Icon/icon-join.svg" alt="Icon Login" class="navIcon">Menjadi Mitra</a></li>
                            <li><a class="dropdown-item d-flex align-items-center gap-1" href="/job_taker"><img src="../Image/Icon/icon-change-role.svg" alt="Icon Login" class="navIcon">Ganti Peran</a></li>
                        </ul>
                    </li>
                </ul>

            </div>
        </div>
    </nav>

    {{-- End Navbar Section --}}


    {{-- Main Section --}}
    <div class="container-fluid pembatas">
        @yield('content')
    </div>

    {{-- End Main Section --}}


    {{-- Footer --}}

    {{-- End Footer --}}
</body>

</html>
