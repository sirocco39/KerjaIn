@extends('Master.master-job_req')

@section('content')
@php
$start_time = $request->start_time;
$end_time = $request->end_time;
$date = date('d M Y', strtotime($start_time));
$time = date('H:i', strtotime($start_time));
$start = new DateTime($start_time);
$end = new DateTime($end_time);
$interval = $start->diff($end);

$amount = $request->price;
$formatted = 'Rp ' . number_format($amount, 2, ',', '.');

$alamat = $request->alamat;
$alamatEncoded = urlencode($alamat);
$mapsLink = "https://www.google.com/maps/search/?api=1&query={$alamatEncoded}";

$duration = $interval->format('%h jam %i menit');
$created_at = $worker->created_at;
$year = date('F Y', strtotime($created_at));

$start_work = date('d M Y H:i', strtotime($transaction->start_work));
$finish_work = date('d M Y H:i', strtotime($transaction->finish_work));

@endphp


<div class="container-fluid pembatas-x mb-5">
    <div class="mb-2 mt-5">
        <h2 style="font-weight: 800;">Pesanan Kamu</h2>
    </div>
    <div class="contain bg-light mt-2 px-4 py-3 rounded-4 d-flex align-items-center" style="border: 1px solid #cacadd; ">
        @if($transaction->status == 'submitted')
        <div class="badge px-4 py-3 rounded-pill bg-primary text-light fs-6" style="background-color:#294287;">Ditinjau</div>
        @elseif($transaction->status == 'cancelled')
        <div class="badge px-4 py-3 rounded-pill bg-warning text-light fs-6" style="background-color:crimson;">Dibatalin</div>
        @elseif($transaction->status == 'accepted')
        <div class="badge px-4 py-3 rounded-pill bg-warning text-light fs-6" style="background-color:#294287;">Diterima</div>
        @elseif($transaction->status == 'in progress')
        <div class="badge px-4 py-3 rounded-5 bg-info text-light fs-6" style="background-color:#309FFF;">Dikerjain</div>
        @elseif($transaction->status == 'completed')
        <div class="badge px-4 py-3 rounded-pill bg-success text-dark fs-6" style="background-color:#D3FA0D;">Selesai</div>
        @endif
        <h3 class="d-inline mx-3 mt-1" style="color:#294287; font-weight: 800;">{{$request->title}}</h3>
    </div>
    <div class="one d-flex mb-3">
        <div class="two d-flex flex-column me-3" style="flex: 1;">
            <div class="contain bg-light mt-3 px-4 py-3 rounded-4 d-flex flex-column" style="border: 1px solid #cacadd;">
                <div class="fw-bold text-start">Deskripsi</div>
                <div class="text-justify" style="font-size: 12px;">{{ $request->description }}</div>
            </div>
            <div class="contain bg-light mt-3 px-4 py-3 rounded-4 d-flex flex-column align-items-center" style="border: 1px solid #cacadd;">
                <div class="d-flex flex-fill justify-content-between " style="width: 100%;">
                    <div class="separate d-flex align-items-center">
                        <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M14.875 14.875C14.875 15.1071 14.7828 15.3296 14.6187 15.4937C14.4546 15.6578 14.2321 15.75 14 15.75C13.7679 15.75 13.5454 15.6578 13.3813 15.4937C13.2172 15.3296 13.125 15.1071 13.125 14.875C13.125 14.6429 13.2172 14.4204 13.3813 14.2563C13.5454 14.0922 13.7679 14 14 14C14.2321 14 14.4546 14.0922 14.6187 14.2563C14.7828 14.4204 14.875 14.6429 14.875 14.875ZM8.75 18.375C8.98206 18.375 9.20462 18.2828 9.36872 18.1187C9.53281 17.9546 9.625 17.7321 9.625 17.5C9.625 17.2679 9.53281 17.0454 9.36872 16.8813C9.20462 16.7172 8.98206 16.625 8.75 16.625C8.51794 16.625 8.29538 16.7172 8.13128 16.8813C7.96719 17.0454 7.875 17.2679 7.875 17.5C7.875 17.7321 7.96719 17.9546 8.13128 18.1187C8.29538 18.2828 8.51794 18.375 8.75 18.375ZM9.625 20.125C9.625 20.3571 9.53281 20.5796 9.36872 20.7437C9.20462 20.9078 8.98206 21 8.75 21C8.51794 21 8.29538 20.9078 8.13128 20.7437C7.96719 20.5796 7.875 20.3571 7.875 20.125C7.875 19.8929 7.96719 19.6704 8.13128 19.5063C8.29538 19.3422 8.51794 19.25 8.75 19.25C8.98206 19.25 9.20462 19.3422 9.36872 19.5063C9.53281 19.6704 9.625 19.8929 9.625 20.125ZM11.375 18.375C11.6071 18.375 11.8296 18.2828 11.9937 18.1187C12.1578 17.9546 12.25 17.7321 12.25 17.5C12.25 17.2679 12.1578 17.0454 11.9937 16.8813C11.8296 16.7172 11.6071 16.625 11.375 16.625C11.1429 16.625 10.9204 16.7172 10.7563 16.8813C10.5922 17.0454 10.5 17.2679 10.5 17.5C10.5 17.7321 10.5922 17.9546 10.7563 18.1187C10.9204 18.2828 11.1429 18.375 11.375 18.375ZM12.25 20.125C12.25 20.3571 12.1578 20.5796 11.9937 20.7437C11.8296 20.9078 11.6071 21 11.375 21C11.1429 21 10.9204 20.9078 10.7563 20.7437C10.5922 20.5796 10.5 20.3571 10.5 20.125C10.5 19.8929 10.5922 19.6704 10.7563 19.5063C10.9204 19.3422 11.1429 19.25 11.375 19.25C11.6071 19.25 11.8296 19.3422 11.9937 19.5063C12.1578 19.6704 12.25 19.8929 12.25 20.125ZM14 18.375C14.2321 18.375 14.4546 18.2828 14.6187 18.1187C14.7828 17.9546 14.875 17.7321 14.875 17.5C14.875 17.2679 14.7828 17.0454 14.6187 16.8813C14.4546 16.7172 14.2321 16.625 14 16.625C13.7679 16.625 13.5454 16.7172 13.3813 16.8813C13.2172 17.0454 13.125 17.2679 13.125 17.5C13.125 17.7321 13.2172 17.9546 13.3813 18.1187C13.5454 18.2828 13.7679 18.375 14 18.375ZM14.875 20.125C14.875 20.3571 14.7828 20.5796 14.6187 20.7437C14.4546 20.9078 14.2321 21 14 21C13.7679 21 13.5454 20.9078 13.3813 20.7437C13.2172 20.5796 13.125 20.3571 13.125 20.125C13.125 19.8929 13.2172 19.6704 13.3813 19.5063C13.5454 19.3422 13.7679 19.25 14 19.25C14.2321 19.25 14.4546 19.3422 14.6187 19.5063C14.7828 19.6704 14.875 19.8929 14.875 20.125ZM16.625 18.375C16.8571 18.375 17.0796 18.2828 17.2437 18.1187C17.4078 17.9546 17.5 17.7321 17.5 17.5C17.5 17.2679 17.4078 17.0454 17.2437 16.8813C17.0796 16.7172 16.8571 16.625 16.625 16.625C16.3929 16.625 16.1704 16.7172 16.0063 16.8813C15.8422 17.0454 15.75 17.2679 15.75 17.5C15.75 17.7321 15.8422 17.9546 16.0063 18.1187C16.1704 18.2828 16.3929 18.375 16.625 18.375ZM17.5 20.125C17.5 20.3571 17.4078 20.5796 17.2437 20.7437C17.0796 20.9078 16.8571 21 16.625 21C16.3929 21 16.1704 20.9078 16.0063 20.7437C15.8422 20.5796 15.75 20.3571 15.75 20.125C15.75 19.8929 15.8422 19.6704 16.0063 19.5063C16.1704 19.3422 16.3929 19.25 16.625 19.25C16.8571 19.25 17.0796 19.3422 17.2437 19.5063C17.4078 19.6704 17.5 19.8929 17.5 20.125ZM19.25 18.375C19.4821 18.375 19.7046 18.2828 19.8687 18.1187C20.0328 17.9546 20.125 17.7321 20.125 17.5C20.125 17.2679 20.0328 17.0454 19.8687 16.8813C19.7046 16.7172 19.4821 16.625 19.25 16.625C19.0179 16.625 18.7954 16.7172 18.6313 16.8813C18.4672 17.0454 18.375 17.2679 18.375 17.5C18.375 17.7321 18.4672 17.9546 18.6313 18.1187C18.7954 18.2828 19.0179 18.375 19.25 18.375ZM17.5 14.875C17.5 15.1071 17.4078 15.3296 17.2437 15.4937C17.0796 15.6578 16.8571 15.75 16.625 15.75C16.3929 15.75 16.1704 15.6578 16.0063 15.4937C15.8422 15.3296 15.75 15.1071 15.75 14.875C15.75 14.6429 15.8422 14.4204 16.0063 14.2563C16.1704 14.0922 16.3929 14 16.625 14C16.8571 14 17.0796 14.0922 17.2437 14.2563C17.4078 14.4204 17.5 14.6429 17.5 14.875ZM19.25 15.75C19.4821 15.75 19.7046 15.6578 19.8687 15.4937C20.0328 15.3296 20.125 15.1071 20.125 14.875C20.125 14.6429 20.0328 14.4204 19.8687 14.2563C19.7046 14.0922 19.4821 14 19.25 14C19.0179 14 18.7954 14.0922 18.6313 14.2563C18.4672 14.4204 18.375 14.6429 18.375 14.875C18.375 15.1071 18.4672 15.3296 18.6313 15.4937C18.7954 15.6578 19.0179 15.75 19.25 15.75Z" fill="#133E87" />
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M7.875 2.625C8.10706 2.625 8.32962 2.71719 8.49372 2.88128C8.65781 3.04538 8.75 3.26794 8.75 3.5V5.25H19.25V3.5C19.25 3.26794 19.3422 3.04538 19.5063 2.88128C19.6704 2.71719 19.8929 2.625 20.125 2.625C20.3571 2.625 20.5796 2.71719 20.7437 2.88128C20.9078 3.04538 21 3.26794 21 3.5V5.25H21.875C22.8033 5.25 23.6935 5.61875 24.3499 6.27513C25.0063 6.9315 25.375 7.82174 25.375 8.75V21.875C25.375 22.8033 25.0063 23.6935 24.3499 24.3499C23.6935 25.0063 22.8033 25.375 21.875 25.375H6.125C5.19674 25.375 4.3065 25.0063 3.65013 24.3499C2.99375 23.6935 2.625 22.8033 2.625 21.875V8.75C2.625 7.82174 2.99375 6.9315 3.65013 6.27513C4.3065 5.61875 5.19674 5.25 6.125 5.25H7V3.5C7 3.26794 7.09219 3.04538 7.25628 2.88128C7.42038 2.71719 7.64294 2.625 7.875 2.625ZM23.625 13.125C23.625 12.6609 23.4406 12.2158 23.1124 11.8876C22.7842 11.5594 22.3391 11.375 21.875 11.375H6.125C5.66087 11.375 5.21575 11.5594 4.88756 11.8876C4.55937 12.2158 4.375 12.6609 4.375 13.125V21.875C4.375 22.3391 4.55937 22.7842 4.88756 23.1124C5.21575 23.4406 5.66087 23.625 6.125 23.625H21.875C22.3391 23.625 22.7842 23.4406 23.1124 23.1124C23.4406 22.7842 23.625 22.3391 23.625 21.875V13.125Z" fill="#133E87" />
                        </svg>

                        <div class="p-2">Tanggal</div>
                    </div>
                    <div class="py-2 fw-bold text-end">{{$date}}</div>
                </div>
                <div class="d-flex flex-fill justify-content-between" style="width: 100%;">
                    <div class="separate d-flex align-items-center">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2.25C6.615 2.25 2.25 6.615 2.25 12C2.25 17.385 6.615 21.75 12 21.75C17.385 21.75 21.75 17.385 21.75 12C21.75 6.615 17.385 2.25 12 2.25ZM12.75 6C12.75 5.80109 12.671 5.61032 12.5303 5.46967C12.3897 5.32902 12.1989 5.25 12 5.25C11.8011 5.25 11.6103 5.32902 11.4697 5.46967C11.329 5.61032 11.25 5.80109 11.25 6V12C11.25 12.414 11.586 12.75 12 12.75H16.5C16.6989 12.75 16.8897 12.671 17.0303 12.5303C17.171 12.3897 17.25 12.1989 17.25 12C17.25 11.8011 17.171 11.6103 17.0303 11.4697C16.8897 11.329 16.6989 11.25 16.5 11.25H12.75V6Z" fill="#133E87" />
                        </svg>

                        <div class="p-2">Jam</div>
                    </div>
                    <div class="py-2 fw-bold text-end">{{$time}}</div>
                </div>
                <div class="d-flex flex-fill justify-content-between" style="width: 100%;">
                    <div class="separate d-flex align-items-center">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2.25C6.615 2.25 2.25 6.615 2.25 12C2.25 17.385 6.615 21.75 12 21.75C17.385 21.75 21.75 17.385 21.75 12C21.75 6.615 17.385 2.25 12 2.25ZM12.75 6C12.75 5.80109 12.671 5.61032 12.5303 5.46967C12.3897 5.32902 12.1989 5.25 12 5.25C11.8011 5.25 11.6103 5.32902 11.4697 5.46967C11.329 5.61032 11.25 5.80109 11.25 6V12C11.25 12.414 11.586 12.75 12 12.75H16.5C16.6989 12.75 16.8897 12.671 17.0303 12.5303C17.171 12.3897 17.25 12.1989 17.25 12C17.25 11.8011 17.171 11.6103 17.0303 11.4697C16.8897 11.329 16.6989 11.25 16.5 11.25H12.75V6Z" fill="#133E87" />
                        </svg>

                        <div class="p-2">Durasi</div>
                    </div>
                    <div class="py-2 fw-bold text-end">{{$duration}}</div>
                </div>
                <div class="d-flex flex-fill justify-content-between" style="width: 100%;">
                    <div class="separate d-flex align-items-center">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 7.5C11.4033 7.5 10.831 7.73705 10.409 8.15901C9.98705 8.58097 9.75 9.15326 9.75 9.75C9.75 10.3467 9.98705 10.919 10.409 11.341C10.831 11.7629 11.4033 12 12 12C12.5967 12 13.169 11.7629 13.591 11.341C14.0129 10.919 14.25 10.3467 14.25 9.75C14.25 9.15326 14.0129 8.58097 13.591 8.15901C13.169 7.73705 12.5967 7.5 12 7.5Z" fill="#133E87" />
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M1.5 4.875C1.5 3.839 2.34 3 3.375 3H20.625C21.66 3 22.5 3.84 22.5 4.875V14.625C22.5 15.661 21.66 16.5 20.625 16.5H3.375C3.12877 16.5 2.88495 16.4515 2.65747 16.3573C2.42998 16.263 2.22328 16.1249 2.04917 15.9508C1.87506 15.7767 1.73695 15.57 1.64273 15.3425C1.5485 15.115 1.5 14.8712 1.5 14.625V4.875ZM8.25 9.75C8.25 8.75544 8.64509 7.80161 9.34835 7.09835C10.0516 6.39509 11.0054 6 12 6C12.9946 6 13.9484 6.39509 14.6517 7.09835C15.3549 7.80161 15.75 8.75544 15.75 9.75C15.75 10.7446 15.3549 11.6984 14.6517 12.4017C13.9484 13.1049 12.9946 13.5 12 13.5C11.0054 13.5 10.0516 13.1049 9.34835 12.4017C8.64509 11.6984 8.25 10.7446 8.25 9.75ZM18.75 9C18.5511 9 18.3603 9.07902 18.2197 9.21967C18.079 9.36032 18 9.55109 18 9.75V9.758C18 10.172 18.336 10.508 18.75 10.508H18.758C18.9569 10.508 19.1477 10.429 19.2883 10.2883C19.429 10.1477 19.508 9.95691 19.508 9.758V9.75C19.508 9.55109 19.429 9.36032 19.2883 9.21967C19.1477 9.07902 18.9569 9 18.758 9H18.75ZM4.5 9.75C4.5 9.55109 4.57902 9.36032 4.71967 9.21967C4.86032 9.07902 5.05109 9 5.25 9H5.258C5.45691 9 5.64768 9.07902 5.78833 9.21967C5.92898 9.36032 6.008 9.55109 6.008 9.75V9.758C6.008 9.95691 5.92898 10.1477 5.78833 10.2883C5.64768 10.429 5.45691 10.508 5.258 10.508H5.25C5.05109 10.508 4.86032 10.429 4.71967 10.2883C4.57902 10.1477 4.5 9.95691 4.5 9.758V9.75Z" fill="#133E87" />
                            <path d="M2.25 18C2.05109 18 1.86032 18.079 1.71967 18.2197C1.57902 18.3603 1.5 18.5511 1.5 18.75C1.5 18.9489 1.57902 19.1397 1.71967 19.2803C1.86032 19.421 2.05109 19.5 2.25 19.5C7.65 19.5 12.88 20.222 17.85 21.575C19.04 21.899 20.25 21.017 20.25 19.755V18.75C20.25 18.5511 20.171 18.3603 20.0303 18.2197C19.8897 18.079 19.6989 18 19.5 18H2.25Z" fill="#133E87" />
                        </svg>

                        <div class="p-2">Upah</div>
                    </div>
                    <div class="py-2 fw-bold text-end">{{$formatted}}</div>
                </div>
                <div class="d-flex flex-fill justify-content-between" style="width: 100%;">
                    <div class="separate d-flex align-items-center">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M11.54 22.351L11.61 22.391L11.638 22.407C11.749 22.467 11.8733 22.4985 11.9995 22.4985C12.1257 22.4985 12.25 22.467 12.361 22.407L12.389 22.392L12.46 22.351C12.8511 22.1191 13.2328 21.8716 13.604 21.609C14.5651 20.9305 15.463 20.1667 16.287 19.327C18.231 17.337 20.25 14.347 20.25 10.5C20.25 8.31196 19.3808 6.21354 17.8336 4.66637C16.2865 3.11919 14.188 2.25 12 2.25C9.81196 2.25 7.71354 3.11919 6.16637 4.66637C4.61919 6.21354 3.75 8.31196 3.75 10.5C3.75 14.346 5.77 17.337 7.713 19.327C8.53664 20.1667 9.43427 20.9304 10.395 21.609C10.7666 21.8716 11.1485 22.1191 11.54 22.351ZM12 13.5C12.7956 13.5 13.5587 13.1839 14.1213 12.6213C14.6839 12.0587 15 11.2956 15 10.5C15 9.70435 14.6839 8.94129 14.1213 8.37868C13.5587 7.81607 12.7956 7.5 12 7.5C11.2044 7.5 10.4413 7.81607 9.87868 8.37868C9.31607 8.94129 9 9.70435 9 10.5C9 11.2956 9.31607 12.0587 9.87868 12.6213C10.4413 13.1839 11.2044 13.5 12 13.5Z" fill="#133E87" />
                        </svg>

                        <div class="p-2">Lokasi</div>
                    </div>
                    <div class="py-2 fw-bold text-end">
                        <a href={{$mapsLink}}
                            target="_blank" class="text-end"
                            style="text-decoration: none; color: #007BFF;">
                            {{ $request->location }}
                        </a>
                    </div>
                </div>
            </div>
            <div class="contain bg-light mt-3 px-4 py-3 rounded-4 d-flex align-items-center justify-content-between" style="border: 1px solid #cacadd;">
                <img src="{{ asset('Image/orang/ilus-beranda-job-taker.svg') }}" alt="" style="width:50%; aspect-ratio:2/3; object-fit:cover;" class="me-3">
                <div class="info d-flex flex-column">
                    <div id="name" class="fw-bold">{{$worker->first_name . ' ' . $worker->last_name}}</div>
                    <div id="rating" class="d-flex">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M8.99008 2.67508C9.36342 1.77758 10.6368 1.77758 11.0101 2.67508L12.7451 6.84675L17.2484 7.20841C18.2184 7.28591 18.6118 8.49591 17.8726 9.12924L14.4418 12.0684L15.4893 16.4626C15.7151 17.4092 14.6859 18.1567 13.8559 17.6501L10.0001 15.2951L6.14425 17.6501C5.31425 18.1567 4.28508 17.4084 4.51092 16.4626L5.55842 12.0684L2.12758 9.12924C1.38842 8.49591 1.78175 7.28591 2.75175 7.20841L7.25508 6.84675L8.99008 2.67508Z" fill="#FFDD00" />
                        </svg>

                        <div class="rate fw-semibold">{{$worker->rating}}</div>
                        <div class="banyak">(120)</div>
                    </div>
                    <div id="join">
                        <p style="font-size: 9px;">Bergabung dengan Kerjain sejak <span>{{$year}}</span></p>
                    </div>
                </div>
            </div>

            @if ($transaction->status !== 'cancelled')
            <div class="contain bg-light mt-3 px-4 py-3 rounded-4 d-flex flex-column align-items-center justify-content-center" style="border: 1px solid #cacadd;">

                <!-- Tombol Tandai Selesai -->
                @if($transaction->status == 'submitted')
                <!-- Tombol Tandai Selesai hanya muncul jika status sesuai -->
                <button type="button" class="btn px-4 py-2 rounded-pill text-light fs-4 fw-bold" data-bs-toggle="modal" data-bs-target="#completeJobModal" id="completeJobBtn" style="background-color:#294287; width:88%;">
                    Tandai Selesai
                </button>
                @endif

                @if($transaction->status == 'completed')
                <!-- Tombol Tandai Selesai hanya muncul jika status sesuai -->
                <a class="btn btn-success px-4 py-2 rounded-pill fs-4 fw-bold" style="width:88%;cursor: not-allowed;pointer-events: none;">
                    Selesai
                </a>
                @endif

                {{-- Lihat Bukti Penyelesaian --}}
                @if($transaction->status == 'in progress')
                <a class="btn px-4 py-2 rounded-5 d-inline fw-semibold fs-5" href="#" style="color: #a7a7a7; text-decoration: none; cursor: not-allowed; pointer-events: none;">
                    Lihat Bukti Penyelesaian
                </a>
                @elseif($transaction->status == 'submitted' || $transaction->status == 'completed')
                <a class="btn px-4 py-2 rounded-5 d-inline fw-semibold fs-5" href="#" data-bs-toggle="modal" data-bs-target="#completionProofModal" style="color: #0d6efd; text-decoration: none;">
                    Lihat Bukti Penyelesaian
                </a>
                @endif

                {{-- Batalkan --}}
                @if($transaction->status == 'accepted')
                <div class="px-4 py-2 rounded-5 d-inline fw-semibold text-light fs-4 text-center" style="background-color:#9d9d9d; width:88%;">Tandai Selesai</div>
                <div class="btn px-4 py-2 rounded-5 d-inline fw-semibold text-danger fs-5" data-bs-toggle="modal" data-bs-target="#cancelWorkModal">Batalkan Kerja</div>
                @endif

            </div>
            @endif
        </div>
        <div class="three flex-grow-3 container-fluid p-0 mt-3" style="flex: 3;">
            <div class="contain bg-light px-4 py-3 rounded-top-4 d-flex align-items-center" style="border: 1px solid #cacadd; height:12%;">
                <div class="atas d-flex justify-content-between align-items-center flex-fill">
                    <div class="profile d-flex">
                        <img src="{{ asset('Image/orang/ilus-beranda-job-taker.svg') }}" alt="" style="width: 48px; height: 48px;" class="rounded-5">
                        <div class="containe-name-status ms-2 d-flex flex-column">
                            <div class="name fw-bold">{{$worker->first_name . ' ' . $worker->last_name}}</div>
                            <div class="kecil">Online</div>
                        </div>
                    </div>
                    <div class="bullet rounded-5" style="width:24px; height:24px; background-color: greenyellow;"></div>
                </div>
            </div>
            <div class="px-4 chat-container container-fluid bg-light rounded-bottom-4 flex-fill flex-column justify-content-between" style="height:88%; border: 1px solid #cacadd;">
                <div class="chat-layout flex-fill" style="flex:6; height:85%;"></div>
                <div class="send-layout d-flex align-items-center justify-content-between" style="flex:1; height:15%;">
                    <form action="" class="d-flex flex-fill">
                        @csrf
                        <input type="text" class="me-2 form-control rounded-5 flex-grow-1" id="biaya" placeholder="Tulis pesan...">
                        <button class="btn rounded-5" style="background-color:#309FFF; height:100%; aspect-ratio: 1/1;">
                            <svg width="29" height="30" viewBox="0 0  30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7.80209 15.0002L4.61035 4.62793C12.2454 6.84848 19.4452 10.3563 25.8995 15.0002C19.4456 19.644 12.2461 23.1519 4.61152 25.3725L7.80209 15.0002ZM7.80209 15.0002H16.5674" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Modal tandai selesai flow pertama-->
<div class="modal fade" id="completeJobModal" tabindex="-1" aria-labelledby="completeJobModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-content p-4 d-flex justify-content-center">
                <div class="d-flex flex-fill justify-content-center">
                    <svg width="42" height="42" viewBox="0 0 42 42" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M0.6875 21C0.6875 9.78125 9.78125 0.6875 21 0.6875C32.2188 0.6875 41.3125 9.78125 41.3125 21C41.3125 32.2188 32.2188 41.3125 21 41.3125C9.78125 41.3125 0.6875 32.2188 0.6875 21ZM21 13.1875C21.4144 13.1875 21.8118 13.3521 22.1049 13.6451C22.3979 13.9382 22.5625 14.3356 22.5625 14.75V22.5625C22.5625 22.9769 22.3979 23.3743 22.1049 23.6674C21.8118 23.9604 21.4144 24.125 21 24.125C20.5856 24.125 20.1882 23.9604 19.8951 23.6674C19.6021 23.3743 19.4375 22.9769 19.4375 22.5625V14.75C19.4375 14.3356 19.6021 13.9382 19.8951 13.6451C20.1882 13.3521 20.5856 13.1875 21 13.1875ZM21 30.375C21.4144 30.375 21.8118 30.2104 22.1049 29.9174C22.3979 29.6243 22.5625 29.2269 22.5625 28.8125C22.5625 28.3981 22.3979 28.0007 22.1049 27.7076C21.8118 27.4146 21.4144 27.25 21 27.25C20.5856 27.25 20.1882 27.4146 19.8951 27.7076C19.6021 28.0007 19.4375 28.3981 19.4375 28.8125C19.4375 29.2269 19.6021 29.6243 19.8951 29.9174C20.1882 30.2104 20.5856 30.375 21 30.375Z" fill="#D3FA0D" />
                    </svg>
                    <h5 class="mx-3 fw-bold fs-2 mt-1" style="color:#309FFF;">Selesaikan Pekerjaan?</h5>
                    <svg width="42" height="42" viewBox="0 0 42 42" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M0.6875 21C0.6875 9.78125 9.78125 0.6875 21 0.6875C32.2188 0.6875 41.3125 9.78125 41.3125 21C41.3125 32.2188 32.2188 41.3125 21 41.3125C9.78125 41.3125 0.6875 32.2188 0.6875 21ZM21 13.1875C21.4144 13.1875 21.8118 13.3521 22.1049 13.6451C22.3979 13.9382 22.5625 14.3356 22.5625 14.75V22.5625C22.5625 22.9769 22.3979 23.3743 22.1049 23.6674C21.8118 23.9604 21.4144 24.125 21 24.125C20.5856 24.125 20.1882 23.9604 19.8951 23.6674C19.6021 23.3743 19.4375 22.9769 19.4375 22.5625V14.75C19.4375 14.3356 19.6021 13.9382 19.8951 13.6451C20.1882 13.3521 20.5856 13.1875 21 13.1875ZM21 30.375C21.4144 30.375 21.8118 30.2104 22.1049 29.9174C22.3979 29.6243 22.5625 29.2269 22.5625 28.8125C22.5625 28.3981 22.3979 28.0007 22.1049 27.7076C21.8118 27.4146 21.4144 27.25 21 27.25C20.5856 27.25 20.1882 27.4146 19.8951 27.7076C19.6021 28.0007 19.4375 28.3981 19.4375 28.8125C19.4375 29.2269 19.6021 29.6243 19.8951 29.9174C20.1882 30.2104 20.5856 30.375 21 30.375Z" fill="#D3FA0D" />
                    </svg>
                </div>
                <div class="d-flex flex-column flex-fill justify-content-center text-center my-4 fw-semibold" style="font-size: 16px;">
                    <div>Apakah kamu yakin pekerjaan ini sudah benar-benar selesai?</div>
                    <div>Setelah pekerjaan diselesaikan, kamu tidak dapat mengubah statusnya kembali.</div>
                </div>
                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn py-3 fw-semibold rounded-4" style="color:#294287; border-color:#294287; border-width: 2px; width:45%;" data-bs-dismiss="modal">Kembali</button>
                    <button type="button" class="btn py-3 text-light fw-semibold rounded-4" style="background-color:#309FFF; width:45%;" data-bs-toggle="modal" data-bs-target="#completionModal">Ya, Selesaikan Pekerjaan</button>
                </div>
                <!-- <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#completionModal">Ya, Selesaikan Pekerjaan</button>
                </div> -->
            </div>
        </div>
    </div>
</div>



<!-- Modal tandai selesai flow kedua-->
<div class="modal fade" id="completionModal" tabindex="-1" aria-labelledby="completionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg py-5 px-5" style="max-width: 1000px; width: 100%;">
        <form action="{{ route('reviews.store', $transaction->id) }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold fs-3" id="completionModalLabel">Detail Penyelesaian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body d-flex">
                    <!-- Detail Pesanan -->
                    <div class="d-flex flex-column" style="width:50%;">
                        <div class="d-flex flex-fill">
                            <div class="text flex-fill">
                                <p class="m-0 p-0 text-black-50 fw-semibold">Judul Pesanan</p>
                                <p class="fw-medium">{{ $request->title }}</p>
                            </div>
                            <div class="text">
                                <p class="m-0 p-0 text-black-50 fw-semibold"></p>
                                <p class="fw-medium"></p>
                            </div>
                        </div>
                        <div class="d-flex">
                            <div class="text">
                                <p class="m-0 p-0 text-black-50 fw-semibold">Nomor Pesanan</p>
                                <p class="fw-medium">{{ $orderNumber }}</p>
                            </div>
                            <div class="text">
                                <p class="m-0 p-0 text-black-50 fw-semibold"></p>
                                <p class="fw-medium"></p>
                            </div>
                        </div>
                        <div class="d-flex flex-fill">
                            <div class="text" style="width:50%;">
                                <p class="m-0 p-0 text-black-50 fw-semibold">Nama Pekerja</p>
                                <p class="fw-medium">{{ $worker->first_name }} {{ $worker->last_name }}</p>
                            </div>
                            <div class="text" style="width:50%;">
                                <p class="m-0 p-0 text-black-50 fw-semibold">Lokasi</p>
                                <p class="fw-medium">{{ $request->location }}</p>
                            </div>
                        </div>
                        <div class="d-flex">
                            <div class="text" style="width:50%;">
                                <p class="m-0 p-0 text-black-50 fw-semibold">Tanggal Pemesanan</p>
                                <p class="fw-medium">{{ $transaction->created_at->format('d M Y') }}</p>
                            </div>
                            <div class="text" style="width:50%;">
                                <p class="m-0 p-0 text-black-50 fw-semibold">Tanggal Selesai</p>
                                <p class="fw-medium">{{ $transaction->updated_at->format('d M Y') }}</p>
                            </div>
                        </div>
                        <hr class="my-1 border border-dark">
                        <div class="d-flex mt-1">
                            <div class="text d-flex justify-content-between align-items-center" style="width:50%;">
                                <p class="p-0 m-0 text-black-50 fw-semibold fs-6">Total</p>
                                <p class="p-0 m-0 fw-medium fs-6">Rp {{ number_format($request->price, 2, ',', '.') }}</p>
                            </div>
                            <div class="text d-flex justify-content-end align-items-center" style="width:50%;">
                                <a href="#" class="d-flex text-decoration-none justify-content-center align-items-center">
                                    <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M8.5029 12.668L3.29334 7.45843L4.75202 5.94766L7.46099 8.65663V0.165039H9.54482V8.65663L12.2538 5.94766L13.7125 7.45843L8.5029 12.668ZM2.25143 16.8356C1.67838 16.8356 1.18781 16.6316 0.779726 16.2235C0.371644 15.8154 0.167603 15.3249 0.167603 14.7518V11.6261H2.25143V14.7518H14.7544V11.6261H16.8382V14.7518C16.8382 15.3249 16.6342 15.8154 16.2261 16.2235C15.818 16.6316 15.3274 16.8356 14.7544 16.8356H2.25143Z" fill="#309FFF" />
                                    </svg>
                                    <div class="ms-2 fw-medium fs-5">Invoice</div>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="vr mx-3"></div>
                    <div class="d-flex flex-column align-items-center justify-content-center" style="width:50%;">
                        <h4 class="fw-semibold mb-1">Kasih penilaian, yuk!</h4>
                        <div class="text-center mt-0 mb-3" style="width:100%;">
                            @for ($i = 1; $i <= 5; $i++)
                                <i class="bi bi-star-fill text-secondary star-rating fs-2" style="font-size: 40px;"
                                data-value="{{ $i }}"></i>
                                @endfor
                                <input type="hidden" name="rating" id="rating-input" value="0">
                        </div>
                        <!-- Komentar -->
                        <div class="ps-3 flex-fill d-flex justify-content-start flex-column" style="width:94%;">
                            <label for="comment" class="form-label text-start">Komentar</label>
                            <textarea name="comment" id="comment" class="form-control" rows="3" placeholder="Tulis komentarmu di sini..." style="height: 100%; border-color:#8a8a8a;"></textarea>
                        </div>
                        <div class="d-flex flex-column mt-3 justify-content-center">
                            <button type="submit" class="btn btn-primary fw-medium rounded-3">Kirim</button>
                            <div class="m-1 text-center">Atau</div>
                            <button type="button" class="m-0 p-0 fw-medium btn text-danger" data-bs-toggle="modal" data-bs-target="#reportWorkModal">Laporkan masalah</button>
                        </div>
                    </div>


                </div>
            </div>
        </form>
    </div>
</div>


<!-- Modal lihat bukti penyelesaian -->
<div class="modal fade" id="completionProofModal" tabindex="-1" aria-labelledby="completionProofModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg py-5 px-5" style="max-width: 800px; width: 100%; height:auto;">
        <div class="modal-content p-3">
            <div class="modal-header d-flex m-0 p-0 mb-3">
                <h3 class="modal-title text-center flex-fill fw-semibold" id="completionProofModalLabel">Bukti Penyelesaian Pekerjaan</h3>
            </div>
            <div class="d-flex">
                <div class="d-flex flex-column" style="width:50%;">
                    <h6 class="fw-bold">Lampiran Bukti Pekerjaan</h6>
                    {{-- Tempat Foto Bukti --}}
                    <div class="rounded-3 my-2 p-2 d-flex justify-content-center align-items-center"
                        style="border-color:#8a8a8a; border-style:solid; width:100%; height:40vh; border-width:1px; background-color: #f9f9f9; overflow: hidden;">

                        @if (!empty($completionProof) && $completionProof->photo_url)
                        <img src="{{ $completionProof->photo_url }}"
                            alt="Bukti Foto"
                            style="width: 100%; height: 100%; object-fit: cover;"
                            class="rounded">
                        @else
                        <div class="text-center text-muted d-flex flex-column justify-content-center align-items-center">
                            <!-- Placeholder SVG -->
                            <svg width="80" height="80" viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M7.5 30C7.5 27.0163 8.68526 24.1548 10.795 22.045C12.9048 19.9353 15.7663 18.75 18.75 18.75H101.25C104.234 18.75 107.095 19.9353 109.205 22.045C111.315 24.1548 112.5 27.0163 112.5 30V90C112.5 92.9837 111.315 95.8452 109.205 97.9549C107.095 100.065 104.234 101.25 101.25 101.25H18.75C15.7663 101.25 12.9048 100.065 10.795 97.9549C8.68526 95.8452 7.5 92.9837 7.5 90V30ZM15 80.3V90C15 92.07 16.68 93.75 18.75 93.75H101.25C102.245 93.75 103.198 93.3549 103.902 92.6517C104.605 91.9484 105 90.9946 105 90V80.3L91.55 66.855C90.1437 65.4505 88.2375 64.6616 86.25 64.6616C84.2625 64.6616 82.3563 65.4505 80.95 66.855L76.55 71.25L81.4 76.1C81.7684 76.4433 82.0639 76.8573 82.2689 77.3173C82.4739 77.7773 82.5841 78.2739 82.593 78.7774C82.6018 79.2809 82.5092 79.781 82.3206 80.248C82.132 80.7149 81.8513 81.1391 81.4952 81.4952C81.1391 81.8513 80.7149 82.132 80.248 82.3206C79.781 82.5092 79.2809 82.6018 78.7774 82.593C78.2739 82.5841 77.7773 82.4739 77.3173 82.2689C76.8573 82.0639 76.4433 81.7684 76.1 81.4L50.3 55.605C48.8937 54.2005 46.9875 53.4116 45 53.4116C43.0125 53.4116 41.1063 54.2005 39.7 55.605L15 80.305V80.3ZM65.625 41.25C65.625 39.7582 66.2176 38.3274 67.2725 37.2725C68.3274 36.2176 69.7582 35.625 71.25 35.625C72.7418 35.625 74.1726 36.2176 75.2275 37.2725C76.2824 38.3274 76.875 39.7582 76.875 41.25C76.875 42.7418 76.2824 44.1726 75.2275 45.2275C74.1726 46.2824 72.7418 46.875 71.25 46.875C69.7582 46.875 68.3274 46.2824 67.2725 45.2275C66.2176 44.1726 65.625 42.7418 65.625 41.25Z" fill="#294287" />
                            </svg>
                            <div class="fw-semibold">Tidak ada bukti foto dari pekerja.</div>
                        </div>
                        @endif

                    </div>
                </div>
                <div class="vr mx-3"></div>

                <div class="d-flex flex-column" style="width:50%;">
                    <h6 class="fw-bold">Catatan dari Pekerja</h6>
                    <div class="rounded-3 mt-2 mb-4 p-2" style="border-color:#8a8a8a; border-style:solid; width:100%; height:64%; border-width:1px;">
                        <p>{{ $completionProof->note ?? 'Tidak ada catatan dari pekerja.' }}</p>
                    </div>
                    <div class="flex-fill" style="width:100%; height:10%;">
                        <button type="button" class="flex-fill rounded-4 py-3 px-4 fw-bold bg-light" data-bs-dismiss="modal" style="border-width:2px; border-color:#294287; color:#294287; width:100%;">Kembali</button>
                    </div>
                </div>
            </div>
            <!-- <div class="modal-body text-center">
                {{-- Tempat Foto Bukti --}}
                <img src="{{ $completionProof->photo_url ?? 'tidak ada bukti foto dari pekerja.' }}"
                    alt="Bukti Foto"
                    class="img-fluid mb-3 rounded">

                {{-- Note dari pekerja --}}
                <p>{{ $completionProof->note ?? 'Tidak ada catatan dari pekerja.' }}</p>
            </div> -->
        </div>
    </div>
</div>
</div>

<div class="modal fade" id="cancelWorkModal" tabindex="-1" aria-labelledby="cancelWorkModalLabel" aria-hidden="true">
    <div class="modal-dialog p-5">
        <div class="modal-content p-2 d-flex justify-content-center">
            <div class="d-flex flex-fill justify-content-center">
                <svg width="42" height="42" viewBox="0 0 42 42" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M0.6875 21C0.6875 9.78125 9.78125 0.6875 21 0.6875C32.2188 0.6875 41.3125 9.78125 41.3125 21C41.3125 32.2188 32.2188 41.3125 21 41.3125C9.78125 41.3125 0.6875 32.2188 0.6875 21ZM21 13.1875C21.4144 13.1875 21.8118 13.3521 22.1049 13.6451C22.3979 13.9382 22.5625 14.3356 22.5625 14.75V22.5625C22.5625 22.9769 22.3979 23.3743 22.1049 23.6674C21.8118 23.9604 21.4144 24.125 21 24.125C20.5856 24.125 20.1882 23.9604 19.8951 23.6674C19.6021 23.3743 19.4375 22.9769 19.4375 22.5625V14.75C19.4375 14.3356 19.6021 13.9382 19.8951 13.6451C20.1882 13.3521 20.5856 13.1875 21 13.1875ZM21 30.375C21.4144 30.375 21.8118 30.2104 22.1049 29.9174C22.3979 29.6243 22.5625 29.2269 22.5625 28.8125C22.5625 28.3981 22.3979 28.0007 22.1049 27.7076C21.8118 27.4146 21.4144 27.25 21 27.25C20.5856 27.25 20.1882 27.4146 19.8951 27.7076C19.6021 28.0007 19.4375 28.3981 19.4375 28.8125C19.4375 29.2269 19.6021 29.6243 19.8951 29.9174C20.1882 30.2104 20.5856 30.375 21 30.375Z" fill="#B02A37" />
                </svg>
                <h2 class="text-danger mx-4 fw-bold">Batalkan Pekerjaan?</h2>
                <svg width="42" height="42" viewBox="0 0 42 42" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M0.6875 21C0.6875 9.78125 9.78125 0.6875 21 0.6875C32.2188 0.6875 41.3125 9.78125 41.3125 21C41.3125 32.2188 32.2188 41.3125 21 41.3125C9.78125 41.3125 0.6875 32.2188 0.6875 21ZM21 13.1875C21.4144 13.1875 21.8118 13.3521 22.1049 13.6451C22.3979 13.9382 22.5625 14.3356 22.5625 14.75V22.5625C22.5625 22.9769 22.3979 23.3743 22.1049 23.6674C21.8118 23.9604 21.4144 24.125 21 24.125C20.5856 24.125 20.1882 23.9604 19.8951 23.6674C19.6021 23.3743 19.4375 22.9769 19.4375 22.5625V14.75C19.4375 14.3356 19.6021 13.9382 19.8951 13.6451C20.1882 13.3521 20.5856 13.1875 21 13.1875ZM21 30.375C21.4144 30.375 21.8118 30.2104 22.1049 29.9174C22.3979 29.6243 22.5625 29.2269 22.5625 28.8125C22.5625 28.3981 22.3979 28.0007 22.1049 27.7076C21.8118 27.4146 21.4144 27.25 21 27.25C20.5856 27.25 20.1882 27.4146 19.8951 27.7076C19.6021 28.0007 19.4375 28.3981 19.4375 28.8125C19.4375 29.2269 19.6021 29.6243 19.8951 29.9174C20.1882 30.2104 20.5856 30.375 21 30.375Z" fill="#B02A37" />
                </svg>
            </div>
            <div class="d-flex flex-column flex-fill justify-content-center text-center my-4" style="font-size: 16px;">
                <div>Apakah kamu yakin ingin membatalkan pekerjaan ini?</div>
                <div>Tindakan ini bisa mempengaruhi reputasimu di platform KerjaIn</div>
            </div>
            <div class="d-flex flex-fill mt-4 justify-content-between">
                <form action="{{ route('transaction.cancel', $transaction->id) }}" method="POST" class="flex-fill d-flex justify-content-between">
                    @csrf
                    <button type="button" class="btn flex-fill p-3 px-5 me-3" style="color:#294287; border-color:#294287; border-width: 2px;" data-bs-dismiss="modal">Lanjut Kerja</button>
                    <button type="submit" class="btn btn-danger flex-fill p-3 px-5">Ya, Tetap Batalin</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="reportWorkModal" tabindex="-1" aria-labelledby="reportWorkModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg py-3 px-5" style="max-width: 1000px; width: 100%;">
        <div class="modal-content p-2 d-flex justify-content-center">
            <div class="modal-header d-flex flex-end">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="title d-flex justify-content-center">
                <h3 class="fw-bold">Laporan</h3>
            </div>
            <hr class="mt-0" style="width: 50%; background-color:#D3FA0D; height:4px; align-self: center; border-color:#D3FA0D;">
            <div class="d-flex flex-fill">
                <div class="text" style="width:25%;">
                    <p class="m-0 p-0 text-black-50 fw-semibold">Judul Pesanan</p>
                    <p class="fw-medium">{{ $request->title }}</p>
                </div>
                <div class="text" style="width:25%;">
                    <p class="m-0 p-0 text-black-50 fw-semibold">Nomor Pesanan</p>
                    <p class="fw-medium">{{ $orderNumber }}</p>
                </div>
                <div class="text" style="width:25%;">
                    <p class="m-0 p-0 text-black-50 fw-semibold">Nama Pekerja</p>
                    <p class="fw-medium">{{ $worker->first_name }} {{ $worker->last_name }}</p>
                </div>
                <div class="text" style="width:25%;">
                    <p class="m-0 p-0 text-black-50 fw-semibold">Lokasi</p>
                    <p class="fw-medium">{{ $request->location }}</p>
                </div>
            </div>
            <div class="d-flex flex-fill">
                <div class="text" style="width:25%;">
                    <p class="m-0 p-0 text-black-50 fw-semibold">Tanggal Pemesanan</p>
                    <p class="fw-medium">{{ $transaction->created_at->format('d M Y') }}</p>
                </div>
                <div class="text" style="width:25%;">
                    <p class="m-0 p-0 text-black-50 fw-semibold">Tanggal Selesai</p>
                    <p class="fw-medium">{{ $transaction->updated_at->format('d M Y') }}</p>
                </div>
                <div class="text" style="width:25%;">
                    <p class="m-0 p-0 text-black-50 fw-semibold">Waktu Mulai Kerja</p>
                    <p class="fw-medium">{{ $start_work }}</p>
                </div>
                <div class="text" style="width:25%;">
                    <p class="m-0 p-0 text-black-50 fw-semibold">Waktu Selesai Kerja</p>
                    <p class="fw-medium">{{ $finish_work }}</p>
                </div>
            </div>
            <div class="text d-flex justify-content-between align-items-center" style="width:50%;">
                <p class="p-0 m-0 text-black-50 fw-semibold fs-6">Total</p>
                <p class="p-0 m-0 fw-medium fs-5">Rp {{ number_format($request->price, 2, ',', '.') }}</p>
            </div>
            <!-- Upload Gambar -->
            <div class="mt-4">
                <label class="form-label fw-semibold">Upload Bukti (gambar):</label>
                <div class="d-flex flex-wrap gap-3 align-items-start" id="imagePreviewContainer">
                    <!-- Tombol Tambah -->
                    <div class="pb-2" onclick="document.getElementById('reportImageInput').click()" style="width: 80px; height: 80px; border: 2px dashed #ccc; display: flex; align-items: center; justify-content: center; cursor: pointer; background-color:#f7f7ff; border-color:#309FFF;">
                        <span class="text-center" style="font-size: 48px; color:#309FFF;">+</span>
                    </div>
                </div>
                <input type="file" class="d-none" id="reportImageInput" name="images[]" accept="image/*" multiple>
            </div>

            <!-- Keluhan -->
            <div class="mt-4">
                <label for="reportNote" class="form-label fw-semibold">Keluh Kesah Anda</label>
                <textarea name="note" id="reportNote" class="form-control" rows="4" placeholder="Ceritakan masalah yang Anda alami..." style="background-color: #f7f7ff !important;"></textarea>
            </div>

            <!-- Submit -->
            <div class="d-flex justify-content-end mt-4">
                <button type="submit" class="btn btn-danger px-4 py-2">Kirim Laporan</button>
            </div>

        </div>
    </div>
</div>

<script>
    const imageInput = document.getElementById('reportImageInput');
    const previewContainer = document.getElementById('imagePreviewContainer');

    imageInput.addEventListener('change', function() {
        const files = Array.from(imageInput.files);

        files.forEach(file => {
            if (!file.type.startsWith('image/')) return;

            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = "rounded border img-thumbnail";
                img.style.width = "120px";
                img.style.height = "120px";
                img.style.objectFit = "cover";
                previewContainer.appendChild(img);
            };
            reader.readAsDataURL(file);
        });

        // Reset input so same image can be re-uploaded
        imageInput.value = '';
    });
</script>


@endsection