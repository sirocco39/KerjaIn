<style>
  .icon-text {
    display: flex;
    align-items: center;
    gap: 8px;
  }
  .label-tersedia {
    background-color: #103F91;
    color: white;
    border-radius: 20px;
    padding: 6px 20px;
    border: none;
    height: 25px;
    width: 35%;
  }
  .detaila{
    font-size: 14px;
  }
  @media (max-width: 992px) {
    .card {
      width: 100% !important;
    }

    .icon-text {
      flex-direction: row;
      align-items: flex-start;
      gap: 10px;
    }

    .icon-text img {
      width: 20px;
      height: 20px;
      flex-shrink: 0;
    }

    .icon-text small,
    .icon-text strong {
      display: inline-block;
      margin: 0;
    }
  }

  @media (max-width: 576px) {
    .p-4 {
      padding: 1rem !important;
    }

    .card-body {
      padding: 1rem !important;
    }
  }
</style>

<div class="p-1 bg-light">
  <div class="container">
    <div class="card bg-white rounded-5" style="width: 100%;">
      <div class="card-body p-3">
        <h5 class="card-title-chat fw-bold f" style="font-size: 20px;">Bangun Candi dengan Patung Iwangg Hahh</h5>
        <div class="row my-3">
          <div class="col-md-6 mb-1 icon-text">
             <img class="ms-2" src="{{ asset('Image/Icon/icon-date.svg') }}" alt="Icon Address">
             <small class="text-muted">Tanggal</small><br>
             <strong class="detaila">19 Mei 2025</strong>
            <div>
            </div>
          </div>
          <div class="col-md-6 mb-1 icon-text">
            <img class="ms-2" src="{{ asset('Image/Icon/icon-location-fill.svg') }}" alt="Icon Address">
            <small style="margin-left: 5px" class="text-muted">Lokasi</small><br>
            <strong>Bandung</strong>
            <div>
            </div>
          </div>
          <div class="col-md-6 mb-1 icon-text">
            <img class="ms-2" src="{{ asset('Image/Icon/icon-clock-fill.svg') }}" alt="Icon Address">
            <small class="text-muted">Jam</small><br>
            <strong style="margin-left: 20px">14.00 - 16.00</strong>
            <div>
            </div>
          </div>
          <div class="col-md-6 mb-1 icon-text">
             <img class="ms-2" src="{{ asset('Image/Icon/icon-stackMoney.svg') }}" alt="Icon Address">
             <small class="text-muted">Upah</small><br>
             <strong style="margin-left:5px;" >Rp150.000</strong>
            <div>
            </div>
          </div>
        </div>
        <label class="label-tersedia d-flex align-items-center justify-content-center">Tersedia</label>
      </div>
    </div>
  </div>
</div>
