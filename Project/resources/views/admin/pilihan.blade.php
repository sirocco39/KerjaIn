<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilihan Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f8f9fa;
        }

        .card-choice {
            margin: 15px;
            padding: 30px;
            text-align: center;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease-in-out;
            cursor: pointer;
            width: 250px;
        }

        .card-choice:hover {
            transform: translateY(-5px);
        }

        .card-choice.requester {
            background-color: #e0f7fa;
            border: 1px solid #00bcd4;
        }

        .card-choice.taker {
            background-color: #e8f5e9;
            border: 1px solid #4caf50;
        }

        .card-choice.admin {
            background-color: #f3e5f5;
            border: 1px solid #9c27b0;
        }

        .card-choice h5 {
            margin-top: 15px;
            font-weight: bold;
        }

        .card-choice a {
            text-decoration: none;
            color: inherit;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="text-center mb-4">
                    {{-- Menampilkan alert dari controller jika ada --}}
                    @if(session('custom_info_alert'))
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        {{ session('custom_info_alert') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif
                    <h1>Pilih Dashboard untuk Admin</h1>
                    <p class="lead">Selamat datang! Silakan pilih halaman yang ingin Anda akses:</p>
                </div>

                <div class="d-flex justify-content-center flex-wrap">
                    <a href="{{ route('job-req.beranda') }}">
                        <div class="card-choice requester">
                            <h3><i class="bi bi-briefcase-fill"></i></h3>
                            <h5>Halaman Job Requester</h5>
                            <p>Mengelola permintaan pekerjaan.</p>
                        </div>
                    </a>
                    <a href="{{ route('job-taker.home') }}"> {{-- Ganti dengan rute job taker Anda jika ada --}}
                        <div class="card-choice taker">
                            <h3><i class="bi bi-person-workspace"></i></h3>
                            <h5>Halaman Job Taker</h5>
                            <p>Melihat dan menerima pekerjaan.</p>
                        </div>
                    </a>
                    <a href="{{ route('admin.dashboard') }}"> {{-- Ganti dengan rute dashboard admin Anda --}}
                        <div class="card-choice admin">
                            <h3><i class="bi bi-gear-fill"></i></h3>
                            <h5>Halaman Admin Utama</h5>
                            <p>Mengelola pengguna, laporan, dll.</p>
                        </div>
                    </a>
                </div>

                <div class="text-center mt-5">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-danger">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</body>

</html>