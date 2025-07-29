{{-- Props yang tidak lagi dibutuhkan telah dihapus --}}
@props(['request'])

{{-- Style yang berhubungan dengan bagian expand telah dihapus --}}
<style>
    .job-taker-card {
        transition: all 0.2s ease-in-out;
        border: 3px solid #dee2e6;
        border-radius: var(--bs-border-radius-xxl);
        cursor: pointer;
    }

    .job-taker-card:hover {
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        border: 1px solid #294287;
    }

    .job-taker-card:active {
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.08);
    }

    .icon-text {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .label-tersedia {
        background-color: #103F91;
        color: white;
        border-radius: 20px;
        padding: 6px 20px;
        border: none;
        height: 35px;
        width: 35%;
        font-size: 14px;
    }

    .detaila, .text-muted {
        font-size: 14px;
    }
</style>

{{-- Logika class 'is-expanded' telah dihapus dari div utama --}}
<div class="card bg-white rounded-5 job-taker-card">
    <div class="card-body p-3">
        {{-- Judul Pekerjaan --}}
        <h5 class="card-title-chat fw-bold mb-2" style="font-size: 20px;">{{ $request->title }}</h5>

        {{-- Detail (Tanggal, Jam, Lokasi, Upah) dengan ikon --}}
        <div class="row g-3 mb-3">
            <div class="col-md-6 mb-1 icon-text">
                <img class="ms-2" src="{{ asset('Image/Icon/icon-clock-fill.svg') }}" alt="Icon Clock">
                <small class="text-muted">Mulai Kerja</small><br>
                <strong class="detaila">{{ $request->start_time }}</strong>
            </div>
            <div class="col-md-6 mb-1 icon-text">
                <img class="ms-2" src="{{ asset('Image/Icon/icon-location-fill.svg') }}" alt="Icon Address">
                <small class="text-muted">Lokasi</small><br>
                <strong>{{ $request->location }}</strong>
            </div>
            <div class="col-md-6 mb-1 icon-text">
                <img class="ms-2" src="{{ asset('Image/Icon/icon-done.svg') }}" alt="Icon Done">
                <small class="text-muted">Selesai Kerja</small><br>
                <strong>{{ $request->end_time }}</strong>
                <div></div>
            </div>
            <div class="col-md-6 mb-1 icon-text">
                <img class="ms-2" src="{{ asset('Image/Icon/icon-stackMoney.svg') }}" alt="Icon Money">
                <small class="text-muted">Upah</small><br>
                <strong>Rp{{ number_format($request->price, 0, ',', '.') }}</strong>
                <div></div>
            </div>
        </div>

        {{-- Status (Tombol Expander telah dihapus) --}}
        <div class="d-flex justify-content-between align-items-center">
            <div class="label-tersedia d-flex align-items-center justify-content-center">Tersedia</div>
            {{-- Tombol expander (panah atas/bawah) telah dihapus dari sini --}}
        </div>
    </div>

    {{-- Seluruh blok @if untuk bagian expand telah dihapus --}}
</div>
