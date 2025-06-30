@extends('Master.master-job_req')

@section('content')
Ini Dummy Job Requester Tawarkan Kerja
<form action="{{route('requesttt.store')}}" method="post">
    @csrf
    <div class="work-title">
        <label for="work-title-label">Judul Pekerjaan</label>
        <input type="text" id="work-title-text" name="workTitleLabel" placeholder="Contoh: Masangin AC Ruang Tamu" value="{{ old('workTitleLabel') }}">
        <span style="color:red">@error('workTitleLabel'){{ $message }}
            @enderror</span>
    </div>
    <div class="work-detail">
        <label for="work-detail-label">Detail Pekerjaan</label>
        <input type="text" id="work-detail-text" name="workDetailLabel" placeholder="Contoh: Pasang AC 1 PK di ruang tamu bagian atas korden" value="{{ old('workDetailLabel') }}">
        <span style="color:red">@error('workDetailLabel'){{ $message }}
            @enderror</span>
    </div>
    <div class="work-address">
        <label for="work-address-label">Alamat</label>
        <input type="text" id="work-address-text" name="workAddressLabel" placeholder="Contoh: Jalan Pakuan No3, Sentul" value="{{ old('workAddressLabel') }}">
        <span style="color:red">@error('workAddressLabel'){{ $message }}
            @enderror</span>
    </div>
    <div class="work-start-time">
        <label for="work-start-time-label">Waktu mulai pekerjaan</label>
        <input type="date" id="work-start-date-text" name="workStartDateLabel" value="{{ old('workStartDateLabel') }}">
        <input type="time" id="work-start-time-text1" name="workStartTimeLabel" value="{{ old('workStartTimeLabel') }}">
        <span style="color:red">@error('workStartDateLabel'){{ $message }}
            @enderror</span>
        <span style="color:red">@error('workStartTimeLabel'){{ $message }}
            @enderror</span>
    </div>
    <div class="work-end-time">
        <label for="work-end-time-label">Waktu selesai pekerjaan</label>
        <input type="date" id="work-end-date-text" name="workEndDateLabel" value="{{ old('workEndDateLabel') }}">
        <input type="time" id="work-end-time-text1" name="workEndTimeLabel" value="{{ old('workEndTimeLabel') }}">
        <span style="color:red">@error('workEndDateLabel'){{ $message }}
            @enderror</span>
        <span style="color:red">@error('workEndTimeLabel'){{ $message }}
            @enderror</span>
        @error('datetime')<span style="color:red">{{ $message }}</span>@enderror
    </div>
    <div class="work-price">
        <label for="work-price-label">Harga</label>
        <input type="number" min="5000" id="work-price-text" name="workPriceLabel" placeholder="Min 5000" value="{{ old('workPriceLabel') }}">
        <span style="color:red">@error('workPriceLabel'){{ $message }}
            @enderror</span>
    </div>
    <button type="submit">Buat Tawaran Kerja</button>
</form>
@endsection

{{--
    Pake template itu untuk div paling luarnya. Kalo yang paling general harusnya gini

    Bagian paling atas
    <div class="container-fluid pembatas-x pembatas-y">
        {Codingan Kalian}
    </div>

    Bagian bagian selanjutnya
    <div class="container-fluid pembatas-x pembatas-b">
        {Codingan Kalian}
    </div>

    Yang div paling luar paling aman pake template yang ku bilang diawal, misalkan dibagian dalem dalemnya
    mau pake padding atas atau bawah atau kanan kiri yang sama kek yang div luarnya, bisa pake

    pembatas-x itu biar konsisten padding kanan kirinya
    pembatas-y itu biar konsisten padding atas bawahnya
    pembatas-a itu biar konsisten padding atasnya
    pembatas-b itu biar konsisten padding bawahnya
--}}