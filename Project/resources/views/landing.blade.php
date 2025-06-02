@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="{{ asset('css/style.css') }}">

<!-- Manrope -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Manrope:wght@200..800&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

<!-- Inter -->
<link href=<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Manrope:wght@200..800&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">


<!-- Hero Section -->
<section class="py-5 text-start text-white" style="background-color: #294287; position: relative; overflow: hidden;">
  <div class="container d-flex position-relative align-items-center" style="z-index: 1;">
    <!-- Konten Teks -->
    <div class="text-content pe-5" style="transform: translateX(-100px); position: relative; z-index: 3;">
      <h1 class="display-2 fw-bold mb-3" style="font-family: 'Manrope'; white-space: nowrap;">Mau segala sesuatu selesai ?</h1>
      <h2 class="display-2 fw-semibold">Yuk, <span style="color: #D3FA0D">Kerja</span><span style="background-color: #309FFF; color: white; padding: 1px 8px; border-radius: 0.5rem;">In</span>!</h2>
      <p class="mt-5 fs-5" style="font-family: 'Inter';">Kerjain adalah solusi digital untuk kebutuhan kerja serabutan. Platform ini menghubungkan pemberi kerja dengan pekerja harian secara cepat, mudah, dan efisien. Temukan bantuan yang kamu butuhkan, atau mulai dapat penghasilan hari ini.</p>
      <div class="mt-5">
        <div class="garis-tengah"></div>
        <a href="#" class="btn-daftar">Daftar Sekarang</a>
        <a href="#" class="btn-pekerja">Cari Pekerja</a>
      </div>
    </div>
    
    <!-- Bulat-bulat Warna-warni - DIPINDAHKAN KE LUAR text-content -->
    <div class="color-dots" style="width: 300px; min-width: 300px; position: relative; height: 400px; z-index: 1;">
      <div class="dot dot-1"></div>
      <div class="dot dot-2"></div>
      <div class="dot dot-3" style="z-index: 0;"></div>
    </div>
  </div>
</section>


<!-- Info Section -->
<section class="py-5 bg-white text-center">
  <div class="container">
    <h3 class="fw-bold mb-4" style="font-family: 'Manrope'; font-weight: 700;">Cari tahu Kerja<span style="background-color: #309FFF; color: white; padding: 1px 8px; border-radius: 0.5rem;">In</span>!</h3>
    <p class="mb-5" style="font-family: 'Inter';">“Gak ada kerjaan yang terlalu kecil. Di KerjaIn, setiap tugas adalah peluang.”<br>Kerja serabutan? Jangan diremehkan. Dari yang kecil, bisa jadi besar.</p>

    <div class="row row-cols-1 row-cols-md-4 g-4">
      @foreach (['Siapa yang bisa kerja?', 'Kenapa harus KerjaIn?', 'Apa itu KerjaIn?', 'Butuh pengalaman?'] as $index => $title)
        <div class="col">
          <div class="card h-100 shadow-sm border-0">
            <div class="card-body">
              <img src="https://cdn-icons-png.flaticon.com/512/2026/2026590.png" class="mb-3" alt="icon" width="60">
              <h5 class="card-title fw-bold">{{ $title }}</h5>
              <p class="card-text">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pelajari lebih lanjut.</p>
              <a href="#" class="btn btn-link text-primary">Learn more</a>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</section>
@endsection


