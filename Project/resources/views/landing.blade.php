@extends('master.master-job-req')

@section('content')
@if(session()->has('custom_error_alert'))
<div class="alert alert-danger" role="alert">
  {{ session()->get('custom_error_alert') }}
</div>
@endif
<!-- Banner Section -->
<section class="banner-section">
  <div class="container-fluid p-0">
    <div class="d-flex flex-wrap flex-lg-nowrap position-relative align-items-center px-3" style="min-height: 500px;">

      <!-- Konten Teks -->
      <div class="text-content custom-translate mb-5" style= "margin-left: 20px">
        <h1 class="display-5 fw-bold mb-3" style="font-family: 'Manrope'">{{ __('landing.banner_title_part1') }}</h1>
        <h1 class="display-5 fw-bold mb-3" style="margin-left: 4px">{{ __('landing.banner_title_part2') }}  <span style="color: #D3FA0D">Kerja</span><span style="background-color: #309FFF; color: white; padding: 1px 8px; border-radius: 0.5rem;">In</span>!</h1>
        <p class="mt-4 fs-6" style="font-family: 'Inter'; max-width: 800px; padding: 0px 30px; margin-left: -30px">{{ __('landing.banner_description') }}</p>
        <div class="mt-4">
          <div class="garis-tengah"></div>
          <button type="button" class="btn-pekerja" style="font-size: 18px" data-bs-toggle="modal" data-bs-target="#logoutModal">{{ __('landing.banner_button_register') }}</button>
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
    <h3 class="fw-bold mb-4" style="font-family: 'Manrope'; font-weight: 700;">{{ __('landing.info_title') }} Kerja<span style="background-color: #309FFF; color: white; padding: 1px 8px; border-radius: 0.5rem;">In</span>!</h3>
    <p class="mb-5" style="font-family: 'Inter';">{{ __('landing.info_tagline_quote') }}<br>{{ __('landing.info_tagline_slogan') }}</p>

    <div class="row row-cols-1 row-cols-md-4 g-4">
      <!-- Card 1 -->
      <div class="col">
        <div class="card h-100 shadow-sm border-0 hover-effect" style="border-radius: 30px">
          <div class="card-body position-relative" style="background-color: #D3FA0D; color: black ;">
            <img src="{{ asset('assets/img/lampu.jpg') }}" class="mb-3" alt="icon" width="200" style="border-radius: 100px; margin-left: 90px; margin-top: -55px; margin-right: -55px">
            <h5 class="card-title fw-bold" style="margin-left: -50px">{{ __('landing.card1_title') }}</h5>
            <p class="card-text">{{ __('landing.card1_text') }}</p>
          </div>
        </div>
      </div>

      <!-- Card 2 -->
      <div class="col">
        <div class="card h-100 shadow-sm border-0 hover-effect" style="border-radius: 30px">
          <div class="card-body position-relative" style="background-color: #309FFF; color: white ;">
            <img src="{{ asset('assets/img/lampu.jpg') }}" class="mb-3" alt="icon" width="200" style="border-radius: 100px; margin-left: 90px; margin-top: -55px; margin-right: -55px">
            <h5 class="card-title fw-bold" style="margin-left: -50px">{{ __('landing.card2_title') }}</h5>
            <p class="card-text">{{ __('landing.card2_text') }}</p>
          </div>
        </div>
      </div>

      <!-- Card 3 -->
      <div class="col">
        <div class="card h-100 shadow-sm border-0 hover-effect" style="border-radius: 30px">
          <div class="card-body position-relative" style="background-color: #D3FA0D; color: black ;">
            <img src="{{ asset('assets/img/lampu.jpg') }}" class="mb-3" alt="icon" width="200" style="border-radius: 100px; margin-left: 90px; margin-top: -55px; margin-right: -55px">
            <h5 class="card-title fw-bold" style="margin-left: -105px">{{ __('landing.card3_title') }}</h5>
            <p class="card-text">{{ __('landing.card3_text') }}</p>
          </div>
        </div>
      </div>

      <!-- Card 4 -->
      <div class="col">
        <div class="card h-100 shadow-sm border-0 hover-effect" style="border-radius: 30px">
          <div class="card-body position-relative" style="background-color: #309FFF; color: white ;">
            <img src="{{ asset('assets/img/lampu.jpg') }}" class="mb-3" alt="icon" width="200" style="border-radius: 100px; margin-left: 90px; margin-top: -55px; margin-right: -55px">
            <h5 class="card-title fw-bold" style="margin-left: -70px">{{ __('landing.card4_title') }}</h5>
            <p class="card-text">{{ __('landing.card4_text') }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Features Section -->
<section class="py-5">
  <div class="container">
    <h2 class="text-center mb-5 fw-bold" style="font-family: 'Manrope';">{{ __('landing.features_title') }}</h2>

      <div class="row g-4">
        <!-- Feature 1 - Riwayat -->
        <div class="col-md-3">
          <div class="feature-card h-100 text-center">
            <div class="icon-circle mx-auto mb-3" 
              style=" background-color: #294287;
              border-radius: 50%;
              width: 80px;
              height: 80px;
              display: flex;
              align-items: center;
              justify-content: center;">
              <img src="{{ asset('assets/img/riwayat.svg') }}" alt="Riwayat" width="40">
            </div>
            <h5 class="fw-bold mb-3">{{ __('landing.feature_history') }}</h5>
          </div>
        </div>


      <!-- Feature 1 - Cari Kerja -->
        <div class="col-md-3">
          <div class="feature-card h-100 text-center">
            <div class="icon-circle mx-auto mb-3" 
              style=" background-color: #294287;
              border-radius: 50%;
              width: 80px;
              height: 80px;
              display: flex;
              align-items: center;
              justify-content: center;">
              <img src="{{ asset('assets/img/cari_kerja.svg') }}" alt="Riwayat" width="40">
            </div>
            <h5 class="fw-bold mb-3">{{ __('landing.feature_find_job') }}</h5>
          </div>
        </div>

      <!-- Feature 1 - Pesan -->
        <div class="col-md-3">
          <div class="feature-card h-100 text-center">
            <div class="icon-circle mx-auto mb-3" 
              style=" background-color: #294287;
              border-radius: 50%;
              width: 80px;
              height: 80px;
              display: flex;
              align-items: center;
              justify-content: center;">
              <img src="{{ asset('assets/img/pesan.svg') }}" alt="Riwayat" width="40">
            </div>
            <h5 class="fw-bold mb-3">{{ __('landing.feature_messages') }}</h5>
          </div>
        </div>

      <!-- Feature 1 - Lokasi -->
        <div class="col-md-3">
          <div class="feature-card h-100 text-center">
            <div class="icon-circle mx-auto mb-3" 
              style=" background-color: #294287;
              border-radius: 50%;
              width: 80px;
              height: 80px;
              display: flex;
              align-items: center;
              justify-content: center;">
              <img src="{{ asset('assets/img/lokasi.svg') }}" alt="Riwayat" width="30">
            </div>
            <h5 class="fw-bold mb-3">{{ __('landing.feature_location') }}</h5>
          </div>
        </div>
  </div>
</section>

<!-- Workflow Section -->
<section class="py-5 bg-white">
  <div class="container">
    <div class="row align-items-center">
      <!-- Left side: Title and Description -->
      <div class="col-md-4 mb-4 mb-md-0">
        <div class="d-flex align-items-start gap-2">
          <div>
            <h2 class="fw-bold" style="font-family: 'Manrope';">{{ __('landing.workflow_title') }}</h2>
            <p class="text-muted" style="font-style: italic; font-size: 14px;">
              {{ __('landing.workflow_quote') }}
            </p>
          </div>
        </div>
      </div>

      <!-- Right side: Steps -->
      <div class="col-md-8">
        <div class="row text-center g-4">
          <!-- Step 1 -->
          <div class="col-md-4">
            <img src="{{ asset('assets/img/orang_bingung.jpg') }}" alt="Step 1" width="70" class="mb-2">
            <h6 class="fw-bold">{{ __('landing.workflow_step1_title') }}</h6>
            <p class="text-muted" style="font-size: 14px;">{{ __('landing.workflow_step1_text') }}</p>
          </div>

          <!-- Step 2 -->
          <div class="col-md-4">
            <img src="{{ asset('assets/img/tas_kerja.jpg') }}" alt="Step 2" width="64" class="mb-2">
            <h6 class="fw-bold">{{ __('landing.workflow_step2_title') }}</h6>
            <p class="text-muted" style="font-size: 14px;">{{ __('landing.workflow_step2_text') }}</p>
          </div>

          <!-- Step 3 -->
          <div class="col-md-4">
            <img src="{{ asset('assets/img/rating.jpg') }}" alt="Step 3" width="70" class="mb-2">
            <h6 class="fw-bold">{{ __('landing.workflow_step3_title') }}</h6>
            <p class="text-muted" style="font-size: 14px;">{{ __('landing.workflow_step3_text') }}<</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

      
      <!-- Testimonials Section -->
      <section class="py-5 bg-white">
        <div class="container">
          <div class="row align-items-start">
            <!-- Title -->
            <div class="col-md-5 mb-4">
              <h2 class="fw-bold" style="font-family: 'Manrope'; font-size: 28px;">
                {{ __('landing.testimonials_title') }}
              </h2>
            </div>
          </div>
       

      <!-- Testimonials -->
      <div class="col">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <!-- Testimonials List -->
          <div class="d-flex gap-3 flex-wrap justify-content-center">
            <!-- Testimonial Card -->
            @for ($i = 0; $i < 3; $i++)
            <div class="bg-white border rounded-3 p-3" style="width: 420px;">
              <div class="mb-2">
                <img src="{{ asset('assets/img/petikan.jpg') }}" alt="Quote" width="20">
              </div>
              <p class="mb-3" style="font-size: 14px;">
                {{ __('landing.testimonial_quote') }}
              </p>
              <div class="d-flex align-items-center gap-2">
                <img src="{{ asset('assets/img/profile_org.jpg') }}" alt="User" width="40" style="border-radius: 50%;">
                <div>
                  <strong>Iwan Jelek</strong><br>
                  <small class="text-muted">{{ __('landing.testimonial_user_role') }}</small><br>
                  <span style="color: gold;">★★★★★</span>
                </div>
              </div>
            </div>
            @endfor
          </div>

        </div>
      </div>
    </div>
</section>



@endsection