@extends('Master.master-job_req')

@section('content')
    <div class="container-fluid pembatas-x pembatas-y">
        <div class="row align-items-center">
            <!-- Kiri: Form -->
            <div class="col-lg-6">
                <h2 class="fw-bold mb-2">Buat tawaran kerja</h2>
                <p class="mb-4">Yuk, mulai! Isi detail pekerjaan agar mitra kami bisa segera membantumu.</p>

                <form action="{{route('requesttt.store')}}" method="post">
                    @csrf
                    <!-- Judul Pekerjaan -->
                    <div class="mb-3">
                        <label for="work-title-label" class="form-label fw-semibold">Judul Pekerjaan</label>
                        <input type="text" class="form-control rounded-3" id="work-title-text" name="workTitleLabel" placeholder="Contoh: Masangin AC Ruang Tamu" value="{{ old('workTitleLabel') }}">
                        <span style="color:red">@error('workTitleLabel'){{ $message }}
                            @enderror</span>
                    </div>
                    <!-- Detail Pekerjaan -->
                    <div class="mb-3">
                        <label for="work-detail-label" class="form-label fw-semibold">Detail Pekerjaan</label>
                        <textarea type="text" class="form-control rounded-3" id="detail" rows="3" id="work-detail-text" name="workDetailLabel" placeholder="Contoh: Pasang AC 1 PK di ruang tamu bagian atas korden" value="{{ old('workDetailLabel') }}"></textarea>
                        <span style="color:red">@error('workDetailLabel'){{ $message }}
                            @enderror</span>
                    </div>
                    <!-- Alamat -->
                    <div class="mb-3">
                        <label for="work-address-label" class="form-label fw-semibold">Alamat</label>
                        <input type="text" class="form-control rounded-3" id="work-address-text" name="workAddressLabel" placeholder="Contoh: Jalan Pakuan No3, Sentul" value="{{ old('workAddressLabel') }}">
                        <span style="color:red">@error('workAddressLabel'){{ $message }}
                            @enderror</span>
                    </div>
                    <!-- Waktu mulai pengerjaan -->
                    <div class="mb-3">
                        <label for="work-start-time-label" class="form-label fw-semibold">Waktu mulai pekerjaan</label>
                        <div class="d-flex">
                            <input type="date" class="form-control rounded-3" id="work-start-date-text" name="workStartDateLabel" value="{{ old('workStartDateLabel') }}">
                            <span class="align-self-center">–</span>
                            <input type="time" class="form-control rounded-3" id="work-start-time-text1" name="workStartTimeLabel" value="{{ old('workStartTimeLabel') }}">
                            <span style="color:red">@error('workStartDateLabel'){{ $message }}
                                @enderror</span>
                            <span style="color:red">@error('workStartTimeLabel'){{ $message }}
                                @enderror</span>
                        </div>
                    </div>
                    <!-- Waktu selesai pengerjaan -->
                    <div class="mb-3">
                        <label for="work-end-time-label" class="form-label fw-semibold">Waktu selesai pekerjaan</label>
                        <div class="d-flex">
                            <input type="date" class="form-control rounded-3" id="work-end-date-text" name="workEndDateLabel" value="{{ old('workEndDateLabel') }}">
                            <span class="align-self-center">–</span>
                            <input type="time" class="form-control rounded-3" id="work-end-time-text1" name="workEndTimeLabel" value="{{ old('workEndTimeLabel') }}">
                            <span style="color:red">@error('workEndDateLabel'){{ $message }}
                                @enderror</span>
                            <span style="color:red">@error('workEndTimeLabel'){{ $message }}
                                @enderror</span>
                            @error('datetime')<span style="color:red">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <!-- Biaya Jasa -->
                    <div class="mb-4">
                        <label for="work-price-label" class="form-label fw-semibold">Harga</label>
                        <div class="input-group">
                            <span class="input-group-text rounded-start-3">Rp</span>
                            <input type="number" class="form-control rounded-end-3" min="5000" id="work-price-text" name="workPriceLabel" placeholder="Contoh: 150.000" value="{{ old('workPriceLabel') }}">
                            <span style="color:red">@error('workPriceLabel'){{ $message }}
                                @enderror</span>
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
@endsection
