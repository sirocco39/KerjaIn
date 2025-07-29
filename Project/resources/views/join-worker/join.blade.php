@extends('master.master-job-req')
<!-- This file name is join.blade.php -->
@section('content')
<x-join-worker.join-template :step="1">
    <form class="row px-0 gx-2 gx-sm-4" action="{{ route('worker.register.store1') }}" method="post" id="personalDataForm">
        <h5 class="col-12 fw-bold my-3">{{ __('join-worker.personal_data') }}</h5>
        @csrf
        <label class="form-label manrope fs-6 fw-bold" for="nama_depan">{{ __('join-worker.full_name') }}</label>
        <div class="col-6">
            <input type="text" name="first_name" id="nama_depan" placeholder="{{ __('join-worker.first_name_placeholder') }}"
                value="{{ old('first_name', $data['first_name'] ?? '') }}"
                class="form-control inter bg-white border border-dark mb-3">
            <p id="error-first_name" class="text-danger @unless ($errors->has('first_name')) hidden @endunless">
                {{ $errors->first('first_name') }}
            </p>
        </div>
        <div class="col-6">
            <input type="text" name="last_name" id="nama_belakang" placeholder="{{ __('join-worker.last_name_placeholder') }}"
                value="{{ old('last_name', $data['last_name'] ?? '') }}"
                class="form-control inter bg-white border border-dark mb-3">
            <p id="error-last_name" class="text-danger @unless ($errors->has('last_name')) hidden @endunless">
                {{ $errors->first('last_name') }}
        </div>
        <div class="col-12 col-md-6">
            <label class="form-label manrope fs-6 fw-bold" for="tanggal_lahir">{{ __('join-worker.birthdate') }}</label>
            <input value="{{ old('birthdate', $data['birthdate'] ?? '') }}"
                class="form-control inter bg-white border border-dark mb-3" placeholder="{{ __('join-worker.birthdate_placeholder') }}" name="birthdate"
                id="tanggal_lahir" type="date">
            <p id="error-birthdate" class="text-danger @unless ($errors->has('birthdate')) hidden @endunless">
                {{ $errors->first('birthdate') }}
            </p>
        </div>
        <div class="col-12 col-md-6">
            <label class="form-label manrope fs-6 fw-bold" for="jenis_kelamin">{{ __('join-worker.gender') }}</label>
            <select class="form-select inter bg-white border border-dark mb-3" id="jenis_kelamin" name="gender">
                <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>{{ __('join-worker.male') }}</option>
                <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>{{ __('join-worker.female') }}</option>
            </select>
            <p id="error-gender" class="text-danger @unless ($errors->has('gender')) hidden @endunless">
                {{ $errors->first('gender') }}
            </p>
        </div>
        <div class="col-12 col-md-6">
            <label class="form-label manrope fs-6 fw-bold" for="nomor_telepon">{{ __('join-worker.phone_number') }}</label>
            <input value="{{ old('phone_number', $data['phone_number'] ?? '') }}"
                class="form-control inter bg-white border border-dark mb-3" placeholder="{{ __('join-worker.phone_number_placeholder') }}"
                id="nomor_telepon" name="phone_number">
            <p id="error-phone_number" class="text-danger @unless ($errors->has('phone_number')) hidden @endunless">
                {{ $errors->first('phone_number') }}
            </p>
        </div>
        <div class="col-12 col-md-6">
            <label class="form-label manrope fs-6 fw-bold " for="nomor_ktp">{{ __('join-worker.nik_number') }}</label>
            <input type="text" name="nik" id="nomor_ktp" placeholder="{{ __('join-worker.nik_placeholder') }}"
                value="{{ old('nik', $data['nik'] ?? '') }}"
                class="form-control inter bg-white border border-dark mb-3">
            <p id="error-nik" class="text-danger @unless ($errors->has('nik')) hidden @endunless">
                {{ $errors->first('nik') }}
            </p>
        </div>
        <div class="col-12">
            <label class="form-label manrope fs-6 fw-bold" for="alamat">{{ __('join-worker.domicile_address') }}</label>
            <input value="{{ old('address', $data['address'] ?? '') }}"
                class="form-control inter bg-white border border-dark mb-3" placeholder="{{ __('join-worker.address_placeholder') }}" id="alamat"
                name="address">
            <p id="error-address" class="text-danger @unless ($errors->has('address')) hidden @endunless">
                {{ $errors->first('address') }}
            </p>
        </div>
        <div class="text-center mt-4">
            <button class="btn bg-secondary text-white px-4">{{ __('join-worker.next_button') }}</button>
        </div>
    </form>
</x-join-worker.join-template>
@endsection