@props(['search-part'])

<div class="container-fluid pembatas-x" id="search-section">
    <!-- Lingkaran Kiri -->
    <div class="circle-decor circle-left d-lg-block"></div>

    <form action="{{ route('browse.work.requests.index') }}" method="GET" class="d-flex justify-content-between gap-3">
        <div class="d-flex flex-fill align-items-center rounded-pill" id="search-form">
            <i class="fas fa-search search-icon"></i>
            <input class="flex-fill p-0" id="input-area" type="text" name="search" placeholder="Cari Pekerjaan" value="{{ request('search') }}">
        </div>
        <button type="submit" class="btn search-button rounded-pill">CARI</button>
    </form>
</div>
