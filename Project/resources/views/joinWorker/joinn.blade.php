@extends('master')

@section('content')
<section class="bg-header text-white text-center py-4">
  <div class="container">
    <h4>Yuk, jadi bagian dari KerjaIn – tempat di mana kerja bareng, bantu bareng, cuan bareng.</h4>
  </div>
</section>

<!-- Form Join -->
<section class="py-5 px-3">
    <div class="bg-white shadow rounded p-4 p-md-5">
      <!-- Stepper -->
        <div class="d-flex justify-content-center gap-4 mb-4 text-center">
          <div><strong class="text-primary">1</strong> Data Pribadi</div>
          <div><strong>2</strong> Detail Kontrak</div>
          <div><strong>3</strong> Verifikasi</div>
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

</section>
@endsection
