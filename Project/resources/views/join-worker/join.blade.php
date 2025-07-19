@extends('master.master-job_req')

@section('content')
    <x-join-worker.join-template :step="1">
        <form class="row px-0 gx-2 gx-sm-4" action="{{ route('worker.register.store1') }}" method="post" id="personalDataForm">
            <h5 class="col-12 fw-bold my-3">Data Pribadi</h5>
            @csrf
            <label class="form-label manrope fs-6 fw-bold" for="nama_depan">Nama Lengkap</label>
            <div class="col-6">
                <input type="text" name="first_name" id="nama_depan" placeholder="Nama depan"
                    value="{{ old('first_name', $data['first_name'] ?? '') }}"
                    class="form-control inter bg-white border border-dark mb-3" placeholder="Nama depan">
                <p id="error-first_name" class="text-danger @unless ($errors->has('first_name')) hidden @endunless">
                    {{ $errors->first('first_name') }}
                </p>
            </div>
            <div class="col-6">
                <input type="text" name="last_name" id="nama_belakang" placeholder="Nama belakang"
                    value="{{ old('last_name', $data['last_name'] ?? '') }}"
                    class="form-control inter bg-white border border-dark mb-3" placeholder="Nama belakang"
                    id="nama_belakang" name="last_name">
                <p id="error-last_name" class="text-danger @unless ($errors->has('last_name')) hidden @endunless">
                    {{ $errors->first('last_name') }}
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label manrope fs-6 fw-bold" for="tanggal_lahir">Tanggal Lahir</label>
                <input value="{{ old('birthdate', $data['birthdate'] ?? '') }}"
                    class="form-control inter bg-white border border-dark mb-3" placeholder="Tanggal lahir" name="birthdate"
                    id="tanggal_lahir" type="date">
                <p id="error-birthdate" class="text-danger @unless ($errors->has('birthdate')) hidden @endunless">
                    {{ $errors->first('birthdate') }}
                </p>
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label manrope fs-6 fw-bold" for="jenis_kelamin">Jenis Kelamin</label>
                <select class="form-select inter bg-white border border-dark mb-3" id="jenis_kelamin" name="gender">
                    <option value="" {{ old('gender') == '' ? 'selected' : '' }}>Jenis kelamin</option>
                    <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Perempuan</option>
                </select>
                <p id="error-gender" class="text-danger @unless ($errors->has('gender')) hidden @endunless">
                    {{ $errors->first('gender') }}
                </p>
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label manrope fs-6 fw-bold" for="nomor_telepon">Nomor Telepon</label>
                <input value ="{{ old('phone_number', $data['phone_number'] ?? '') }}"
                    class="form-control inter bg-white border border-dark mb-3" placeholder="Nomor telepon"
                    id="nomor_telepon" name="phone_number">
                <p id="error-phone_number" class="text-danger @unless ($errors->has('phone_number')) hidden @endunless">
                    {{ $errors->first('phone_number') }}
                </p>
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label manrope fs-6 fw-bold " for="nomor_ktp">Nomor KTP</label>
                <input type="text" name="nik" id="nomor_ktp" placeholder="Nomor KTP/NIK"
                    value="{{ old('nik', $data['nik'] ?? '') }}"
                    class="form-control inter bg-white border border-dark mb-3" placeholder="Nomor KTP/NIK">
                <p id="error-nik" class="text-danger @unless ($errors->has('nik')) hidden @endunless">
                    {{ $errors->first('nik') }}
                </p>
            </div>
            <div class="col-12">
                <label class="form-label manrope fs-6 fw-bold" for="alamat">Alamat Domisili</label>
                <input value="{{ old('address', $data['address'] ?? '') }}"
                    class="form-control inter bg-white border border-dark mb-3" placeholder="Alamat domisili" id="alamat"
                    name="address">
                <p id="error-address" class="text-danger @unless ($errors->has('address')) hidden @endunless">
                    {{ $errors->first('address') }}
                </p>
            </div>
            <div class="text-center mt-4">
                <button class="btn bg-secondary text-white px-4">Lanjut</button>
            </div>
        </form>
    </x-join-worker.join-template>
@endsection
