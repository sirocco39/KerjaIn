@extends('Master.master-job_taker')

@section('content')
    <div class="header-wrap" id="header-beranda-job_taker">
        <div class="container-fluid pembatas-x">
            @auth
                <h1 class="fw-bold mb-1">Halo, {{auth()->user()->first_name}}  {{auth()->user()->last_name}}</h1>
            @else
                <h1 class="fw-bold mb-1">Halo, Nama Pengguna</h1>
            @endauth
            <p class="mb-0">
                Setiap langkah kecil menuju tujuan adalah kemajuan yang berharga.
            </p>
        </div>
        <x-search></x-search>
    </div>


    <div class="container-fluid pembatas-x pembatas-y d-flex flex-column gap-4">
        <h3 class="fw-bold mb-0">Ringkasan Pengalaman Anda</h3>

        <div class="container-fluid d-flex row p-0 m-0 align-items-center justify-content-center" id="shortDetail">
            <div class="col d-flex flex-column align-items-center p-0">
                <h4 class="fw-bold title-detail m-0 mb-2">Lama Bekerja</h4>
                @auth
                    <p class="mb-0 text-p"><b class="bold-point">{{ floor(auth()->user()->created_at->diffInYears(now()))}}</b> Tahun</p>
                @else
                    <p class="mb-0 text-p"><b class="bold-point">0</b> Tahun</p>
                @endauth
            </div>

            <div class="col d-flex flex-column align-items-center p-0" id="detail-tengah">
                <h4 class="fw-bold title-detail m-0 mb-2">Pekerjaan Selesai</h4>
                @auth
                    <p class="mb-0 text-p"><b class="bold-point">{{auth()->user()->job_done}}</b> Pekerjaan</p>
                @else
                    <p class="mb-0 text-p"><b class="bold-point">0</b> Pekerjaan</p>
                @endauth
            </div>

            <div class="col d-flex flex-column align-items-center p-0">
                <h4 class="fw-bold title-detail m-0 mb-2">Rating Rata-rata</h4>
                <p class="mb-0"></p>
                @auth
                    <p class="mb-0 text-p"><b class="bold-point">{{auth()->user()->rating}}</b></p>
                @else
                    <p class="mb-0 text-p"><b class="bold-point">0</b></p>
                @endauth
            </div>
        </div>
    </div>

    <div class="container-fluid pembatas-x pembatas-b d-flex flex-column gap-4">
        <h3 class="fw-bold mb-0">Pekerjaan yang Sedang Anda Ambil</h3>

        <div class="d-flex">
            <div class="col-12 col-xl-8 d-flex flex-column gap-4 beranda-req-kiri">
                @if($fiveLatestTransaction->isEmpty())
                    <p>Anda belum pernah mengambil pekerjaan!</p>
                @else
                    @foreach ($fiveLatestTransaction as $r)
                        <div class="work-request p-4 d-flex flex-column">
                            <?php
                            $startdatetime  = strtotime($r->request->start_time);
                            $enddatetime  = strtotime($r->request->end_time);
                            $startdate   = date('d M Y', $startdatetime);
                            $starttime = date('H.i', $startdatetime);
                            $enddate  = date('d M Y', $enddatetime);
                            $endtime = date('H.i', $enddatetime);
                            ?>

                            <h4 class="fw-bold mb-1">{{ $r->title }}</h4>

                            <ul class="job-req-card-details d-flex justify-content-between mt-1 flex-column flex-md-row">
                                <li class="col-12 col-md-2 gap-2 me-2">
                                    <div class="icon-wrapper-beranda align-items-center align-items-md-start">
                                        <img src="{{ asset('Image/Icon/icon-profile.svg') }}" alt="Icon Profile">
                                    </div>
                                    <span>Kak {{ $r->requester->first_name }}</span>
                                </li>

                                <li class="col-12 col-md-3 col-lg-4 gap-2 me-2">
                                    <div class="icon-wrapper-beranda align-items-center align-items-md-start">
                                        <img src="{{ asset('Image/Icon/icon-address.svg') }}" alt="Icon Address">
                                    </div>
                                    <span>{{ $r->request->location }}</span>
                                </li>

                                <li class="col-12 col-md-2 gap-2">
                                    <div class="icon-wrapper-beranda align-items-center align-items-md-start">
                                        <img src="{{ asset('Image/Icon/icon-date.svg') }}" alt="Icon Date">
                                    </div>
                                    <span>{{ $startdate }}</span>
                                </li>

                                <li class="col-12 col-md-2 gap-2">
                                    <div class="icon-wrapper-beranda align-items-center align-items-md-start">
                                        <img src="{{ asset('Image/Icon/icon-clock.svg') }}" alt="Icon Clock">
                                    </div>
                                    <span>{{ $starttime }} - {{ $endtime }}</span>
                                </li>

                                <li class="col-12 col-md-2 gap-2">
                                    <div class="icon-wrapper-beranda align-items-center align-items-md-start">
                                        <img src="{{ asset('Image/Icon/icon-dollar.svg') }}" alt="Icon Money">
                                    </div>
                                    <span>Rp{{ number_format( $r->request->price, 2, ',', '.') }}</span>
                                </li>
                            </ul>

                            <div class="details-bottom-segment d-flex justify-content-between mt-2">
                                @if ($r->status == 'accepted')
                                    <div class="status">
                                        <p class="mb-0">Diterima</p>
                                    </div>
                                @elseif($r->status == 'in progress')
                                    <div class="status">
                                        <p class="mb-0">Dikerjain</p>
                                    </div>
                                @elseif($r->status == 'submitted')
                                    <div class="status">
                                        <p class="mb-0">Ditinjau</p>
                                    </div>
                                @elseif($r->status == 'completed')
                                    <div class="status" style="background-color:#E8FA0D; color: #294287;">
                                        <p class="mb-0">Selesai</p>
                                    </div>
                                @elseif($r->status == 'cancelled')
                                    <div class="status" style="background-color: #B02A37">
                                        <p class="mb-0">Dibatalin</p>
                                    </div>
                                @endif
                                <a class="detail-req-button" href="#">DETAIL</a>
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
                @endif
            </div>

            <div class="col beranda-req-kanan d-none d-xl-flex align-items-center justify-content-end">
                <img class="d-none d-xl-flex" src="{{ asset('Image/orang/ilus-beranda-job-taker.svg') }}" alt="People Give Money" id="ilus-beranda">
            </div>
        </div>
    </div>

    <div class="container-fluid pembatas-x pembatas-b d-flex flex-column gap-4">
        <h3 class="fw-bold mb-0">Pekerjaan yang Tersedia</h3>
        <div class="d-flex">
            <div class="col-12 d-flex flex-column gap-4 beranda-req-kiri">
                @if($fiveLatestRequests->isEmpty())
                    <p>Tidak ada pekerjaan yang tersedia!</p>
                @else
                    @foreach ($fiveLatestRequests as $r)
                        <div class="work-request p-4 d-flex flex-column">
                            <?php
                            $startdatetime  = strtotime($r->start_time);
                            $enddatetime  = strtotime($r->end_time);
                            $startdate   = date('d M Y', $startdatetime);
                            $starttime = date('H.i', $startdatetime);
                            $enddate  = date('d M Y', $enddatetime);
                            $endtime = date('H.i', $enddatetime);
                            ?>

                            <h4 class="fw-bold mb-1">{{ $r->title }}</h4>

                            <ul class="job-req-card-details d-flex justify-content-between mt-1 flex-column flex-md-row">
                                <li class="col-12 col-md-2 gap-2 me-2">
                                    <div class="icon-wrapper-beranda align-items-center align-items-md-start">
                                        <img src="{{ asset('Image/Icon/icon-profile.svg') }}" alt="Icon Profile">
                                    </div>
                                    <span>Kak {{ $r->requester->first_name }}</span>
                                </li>

                                <li class="col-12 col-md-3 col-lg-4 gap-2 me-2">
                                    <div class="icon-wrapper-beranda align-items-center align-items-md-start">
                                        <img src="{{ asset('Image/Icon/icon-address.svg') }}" alt="Icon Address">
                                    </div>
                                    <span>{{ $r->location }}</span>
                                </li>

                                <li class="col-12 col-md-2 gap-2">
                                    <div class="icon-wrapper-beranda align-items-center align-items-md-start">
                                        <img src="{{ asset('Image/Icon/icon-date.svg') }}" alt="Icon Date">
                                    </div>
                                    <span>{{ $startdate }}</span>
                                </li>

                                <li class="col-12 col-md-2 gap-2">
                                    <div class="icon-wrapper-beranda align-items-center align-items-md-start">
                                        <img src="{{ asset('Image/Icon/icon-clock.svg') }}" alt="Icon Clock">
                                    </div>
                                    <span>{{ $starttime }} - {{ $endtime }}</span>
                                </li>

                                <li class="col-12 col-md-2 gap-2">
                                    <div class="icon-wrapper-beranda align-items-center align-items-md-start">
                                        <img src="{{ asset('Image/Icon/icon-dollar.svg') }}" alt="Icon Money">
                                    </div>
                                    <span>Rp{{ number_format( $r->price, 2, ',', '.') }}</span>
                                </li>
                            </ul>

                            <div class="details-bottom-segment d-flex justify-content-between mt-2">
                                <div class="status">
                                    <p class="mb-0">{{$r->status == 'open' ? 'Tersedia' : ''}}</p>
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
                @endif
            </div>
        </div>
    </div>


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
