<html lang="en">
<head>
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Manrope:wght@200..800&display=swap" rel="stylesheet">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <title>Document</title>
</head>
<body>
    <nav class="navbar navbar-expand-lg bg-light shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold text-primary" href="#">Kerja<span class="text-dark">In</span></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarMenu">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="#">Beranda</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Cari Kerja</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Pesan</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Riwayat</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- Header -->
<section class="bg-header text-white text-center py-4">
  <div class="container">
    <h4>Yuk, jadi bagian dari KerjaIn – tempat di mana kerja bareng, bantu bareng, cuan bareng.</h4>
  </div>
</section>

<!-- Form Join -->
<section class="py-5">
  <div class="container">
    <div class="bg-white shadow rounded p-4 p-md-5">
      <!-- Stepper -->
      <div class="mb-4 text-center">
        <div class="d-flex justify-content-center gap-4">
          <div><strong class="text-primary">1</strong> Data Pribadi</div>
          <div><strong>2</strong> Detail Kontrak</div>
          <div><strong>3</strong> Verifikasi</div>
        </div>
      </div>

      <!-- Form -->
      <form>
        <div class="row g-3">
          <div class="col-12 col-md-6">
            <label class="form-label">Nama Depan</label>
            <input type="text" class="form-control" placeholder="Nama depan">
          </div>
          <div class="col-12 col-md-6">
            <label class="form-label">Nama Belakang</label>
            <input type="text" class="form-control" placeholder="Nama belakang">
          </div>

          <div class="col-12 col-md-6">
            <label class="form-label">Nomor KTP</label>
            <input type="text" class="form-control" placeholder="Nomor KTP/NIK">
          </div>
          <div class="col-12 col-md-6">
            <label class="form-label">Tanggal Lahir</label>
            <input type="date" class="form-control">
          </div>

          <div class="col-12 col-md-6">
            <label class="form-label">Jenis Kelamin</label>
            <select class="form-select">
              <option selected disabled>Jenis Kelamin</option>
              <option>Laki-laki</option>
              <option>Perempuan</option>
            </select>
          </div>
          <div class="col-12 col-md-6">
            <label class="form-label">Alamat</label>
            <input type="text" class="form-control" placeholder="Alamat Domisili">
          </div>

          <div class="col-12 col-md-6">
            <label class="form-label">Nomor Telepon</label>
            <input type="text" class="form-control" placeholder="Nomor Telepon">
          </div>
          <div class="col-12 col-md-6">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" placeholder="Email">
          </div>

          <div class="col-12 text-center mt-4">
            <button type="submit" class="btn btn-primary px-5">Lanjut</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</section>

<!-- Footer -->
<footer class="bg-dark text-white mt-5 py-4">
  <div class="container text-center text-md-start">
    <div class="row g-4">
      <div class="col-md-3">
        <h5>Kerja<span class="text-primary">In</span></h5>
        <p class="small">Kami selalu mengusahakan yang terbaik buat pelanggan dan memberikan pelayanan terbaik yang kami bisa.</p>
      </div>
      <div class="col-md-2">
        <h6>Company</h6>
        <ul class="list-unstyled small">
          <li>About us</li>
          <li>Features</li>
          <li>News</li>
          <li>FAQ</li>
        </ul>
      </div>
      <div class="col-md-2">
        <h6>Resources</h6>
        <ul class="list-unstyled small">
          <li>Event</li>
          <li>Promo</li>
          <li>Req Demo</li>
        </ul>
      </div>
      <div class="col-md-2">
        <h6>Support</h6>
        <ul class="list-unstyled small">
          <li>Account</li>
          <li>Support Center</li>
          <li>Feedback</li>
          <li>Accessibility</li>
        </ul>
      </div>
      <div class="col-md-3">
        <h6>Contact Us</h6>
        <p class="small">kerjain@gmail.com</p>
        <div class="d-flex gap-2">
          <a href="#" class="text-white">📸</a>
          <a href="#" class="text-white">📘</a>
          <a href="#" class="text-white">🐦</a>
        </div>
      </div>
    </div>
  </div>
</footer>
</body>
</html>