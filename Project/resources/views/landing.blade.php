@extends('Master.master-job_req')

@section('content')

<!-- Banner Section -->
<section class="banner-section">
  <div class="container-fluid p-0">
    <div class="d-flex flex-wrap flex-lg-nowrap position-relative align-items-center px-3" style="min-height: 500px;">
      
      <!-- Konten Teks -->
      <div class="text-content custom-translate mb-5" style: "margin-left: 20px">
        <h1 class="display-5 fw-bold mb-3" style="font-family: 'Manrope'">Mau segala sesuatu selesai?</h1>
        <h1 class="display-5 fw-bold mb-3" style="margin-left: 4px">Yuk, <span style="color: #D3FA0D">Kerja</span><span style="background-color: #309FFF; color: white; padding: 1px 8px; border-radius: 0.5rem;">In</span>!</h1>
        <p class="mt-4 fs-6" style="font-family: 'Inter'; max-width: 800px; padding: 0px 30px; margin-left: -30px">Kerjain adalah solusi digital untuk kebutuhan kerja serabutan. Platform ini menghubungkan pemberi kerja dengan pekerja harian secara cepat, mudah, dan efisien. Temukan bantuan yang kamu butuhkan, atau mulai dapat penghasilan hari ini.</p>
        <div class="mt-4">
          <div class="garis-tengah"></div>
          <button type="button" class="btn-pekerja" style="font-size: 18px" data-bs-toggle="modal" data-bs-target="#logoutModal">Daftar Sekarang</button>
        </div>
      </div>

      <!-- Bulat-bulat Warna-warni -->
      <div class="color-dots" style="width: 300px; min-width: 300px; position: relative; height: 400px; z-index: 1;">
        <div class="dot dot-1"></div>
        <div class="dot dot-2"></div>
        <div class="dot dot-3"></div>
      </div>

    </div>
  </div>
</section>



<!-- Info Section -->
<section class="py-5 bg-white text-center">
  <div class="container">
    <h3 class="fw-bold mb-4" style="font-family: 'Manrope'; font-weight: 700;">Cari tahu Kerja<span style="background-color: #309FFF; color: white; padding: 1px 8px; border-radius: 0.5rem;">In</span>!</h3>
    <p class="mb-5" style="font-family: 'Inter';">“Gak ada kerjaan yang terlalu kecil. Di KerjaIn, setiap tugas adalah peluang.”<br>Kerja serabutan? Jangan diremehkan. Dari yang kecil, bisa jadi besar.</p>

    <div class="row row-cols-1 row-cols-md-4 g-4">
      <!-- Card 1 -->
      <div class="col">
        <div class="card h-100 shadow-sm border-0 hover-effect">
          <div class="card-body position-relative">
            <div class="card-number">1</div>
            <img src="https://cdn-icons-png.flaticon.com/512/2026/2026590.png" class="mb-3" alt="icon" width="60">
            <h5 class="card-title fw-bold">Siapa yang bisa pakai?</h5>
            <p class="card-text">Pencari kerja: Orang-orang yang ingin mendapatkan pengalaman tambahan atau pekerjaan harian dengan cara mudah dan fleksibel.<br><br>Pemberi kerja: Siapa saja yang membutuhkan bantuan untuk pekerjaan serabutan.</p>
            <a href="#" class="btn btn-link text-primary">Learn more</a>
          </div>
        </div>
      </div>
      
      <!-- Card 2 -->
      <div class="col">
        <div class="card h-100 shadow-sm border-0 hover-effect">
          <div class="card-body position-relative">
            <div class="card-number">2</div>
            <img src="https://cdn-icons-png.flaticon.com/512/3446/3446435.png" class="mb-3" alt="icon" width="60">
            <h5 class="card-title fw-bold">Kenapa harus KerjaIn?</h5>
            <p class="card-text">Platform mudah digunakan dengan berbagai pilihan pekerjaan dan tenaga kerja yang tersedia.</p>
            <a href="#" class="btn btn-link text-primary">Learn more</a>
          </div>
        </div>
      </div>
      
      <!-- Card 3 -->
      <div class="col">
        <div class="card h-100 shadow-sm border-0 hover-effect">
          <div class="card-body position-relative">
            <div class="card-number">3</div>
            <img src="https://cdn-icons-png.flaticon.com/512/1570/1570887.png" class="mb-3" alt="icon" width="60">
            <h5 class="card-title fw-bold">Apa itu KerjaIn?</h5>
            <p class="card-text">KerjaIn adalah platform digital yang menghubungkan orang-orang yang membutuhkan bantuan kerja serabutan dengan mereka yang siap memberikan jasa.</p>
            <a href="#" class="btn btn-link text-primary">Learn more</a>
          </div>
        </div>
      </div>
      
      <!-- Card 4 -->
      <div class="col">
        <div class="card h-100 shadow-sm border-0 hover-effect">
          <div class="card-body position-relative">
            <div class="card-number">4</div>
            <img src="https://cdn-icons-png.flaticon.com/512/3281/3281289.png" class="mb-3" alt="icon" width="60">
            <h5 class="card-title fw-bold">Butuh pengalaman?</h5>
            <p class="card-text">Tidak masalah! Banyak pekerjaan di KerjaIn yang bisa dilakukan tanpa pengalaman khusus.</p>
            <a href="#" class="btn btn-link text-primary">Learn more</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Features Section -->
<section class="py-5 bg-light">
  <div class="container">
    <h2 class="text-center mb-5 fw-bold" style="font-family: 'Manrope'; color: black;">Top Fitur untuk Anda</h2>
    
    <div class="row g-4">
      <!-- Feature 1 - Riwayat -->
      <div class="col-md-3">
        <div class="feature-card h-100 p-4 bg-white rounded-3 shadow-sm">
          <div class="feature-icon mb-3">
            <img src="https://cdn-icons-png.flaticon.com/512/3132/3132693.png" alt="Riwayat" width="40">
          </div>
          <h5 class="fw-bold mb-3">Riwayat</h5>
          <h6 class="text-muted mb-3">Alur Kerja</h6>
          <p class="mb-0">"Jangan tunggu kesempatan datang. Buka aplikasi, dan ciptakan peluangmu sendiri."</p>
        </div>
      </div>
      
      <!-- Feature 2 - Cari Kerja -->
      <div class="col-md-3">
        <div class="feature-card h-100 p-4 bg-white rounded-3 shadow-sm">
          <div class="feature-icon mb-3">
            <img src="https://cdn-icons-png.flaticon.com/512/3081/3081985.png" alt="Cari Kerja" width="40">
          </div>
          <h5 class="fw-bold mb-3">Cari Kerja</h5>
          <h6 class="text-muted mb-3">Daftar akun</h6>
          <p class="mb-0">Pilih peranmu sebagai Pemberi Kerja atau Pekerja Lepas (Serabutan).</p>
        </div>
      </div>
      
      <!-- Feature 3 - Pesan -->
      <div class="col-md-3">
        <div class="feature-card h-100 p-4 bg-white rounded-3 shadow-sm">
          <div class="feature-icon mb-3">
            <img src="https://cdn-icons-png.flaticon.com/512/542/542638.png" alt="Pesan" width="40">
          </div>
          <h5 class="fw-bold mb-3">Pesan</h5>
          <h6 class="text-muted mb-3">Daftar akun</h6>
          <p class="mb-0">Pilih peranmu sebagai Pemberi Kerja atau Pekerja Lepas (Serabutan).</p>
        </div>
      </div>
      
      <!-- Feature 4 - Lokasi -->
      <div class="col-md-3">
        <div class="feature-card h-100 p-4 bg-white rounded-3 shadow-sm">
          <div class="feature-icon mb-3">
            <img src="https://cdn-icons-png.flaticon.com/512/2838/2838694.png" alt="Lokasi" width="40">
          </div>
          <h5 class="fw-bold mb-3">Lokasi</h5>
          <h6 class="text-muted mb-3">Daftar akun</h6>
          <p class="mb-0">Pilih peranmu sebagai Pemberi Kerja atau Pekerja Lepas (Serabutan).</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Workflow Section -->
<section class="py-5 bg-white">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-8 text-center">
        <h1 class="fw-bold mb-5" style="font-family: 'Manrope';">Alur Kerja</h1>
        <p class="lead mb-5" style="font-size: 1.25rem; font-style: italic;">"Jangan tunggu kesempatan datang, Buka aplikasi, dan ciptakan peluangmu sendiri."</p>
        
        <div class="d-flex flex-column flex-md-row justify-content-center gap-4 mb-5">
          <!-- Step 1 -->
          <div class="workflow-step p-4 rounded-3" style="background-color: #f8f9fa; max-width: 500px;">
            <h3 class="h4 fw-bold mb-3">Daftar akun</h3>
            <p class="mb-0">Pilih peranmu sebagai Pemberi Kerja atau Pekerja Lepas (Serabutan).</p>
          </div>
          
          <!-- Step 2 -->
          <div class="workflow-step p-4 rounded-3" style="background-color: #f8f9fa; max-width: 500px;">
            <h3 class="h4 fw-bold mb-3">Daftar akun</h3>
            <p class="mb-0">Pilih peranmu sebagai Pemberi Kerja atau Pekerja Lepas (Serabutan).</p>
          </div>
          
          <!-- Step 3 -->
          <div class="workflow-step p-4 rounded-3" style="background-color: #f8f9fa; max-width: 500px;">
            <h3 class="h4 fw-bold mb-3">Daftar akun</h3>
            <p class="mb-0">Pilih peranmu sebagai Pemberi Kerja atau Pekerja Lepas (Serabutan).</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Testimonials Section -->
<section class="py-5" style="background-color: #f8f9fa;">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-8 text-center">
        <h2 class="h3 fw-bold mb-5" style="font-family: 'Manrope';">Kepuasan Pelanggan adalah yang utama bagi kami</h2>
        
        <div class="row g-4">
          <!-- Testimonial 1 -->
          <div class="col-md-4">
            <div class="testimonial-card p-4 h-100 bg-white rounded-3">
              <p class="mb-4" style="font-style: italic;">"Terima kasih telah menghadirkan kerjain yang sangat membantu kehidupan saya sehari hari, tampilan sangat mudah dimengerti, pelayanan bagus sekali"</p>
              <div class="testimonial-author">
                <h5 class="fw-bold mb-1">Iwan Jelek</h5>
                <p class="text-muted mb-0">Pekerja</p>
              </div>
            </div>
          </div>
          
          <!-- Testimonial 2 -->
          <div class="col-md-4">
            <div class="testimonial-card p-4 h-100 bg-white rounded-3">
              <p class="mb-4" style="font-style: italic;">"Terima kasih telah menghadirkan kerjain yang sangat membantu kehidupan saya sehari hari, tampilan sangat mudah dimengerti, pelayanan bagus sekali"</p>
              <div class="testimonial-author">
                <h5 class="fw-bold mb-1">Iwan Jelek</h5>
                <p class="text-muted mb-0">Pekerja</p>
              </div>
            </div>
          </div>
          
          <!-- Testimonial 3 -->
          <div class="col-md-4">
            <div class="testimonial-card p-4 h-100 bg-white rounded-3">
              <p class="mb-4" style="font-style: italic;">"Terima kasih telah menghadirkan kerjain yang sangat membantu kehidupan saya sehari hari, tampilan sangat mudah dimengerti, pelayanan bagus sekali"</p>
              <div class="testimonial-author">
                <h5 class="fw-bold mb-1">Iwan Jelek</h5>
                <p class="text-muted mb-0">Pekerja</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>


@endsection