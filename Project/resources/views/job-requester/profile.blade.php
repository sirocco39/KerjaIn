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
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-3">

            <!-- Profile Image -->
            <div class="text-center mb-3">
                <div style="width: 300px; height: 300px; margin: 0 auto; background-color: #f8f9fa; border: 1px dashed #ccc; display: flex; align-items: center; justify-content: center;">
                <img class="img-fluid"
                    src="../../dist/img/user4-128x128.jpg"
                    alt="User profile picture" style="max-width: 100%; max-height: 100%; object-fit: cover;">
                </div style="position: absolute; bottom: 10px; right: 10px;">
            </div>

            <!-- About Me Box -->
            <div class="card card-primary" style="width: 300px; margin-left: 10px;">
              <div class="card-header">
                <h3 class="card-title">About Me</h3>
              </div>
                <!-- /.card-header -->
                <div class="card-body">
                        <li style="list-style: none;">
                        {{-- Ganti href="#" dengan link ke halaman saldo jika ada --}}
                        <a class="dropdown-item d-flex align-items-center"
                            href="{{ route('balance.job-req') }}">
                            {{-- Sisi Kiri: Ikon dan Teks --}}
                            <div class="d-flex align-items-center gap-2">
                                {{-- Pastikan Anda punya ikon untuk saldo, contoh: icon-wallet.svg --}}
                                <img src="{{ asset('Image/Icon/icon-wallet.svg') }}" alt="Icon Saldo"
                                    class="navIcon">
                                <span>Saldo</span>
                            </div>
                            {{-- Sisi Kanan: Jumlah Saldo --}}
                            <span class="ms-auto fw-bold">
                                {{-- Asumsi saldo tersimpan di kolom 'saldo' pada tabel user --}}
                                {{-- Fungsi number_format untuk format Rupiah --}}
                                Rp{{ number_format(auth()->user()->balance, 0, ',', '.') }}
                            </span>
                        </a>
                    </li>

                    <hr>

                        <li style="list-style: none;">
                        {{-- Ganti href="#" dengan link ke halaman saldo jika ada --}}
                        <a class="dropdown-item d-flex align-items-center"
                            href="{{ route('balance.job-req') }}">
                            {{-- Sisi Kiri: Ikon dan Teks --}}
                            <div class="d-flex align-items-center gap-2">
                                {{-- Pastikan Anda punya ikon untuk saldo, contoh: icon-wallet.svg --}}
                                <img src="{{ asset('Image/Icon/icon-wallet.svg') }}" alt="Icon Saldo"
                                    class="navIcon">
                                <span>Saldo Tertahan</span>
                            </div>
                            {{-- Sisi Kanan: Jumlah Saldo --}}
                            <span class="ms-auto fw-bold">
                                {{-- Asumsi saldo tersimpan di kolom 'saldo' pada tabel user --}}
                                {{-- Fungsi number_format untuk format Rupiah --}}
                                Rp{{ number_format(auth()->user()->balance, 0, ',', '.') }}
                            </span>
                        </a>
                    </li>
                </div>
                <!-- /.card-body -->
                
                            
            </div>
          </div>
          
          <div class="col-md-9">
            <div class="card card-primary card-outline">
                <div class="tab-pane" id="settings" style="padding: 20px;">
                    <h3 class="profile-username">Nama</h3>

                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <b>Tempat/Tanggal Lahir</b> <a class="float-right">1,322</a>
                        </li>
                        <li class="list-group-item">
                            <b>Email</b> <a class="float-right">543</a>
                        </li>
                        <li class="list-group-item">
                            <b>No Handphone</b> <a class="float-right">13,287</a>
                        </li>
                    </ul>

                    <a href="#" class="btn btn-primary btn-block" id="btnEdit" style="margin-bottom: 10px"><b>Edit</b></a>

                    <!-- FORM EDIT -->
                    <div id="formEdit" style="display: none; margin-top: 20px;">
                        <form class="form-horizontal">
                            <div class="form-group row">
                                <label for="inputName" class="col-sm-2 col-form-label">Nama</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="inputName" placeholder="Name">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputTTL" class="col-sm-2 col-form-label">Tempat/Tanggal Lahir</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="inputTTL" placeholder="Tempat/Tanggal Lahir">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail" class="col-sm-2 col-form-label">Email</label>
                                <div class="col-sm-10">
                                    <input type="email" class="form-control" id="inputEmail" placeholder="Email">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputPhone" class="col-sm-2 col-form-label">No Handphone</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="inputPhone" placeholder="No Handphone">
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="offset-sm-2 col-sm-10">
                                    <button type="submit" class="btn btn-danger">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- END FORM EDIT -->
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

@endsection