{{-- Menambahkan $isSelected sebagai prop baru --}}
@props(['request', 'isSelected' => false])

{{-- Style ini bisa Anda biarkan atau hapus jika sudah ada di file CSS utama --}}
<style>
    .job-card { background-color: #fff; border: 1px solid #dee2e6; border-radius: 1rem; cursor: pointer; transition: all 0.2s ease-in-out; }
    .job-card:hover { transform: translateY(-3px); box-shadow: 0 4px 12px rgba(0,0,0,.08); }
    .job-card.selected { border-color: #103F91; background-color: #f7faff; box-shadow: 0 4px 12px rgba(20, 60, 150,.15); }
    .job-card .icon-text { display: flex; gap: 0.5rem; align-items: center; color: #6c757d; font-size: 0.9rem; }
    .job-card .label-tersedia { background-color: #103F91; color: white; border-radius: 50px; padding: 0.25rem 1rem; font-size: 0.8rem; }
</style>

{{-- Menerapkan class 'selected' secara kondisional --}}
<div class="card job-card p-3 {{ $isSelected ? 'selected' : '' }}">
    <h6 class="fw-bold mb-3">{{ $request->title }}</h6>
    <div class="row g-2">
        <div class="col-6 icon-text">
            <i class="bi bi-calendar-event"></i>
            <span>{{ \Carbon\Carbon::parse($request->start_time)->format('d M Y') }}</span>
        </div>
        <div class="col-6 icon-text">
            <i class="bi bi-geo-alt-fill"></i>
            <span>{{ $request->location }}</span>
        </div>
        <div class="col-6 icon-text">
            <i class="bi bi-clock-fill"></i>
            <span>{{ \Carbon\Carbon::parse($request->start_time)->format('H:i') }}</span>
        </div>
        <div class="col-6 icon-text">
            <i class="bi bi-cash"></i>
            <span class="fw-bold text-dark">Rp{{ number_format($request->price, 0, ',', '.') }}</span>
        </div>
    </div>
    <hr class="my-3">
    <div class="d-flex justify-content-between align-items-center">
        <span class="label-tersedia">Tersedia</span>
        <i class="bi bi-chevron-right text-muted"></i>
    </div>
</div>