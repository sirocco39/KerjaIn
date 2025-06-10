@extends('Master.master-job_req') 

@section('content')

<section class="py-5 bg-light">
  <div class="container">
    <div class="row align-items-center">
      
      <!-- Kiri: Form -->
      <div class="col-lg-6">
        <h2 class="fw-bold mb-2">Buat tawaran kerja</h2>
        <p class="mb-4">Yuk, mulai! Isi detail pekerjaan agar mitra kami bisa segera membantumu.</p>

        <form>
          <!-- Judul Pekerjaan -->
          <div class="mb-3">
            <label for="judul" class="form-label fw-semibold">Judul Pekerjaan</label>
            <input type="text" class="form-control rounded-3" id="judul" placeholder="Contoh: Masangin AC Ruang Tamu">
          </div>

          <!-- Detail Pekerjaan -->
          <div class="mb-3">
            <label for="detail" class="form-label fw-semibold">Detail Pekerjaan</label>
            <textarea class="form-control rounded-3" id="detail" rows="3" placeholder="Contoh: Pasang AC 1 PK di ruang tamu bagian atas korden"></textarea>
          </div>

          <!-- Alamat -->
          <div class="mb-3">
            <label for="alamat" class="form-label fw-semibold">Alamat</label>
            <input type="text" class="form-control rounded-3" id="alamat" placeholder="Contoh: Jalan Pakuan No 3, Sentul">
          </div>

          <!-- Waktu mulai pengerjaan -->
          <div class="mb-3">
            <label for="tanggal" class="form-label fw-semibold">Waktu mulai pekerjaan</label>
            <div class="d-flex gap-2">
              <input type="date" class="form-control rounded-3" id="tanggal">
              <span class="align-self-center">–</span>
              <input type="time" class="form-control rounded-3" />
            </div>            
          </div>

          <!-- Waktu selesai pengerjaan -->
          <div class="mb-3">
            <label class="form-label fw-semibold">Waktu selesai pengerjaan</label>
            <div class="d-flex gap-2">
              <input type="date" class="form-control rounded-3" id="tanggal">
              <span class="align-self-center">–</span>
              <input type="time" class="form-control rounded-3" />
            </div>
          </div>

          <!-- Biaya Jasa -->
          <div class="mb-4">
            <label for="biaya" class="form-label fw-semibold">Biaya Jasa</label>
            <div class="input-group">
              <span class="input-group-text rounded-start-3">Rp</span>
              <input type="text" class="form-control rounded-end-3" id="biaya" placeholder="Contoh: 150.000">
            </div>
          </div>

          <button type="submit" class="btn btn-primary px-4 py-2 rounded-3">Buat Tawaran Kerja</button>
        </form>
      </div>

      <!-- Kanan: Ilustrasi -->
      <div class="col-lg-6 text-center mt-5 mt-lg-0">
        <img src="{{ asset('Image/orang/Merah dan Pink Ilustrasi Mochi Logo (12) 1.png') }}" alt="Ilustrasi Orang" class="img-fluid" style="max-height: 400px;">
      </div>

    </div>
  </div>
</section>


@endsection


