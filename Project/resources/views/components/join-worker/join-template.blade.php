@props(['step'])    
    <link rel="stylesheet" href="{{ asset('css/joinWorker.css') }}">
    <div class="hero-banner">
        <div class="container-fluid py-4 text-white fw-bold px-5">
            <p>Yuk, jadi bagian dari <span class="text-success"> KerjaIn </span>&nbsp;–&nbsp; tempat di mana kerja
                bareng,
                bantu bareng, cuan bareng.</p>
        </div>
    </div>
    <div class="container-fluid form-float px-5">
        <div class="row justify-content-center bg-light py-4 px-2 px-sm-5 shadow-sm">
            <h6 class="fw-bold text-end fs-5x">Jadi bagian Kerja<span style="background-color: #309FFF; color: white; padding: 2px 4px; border-radius: 0.5rem;">In</span></h6>
            <div class="py-4 col-sm-10 col-lg-8 px-md-5">
                <div class="d-flex align-items-center stepper px-5">
                    <div class="circle active text-center">1</div>
                    <div class="{{ $step >= 2 ? 'line active' : 'line bg-stroke-gray' }}"></div>
                    <div class="{{ $step >= 2 ? 'circle active' : 'circle' }} text-center">2</div>
                    <div class="{{ $step == 3 ? 'line active' : 'line bg-stroke-gray' }}"></div>
                    <div class="{{ $step == 3 ? 'circle active' : 'circle' }} text-center">3</div>
                </div>
                <div class="d-flex align-items-center stepper-text pt-1">
                    <small>Data Pribadi</small>
                    <small>Detail Kontrak</small>
                    <small>Verifikasi</small>
                </div>
            </div>
            {{ $slot }}
        </div>
    </div>
