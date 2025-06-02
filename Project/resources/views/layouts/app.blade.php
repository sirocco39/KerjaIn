<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>KerjaIn</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
</head>
<body>

  <!-- Navbar -->
<nav class="navbar navbar-expand-lg bg-white shadow-sm">
    <div class="container">
      <a class="navbar-brand fw-bold text-primary" href="#">Kerja<span class="text-warning">In</span></a>
      <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto me-3">
          <li class="nav-item"><a class="nav-link" href="#">Beranda</a></li>
          <li class="nav-item"><a class="nav-link" href="#">Cari Kerja</a></li>
          <li class="nav-item"><a class="nav-link" href="#">Pesan</a></li>
          <li class="nav-item"><a class="nav-link" href="#">Riwayat</a></li>
        </ul>
        <div class="dropdown me-2">
          <button class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
            Bahasa
          </button>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#">Indonesia</a></li>
            <li><a class="dropdown-item" href="#">English</a></li>
          </ul>
        </div>
        <a class="btn btn-outline-dark" href="#"><i class="bi bi-person-circle"></i></a>
      </div>
    </div>
  </nav>

  <!-- Main Content -->
  <main>
    @yield('content')
  </main>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
</body>
</html>
