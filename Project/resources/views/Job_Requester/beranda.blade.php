@extends('Master.master-job_req')

@section('content')
    <div class="container-fluid pembatas-x pembatas-y d-flex flex-column gap-3" id="greetings-section">
        <h1 class="fw-bold mb-0">Halo, Username</h1>
        <p class="mb-0">
            Selamat datang! Di sini tempat terbaik untuk menemukan mitra kerja yang siap membantu. <br>
            Mulailah dengan membuat permintaan pekerjaan pertamamu.
        </p>
        <a href="/job-req/tawarkan-kerja" class="button-switch">
            Buat Lowongan Baru
        </a>
    </div>

    <div class="container-fluid pembatas-x pembatas-b d-flex flex-column gap-4">
        <h1 class="fw-bold mb-0">Tawaran pekerjaan saya baru-baru ini</h1>
        @foreach ($fiveLatestRequests as $r)
            <div class="work-request p-4 d-flex flex-column gap-2">
                <?php
                $startdatetime  = strtotime($r->start_time);
                $enddatetime  = strtotime($r->end_time);
                $startdate   = date('d M Y', $startdatetime);
                $starttime = date('H.i', $startdatetime);
                $enddate  = date('d M Y', $enddatetime);
                $endtime = date('H.i', $enddatetime);
                ?>

                <h3 class="fw-bold mb-0">{{ $r->title }}</h3>

                <ul class="job-card-details d-flex gap-4">
                    <li class="col-lg-5 gap-2 mb-0">
                        <div class="icon-wrapper">
                            <img src="{{ asset('Image/Icon/icon-address.svg') }}" alt="Icon Address">
                        </div>
                        <span>{{ $r->location }}</span>
                    </li>

                    <li class="col-lg gap-2">
                        <div class="icon-wrapper">
                            <img src="{{ asset('Image/Icon/icon-date.svg') }}" alt="Icon Date">
                        </div>
                        <span>{{ $startdate }}</span>
                    </li>

                    <li class="col-lg gap-2">
                        <div class="icon-wrapper">
                            <img src="{{ asset('Image/Icon/icon-clock.svg') }}" alt="Icon Clock">
                        </div>
                        <span>{{ $starttime }} - {{ $endtime }}</span>
                    </li>

                    <li class="col-lg-auto gap-2">
                        <div class="icon-wrapper">
                            <img src="{{ asset('Image/Icon/icon-dollar.svg') }}" alt="Icon Money">
                        </div>
                        <span>Rp{{ number_format( $r->price, 2, ',', '.') }}</span>
                    </li>
                    {{-- <button class="detail-button" data-request-id="{{ $request->id }}">DETAIL</button> --}}
                </ul>

                <div class="details-bottom-segment d-flex justify-content-between">
                    <div class="status">
                        <p class="mb-0">{{$r->status == 'open' ? 'Menunggu Mitra' : ''}}</p>
                    </div>
                    <a class="detail-req-button" href="{{ route('requesttt.show', $r->slug) }}">DETAIL</a>
                </div>
            </div>
            {{-- <div class="dropdown text-end">
                <button class="btn btn-light border-0" type="button" id="dropdownMenuButton{{ $loop->index }}" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-three-dots-vertical"></i>
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $loop->index }}">
                    <li><a class="dropdown-item" href="{{ route('requesttt.edit', $r->slug) }}">Edit</a></li>
                    <li>
                        <form action="{{ route('requesttt.destroy', $r->slug) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                            @csrf
                            @method('DELETE')
                            <button class="dropdown-item text-danger" type="submit">Delete</button>
                        </form>
                    </li>
                </ul>
            </div> --}}
        @endforeach
    </div>
@endsection
