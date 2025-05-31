<!DOCTYPE html>
<html lang="en">
<head>
    @vite(["resources/sass/app.scss","resources/js/app.js"])

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>KerjaIn</title>
    <link rel="icon" href="./Image/Icon Kerjain.png" type="image/x-icon">
</head>

<body>
    {{-- Navbar Section --}}
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img src="./Image/Logo Kerjain - LightBackground.png" alt="Logo Kerjain" class="d-inline-block align-text-top" id="logoNavbar">
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarJobTaker" aria-controls="#navbarJobTaker" aria-expanded="false" aira-label="toggleNavigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarJobTaker">
                <ul class="navbar-nav">
                    <li>
                        <a class="nav-link" href="#">Beranda</a>
                    </li>

                    <li>
                        <a class="nav-link" href="#">Cari Kerja</a>
                    </li>

                    <li>
                        <a class="nav-link" href="#">Pesan</a>
                    </li>

                    <li>
                        <a class="nav-link" href="#">Riwayat</a>
                    </li>

                </ul>
            </div>
        </div>
    </nav>
    {{-- End Navbar Section --}}



    {{-- Main Section --}}

    {{-- End Main Section --}}



    {{-- Footer --}}

    {{-- End Footer --}}
</body>

</html>
