@extends('master.master-job-req') {{-- Atau layout utama kamu --}}

@section('content')

  <!-- Content Wrapper. Contains page content -->
  <div class="content" style="margin-left: 100px; margin-right: 50px;">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Profile</h1>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>


    <!-- Main content -->
    <section class="content">
      <div class="container">
        <div class="row">
          <div class="col-12 col-md-4 mb-4">
            <form method="POST" action="{{ route('profile.update.photo') }}" enctype="multipart/form-data">
                @csrf
                <!-- Profile Image Upload -->
                <div class="text-center">
                    <input type="file" id="profileImageInput" name="photo" accept="image/*" style="display: none;" onchange="previewImage(event)">

                    <div onclick="document.getElementById('profileImageInput').click();" 
                        style="cursor: pointer; width: 300px; height: 300px; margin: 0 auto; background-color: #f8f9fa; border: 1px dashed #ccc; display: flex; align-items: center; justify-content: center;">
                        <img id="profileImagePreview" 
                            class="img-fluid"
                            src="{{ auth()->user()->photo_url_user ? asset(auth()->user()->photo_url_user) : asset('dist/img/user4-128x128.jpg') }}"
                            alt="User profile picture" 
                            style="max-width: 100%; max-height: 100%; object-fit: cover;">
                    </div>
                    <small class="text-muted d-block mt-2">Klik gambar untuk mengunggah foto baru</small>

                    <button type="submit" class="btn btn-primary mt-2">Simpan Foto</button>
                </div>
            </form>


           <!-- About Me Box -->
        <div class="card card-primary mt-4">
          <div class="card-header">
            <h3 class="card-title">About Me</h3>
          </div>
          <div class="card-body p-3">
            <ul class="list-unstyled">
              <li class="mb-3">
                <a class="dropdown-item d-flex align-items-center" href="{{ route('balance.job-req') }}">
                  <div class="d-flex align-items-center gap-2">
                    <img src="{{ asset('Image/Icon/icon-wallet.svg') }}" alt="Icon Saldo" class="navIcon">
                    <span>Saldo</span>
                  </div>
                  <span class="ms-auto fw-bold">Rp{{ number_format(auth()->user()->balance, 0, ',', '.') }}</span>
                </a>
              </li>

              <li>
                <a class="dropdown-item d-flex align-items-center" href="{{ route('balance.job-req') }}">
                  <div class="d-flex align-items-center gap-2">
                    <img src="{{ asset('Image/Icon/icon-wallet.svg') }}" alt="Icon Saldo" class="navIcon">
                    <span>Saldo Tertahan</span>
                  </div>
                  <span class="ms-auto fw-bold">Rp{{ number_format(auth()->user()->locked_balance, 0, ',', '.') }}</span>
                </a>
              </li>
            </ul>
          </div>
        </div>
      </div>
          
           <!-- Kolom Info User -->
      <div class="col-12 col-md-8">
        <div class="card card-primary card-outline">
          <div class="p-4">
            <h3 class="profile-username">{{ auth()->user()->first_name . ' ' . auth()->user()->last_name }}</h3>
            <ul class="list-group list-group-unbordered mb-3">
              <li class="list-group-item d-flex justify-content-between">
                <b>Tanggal Lahir</b>
                <span>{{ auth()->user()->birth_date }}</span>
              </li>
              <li class="list-group-item d-flex justify-content-between">
                <b>Email</b>
                <span>{{ auth()->user()->email }}</span>
              </li>
              <li class="list-group-item d-flex justify-content-between">
                <b>No Handphone</b>
                <span>{{ auth()->user()->phone_number }}</span>
              </li>
            </ul>

            <a href="#" class="btn btn-primary btn-block mb-3" id="btnEdit"><b>Edit</b></a>

            <!-- FORM EDIT -->
            <div id="formEdit" style="display: none;">
              <form class="form-horizontal" method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('PUT')

                <div class="form-group row mb-3">
                  <label class="col-sm-4 col-form-label">Nama Depan</label>
                  <div class="col-sm-8">
                    <input type="text" class="form-control" name="first_name" value="{{ old('first_name', auth()->user()->first_name) }}">
                  </div>
                </div>

                <div class="form-group row mb-3">
                  <label class="col-sm-4 col-form-label">Nama Belakang</label>
                  <div class="col-sm-8">
                    <input type="text" class="form-control" name="last_name" value="{{ old('last_name', auth()->user()->last_name) }}">
                  </div>
                </div>

                <div class="form-group row mb-3">
                  <label class="col-sm-4 col-form-label">Tanggal Lahir</label>
                  <div class="col-sm-8">
                    <input type="text" class="form-control" name="birth_date" value="{{ old('birth_date', optional(auth()->user()->birth_date)->format('Y-m-d')) }}">
                  </div>
                </div>

                <div class="form-group row mb-3">
                  <label class="col-sm-4 col-form-label">No Handphone</label>
                  <div class="col-sm-8">
                    <input type="text" class="form-control" name="phone_number" value="{{ auth()->user()->phone_number }}">
                  </div>
                </div>

                <div class="form-group row">
                  <div class="offset-sm-4 col-sm-8">
                    <button type="submit" class="btn btn-outline-danger">Submit</button>
                  </div>
                </div>
              </form>
            </div>
            <!-- END FORM EDIT -->
                </div>
            </div>
        </div>
    </div>
  </div>
    </section>
    <!-- /.content -->
</div>


<script>
  $(document).ready(function () {
    $('#btnEdit').on('click', function (e) {
      e.preventDefault();
      $('#formEdit').slideToggle(); // bisa juga pakai .show() kalau mau langsung
    });
  });
</script>

<!-- JS untuk preview -->
<script>
function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function () {
        const preview = document.getElementById('profileImagePreview');
        preview.src = reader.result;
    };
    reader.readAsDataURL(event.target.files[0]);
}
</script>

@endsection