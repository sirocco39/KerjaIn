
@props(['request'])


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


<div class="card bg-white rounded-5 job-taker-card">
    <div class="card-body p-3">
        
        <h5 class="card-title-chat fw-bold mb-2" style="font-size: 20px;">{{ $request->title }}</h5>

        
        <div class="row g-3 mb-3">
            <div class="col-md-6 mb-1 icon-text">
                <img class="ms-2" src="{{ asset('Image/Icon/icon-clock-fill.svg') }}" alt="Icon Clock">
                <small class="text-muted">{{ __('chat-job-taker.card.mulai_kerja') }}</small><br>
                <strong class="detaila">{{ $request->start_time }}</strong>
            </div>
            <div class="col-md-6 mb-1 icon-text">
                <img class="ms-2" src="{{ asset('Image/Icon/icon-location-fill.svg') }}" alt="Icon Address">
                <small class="text-muted">{{ __('chat-job-taker.card.lokasi') }}</small><br>
                <strong>{{ $request->location }}</strong>
            </div>
            <div class="col-md-6 mb-1 icon-text">
                <img class="ms-2" src="{{ asset('Image/Icon/icon-clock-fill.svg') }}" alt="Icon Done">
                <small class="text-muted">{{ __('chat-job-taker.card.selesai_kerja') }}</small><br>
                <strong>{{ $request->end_time }}</strong>
                <div></div>
            </div>
            <div class="col-md-6 mb-1 icon-text">
                <img class="ms-2" src="{{ asset('Image/Icon/icon-stackMoney.svg') }}" alt="Icon Money">
                <small class="text-muted">{{ __('chat-job-taker.card.upah') }}</small><br>
                <strong>Rp{{ number_format($request->price, 0, ',', '.') }}</strong>
                <div></div>
            </div>
        </div>

        
        <div class="d-flex justify-content-between align-items-center">
            <div class="label-tersedia d-flex align-items-center justify-content-center">{{ __('chat-job-taker.card.status_tersedia') }}</div>
            
        </div>
    </div>

    
</div>
