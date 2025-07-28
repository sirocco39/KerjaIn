@extends('master.master-job-taker')

@section('content')
    @php
        $start_time_detail = $request->start_time->format('d M Y, H:i');
        $end_time_detail = $request->end_time->format('d M Y, H:i');
        $start_time = $request->start_time;
        $end_time = $request->end_time;
        $date = date('d M Y', strtotime($start_time));
        $time = date('H:i', strtotime($start_time));
        $start = new DateTime($start_time);
        $end = new DateTime($end_time);
        $interval = $start->diff($end);

        $days = $interval->d;
        $hours = $interval->h;
        $minutes = $interval->i;

        $total_hours = $days * 24 + $hours;

        // Format durasi baru
        $duration = $total_hours . ' jam ' . $minutes . ' menit';

        $amount = $request->final_price;
        $formatted = 'Rp ' . number_format($amount, 2, ',', '.');

        $alamat = $request->location; // Menggunakan $request->location dari model
        $alamatEncoded = urlencode($alamat);
        $mapsLink = "https://www.google.com/maps/search/?api=1&query={$alamatEncoded}";

        $created_at = $worker->created_at;
        $year = date('F Y', strtotime($created_at));
        $start_work = $transaction->start_work ? date('d M Y H:i', strtotime($transaction->start_work)) : null;
        $finish_work = $transaction->finish_work ? date('d M Y H:i', strtotime($transaction->finish_work)) : null;

    @endphp
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="container-fluid pembatas-x mb-5">
        <div class="row mb-3 p-1">
            <div class="col-12 mb-2 mt-5">
                <h2 style="font-weight: 800;">Kerjaan Kamu</h2>
            </div>
            <div class="col-12 contain bg-light mt-2 px-4 py-3 rounded-4 d-flex align-items-center"
                style="border: 1px solid #cacadd; ">
                @if ($transaction->status == 'submitted')
                    <div class="badge px-4 py-3 rounded-pill bg-primary text-light fs-6" style="background-color:#294287;">
                        Ditinjau</div>
                @elseif($transaction->status == 'cancelled')
                    <div class="badge px-4 py-3 rounded-pill bg-warning text-light fs-6" style="background-color:crimson;">
                        Dibatalin</div>
                @elseif($transaction->status == 'accepted')
                    <div class="badge px-4 py-3 rounded-pill bg-warning text-light fs-6" style="background-color:#294287;">
                        Diterima</div>
                @elseif($transaction->status == 'in progress')
                    <div class="badge px-4 py-3 rounded-5 bg-info text-light fs-6" style="background-color:#309FFF;">
                        Dikerjain</div>
                @elseif($transaction->status == 'completed')
                    <div class="badge px-4 py-3 rounded-pill bg-success text-dark fs-6" style="background-color:#D3FA0D;">
                        Selesai</div>
                @endif
                <h3 class="d-inline mx-3 mt-1" style="color:#294287; font-weight: 800;">{{ $request->title }}</h3>
            </div>
        </div>
        <div class="one row">
            <div class="col-12 col-lg-3 d-flex flex-column order-0 order-lg-0">
                <div class="two row gx-3 gy-3">
                    <div class="col-12 col-md-6 col-lg-12 px-0 pe-md-2">
                        <div class="contain bg-light px-4 py-3 rounded-4 d-flex flex-column"
                            style="border: 1px solid #cacadd; height:100%;">
                            <div class="fw-bold text-start">Deskripsi</div>
                            <div class="text-justify" style="font-size: 12px;">{{ $request->description }}</div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-12 px-0 pe-lg-2">
                        <div class="contain bg-light px-4 py-3 rounded-4 d-flex flex-column align-items-center"
                            style="border: 1px solid #cacadd; height:100%;">
                            <div class="d-flex flex-fill justify-content-between" style="width: 100%;">
                                <div class="separate d-flex align-items-center">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        {{-- Path untuk badan kalender --}}
                                        <path
                                            d="M19.5 3.75H4.5C3.80964 3.75 3.14707 4.02656 2.65165 4.52198C2.15623 5.01739 1.875 5.67996 1.875 6.375V19.5C1.875 20.1904 2.15623 20.8529 2.65165 21.3483C3.14707 21.8437 3.80964 22.125 4.5 22.125H19.5C20.1904 22.125 20.8529 21.8437 21.3483 21.3483C21.8437 20.8529 22.125 20.1904 22.125 19.5V6.375C22.125 5.67996 21.8437 5.01739 21.3483 4.52198C20.8529 4.02656 20.1904 3.75 19.5 3.75ZM15.75 1.875V5.625M8.25 1.875V5.625M1.875 9.375H22.125"
                                            stroke="#133E87" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        {{-- Path untuk titik-titik di dalam kalender --}}
                                        <path
                                            d="M14.875 14.875C14.875 15.1071 14.7828 15.3296 14.6187 15.4937C14.4546 15.6578 14.2321 15.75 14 15.75C13.7679 15.75 13.5454 15.6578 13.3813 15.4937C13.2172 15.3296 13.125 15.1071 13.125 14.875C13.125 14.6429 13.2172 14.4204 13.3813 14.2563C13.5454 14.0922 13.7679 14 14 14C14.2321 14 14.4546 14.0922 14.6187 14.2563C14.7828 14.4204 14.875 14.6429 14.875 14.875ZM8.75 18.375C8.98206 18.375 9.20462 18.2828 9.36872 18.1187C9.53281 17.9546 9.625 17.7321 9.625 17.5C9.625 17.2679 9.53281 17.0454 9.36872 16.8813C9.20462 16.7172 8.98206 16.625 8.75 16.625C8.51794 16.625 8.29538 16.7172 8.13128 16.8813C7.96719 17.0454 7.875 17.2679 7.875 17.5C7.875 17.7321 7.96719 17.9546 8.13128 18.1187C8.29538 18.2828 8.51794 18.375 8.75 18.375ZM9.625 20.125C9.625 20.3571 9.53281 20.5796 9.36872 20.7437C9.20462 20.9078 8.98206 21 8.75 21C8.51794 21 8.29538 20.9078 8.13128 20.7437C7.96719 20.5796 7.875 20.3571 7.875 20.125C7.875 19.8929 7.96719 19.6704 8.13128 19.5063C8.29538 19.3422 8.51794 19.25 8.75 19.25C8.98206 19.25 9.20462 19.3422 9.36872 19.5063C9.53281 19.6704 9.625 19.8929 9.625 20.125ZM11.375 18.375C11.6071 18.375 11.8296 18.2828 11.9937 18.1187C12.1578 17.9546 12.25 17.7321 12.25 17.5C12.25 17.2679 12.1578 17.0454 11.9937 16.8813C11.8296 16.7172 11.6071 16.625 11.375 16.625C11.1429 16.625 10.9204 16.7172 10.7563 16.8813C10.5922 17.0454 10.5 17.2679 10.5 17.5C10.5 17.7321 10.5922 17.9546 10.7563 18.1187C10.9204 18.2828 11.1429 18.375 11.375 18.375ZM12.25 20.125C12.25 20.3571 12.1578 20.5796 11.9937 20.7437C11.8296 20.9078 11.6071 21 11.375 21C11.1429 21 10.9204 20.9078 10.7563 20.7437C10.5922 20.5796 10.5 20.3571 10.5 20.125C10.5 19.8929 10.5922 19.6704 10.7563 19.5063C10.9204 19.3422 11.1429 19.25 11.375 19.25C11.6071 19.25 11.8296 19.3422 11.9937 19.5063C12.1578 19.6704 12.25 19.8929 12.25 20.125ZM14 18.375C14.2321 18.375 14.4546 18.2828 14.6187 18.1187C14.7828 17.9546 14.875 17.7321 14.875 17.5C14.875 17.2679 14.7828 17.0454 14.6187 16.8813C14.4546 16.7172 14.2321 16.625 14 16.625C13.7679 16.625 13.5454 16.7172 13.3813 16.8813C13.2172 17.0454 13.125 17.2679 13.125 17.5C13.125 17.7321 13.2172 17.9546 13.3813 18.1187C13.5454 18.2828 13.7679 18.375 14 18.375ZM14.875 20.125C14.875 20.3571 14.7828 20.5796 14.6187 20.7437C14.4546 20.9078 14.2321 21 14 21C13.7679 21 13.5454 20.9078 13.3813 20.7437C13.2172 20.5796 13.125 20.3571 13.125 20.125C13.125 19.8929 13.2172 19.6704 13.3813 19.5063C13.5454 19.3422 13.7679 19.25 14 19.25C14.2321 19.25 14.4546 19.3422 14.6187 19.5063C14.7828 19.6704 14.875 19.8929 14.875 20.125ZM16.625 18.375C16.8571 18.375 17.0796 18.2828 17.2437 18.1187C17.4078 17.9546 17.5 17.7321 17.5 17.5C17.5 17.2679 17.4078 17.0454 17.2437 16.8813C17.0796 16.7172 16.8571 16.625 16.625 16.625C16.3929 16.625 16.1704 16.7172 16.0063 16.8813C15.8422 17.0454 15.75 17.2679 15.75 17.5C15.75 17.7321 15.8422 17.9546 16.0063 18.1187C16.1704 18.2828 16.3929 18.375 16.625 18.375ZM17.5 20.125C17.5 20.3571 17.4078 20.5796 17.2437 20.7437C17.0796 20.9078 16.8571 21 16.625 21C16.3929 21 16.1704 20.9078 16.0063 20.7437C15.8422 20.5796 15.75 20.3571 15.75 20.125C15.75 19.8929 15.8422 19.6704 16.0063 19.5063C16.1704 19.3422 16.3929 19.25 16.625 19.25C16.8571 19.25 17.0796 19.3422 17.2437 19.5063C17.4078 19.6704 17.5 19.8929 17.5 20.125ZM19.25 18.375C19.4821 18.375 19.7046 18.2828 19.8687 18.1187C20.0328 17.9546 20.125 17.7321 20.125 17.5C20.125 17.2679 20.0328 17.0454 19.8687 16.8813C19.7046 16.7172 19.4821 16.625 19.25 16.625C19.0179 16.625 18.7954 16.7172 18.6313 16.8813C18.4672 17.0454 18.375 17.2679 18.375 17.5C18.375 17.7321 18.4672 17.9546 18.6313 18.1187C18.7954 18.2828 19.0179 18.375 19.25 18.375ZM17.5 14.875C17.5 15.1071 17.4078 15.3296 17.2437 15.4937C17.0796 15.6578 16.8571 15.75 16.625 15.75C16.3929 15.75 16.1704 15.6578 16.0063 15.4937C15.8422 15.3296 15.75 15.1071 15.75 14.875C15.75 14.6429 15.8422 14.4204 16.0063 14.2563C16.1704 14.0922 16.3929 14 16.625 14C16.8571 14 17.0796 14.0922 17.2437 14.2563C17.4078 14.4204 17.5 14.6429 17.5 14.875ZM19.25 15.75C19.4821 15.75 19.7046 15.6578 19.8687 15.4937C20.0328 15.3296 20.125 15.1071 20.125 14.875C20.125 14.6429 20.0328 14.4204 19.8687 14.2563C19.7046 14.0922 19.4821 14 19.25 14C19.0179 14 18.7954 14.0922 18.6313 14.2563C18.4672 14.4204 18.375 14.6429 18.375 14.875C18.375 15.1071 18.4672 15.3296 18.6313 15.4937C18.7954 15.6578 19.0179 15.75 19.25 15.75Z"
                                            fill="#133E87" />
                                    </svg>
                                    <div class="p-2">Mulai</div>
                                </div>
                                <div class="py-2 fw-bold text-end">{{ $start_time_detail }}</div>
                            </div>
                            <div class="d-flex flex-fill justify-content-between" style="width: 100%;">
                                <div class="separate d-flex align-items-center">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        {{-- Path untuk badan kalender --}}
                                        <path
                                            d="M19.5 3.75H4.5C3.80964 3.75 3.14707 4.02656 2.65165 4.52198C2.15623 5.01739 1.875 5.67996 1.875 6.375V19.5C1.875 20.1904 2.15623 20.8529 2.65165 21.3483C3.14707 21.8437 3.80964 22.125 4.5 22.125H19.5C20.1904 22.125 20.8529 21.8437 21.3483 21.3483C21.8437 20.8529 22.125 20.1904 22.125 19.5V6.375C22.125 5.67996 21.8437 5.01739 21.3483 4.52198C20.8529 4.02656 20.1904 3.75 19.5 3.75ZM15.75 1.875V5.625M8.25 1.875V5.625M1.875 9.375H22.125"
                                            stroke="#133E87" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        {{-- Path untuk titik-titik di dalam kalender --}}
                                        <path
                                            d="M14.875 14.875C14.875 15.1071 14.7828 15.3296 14.6187 15.4937C14.4546 15.6578 14.2321 15.75 14 15.75C13.7679 15.75 13.5454 15.6578 13.3813 15.4937C13.2172 15.3296 13.125 15.1071 13.125 14.875C13.125 14.6429 13.2172 14.4204 13.3813 14.2563C13.5454 14.0922 13.7679 14 14 14C14.2321 14 14.4546 14.0922 14.6187 14.2563C14.7828 14.4204 14.875 14.6429 14.875 14.875ZM8.75 18.375C8.98206 18.375 9.20462 18.2828 9.36872 18.1187C9.53281 17.9546 9.625 17.7321 9.625 17.5C9.625 17.2679 9.53281 17.0454 9.36872 16.8813C9.20462 16.7172 8.98206 16.625 8.75 16.625C8.51794 16.625 8.29538 16.7172 8.13128 16.8813C7.96719 17.0454 7.875 17.2679 7.875 17.5C7.875 17.7321 7.96719 17.9546 8.13128 18.1187C8.29538 18.2828 8.51794 18.375 8.75 18.375ZM9.625 20.125C9.625 20.3571 9.53281 20.5796 9.36872 20.7437C9.20462 20.9078 8.98206 21 8.75 21C8.51794 21 8.29538 20.9078 8.13128 20.7437C7.96719 20.5796 7.875 20.3571 7.875 20.125C7.875 19.8929 7.96719 19.6704 8.13128 19.5063C8.29538 19.3422 8.51794 19.25 8.75 19.25C8.98206 19.25 9.20462 19.3422 9.36872 19.5063C9.53281 19.6704 9.625 19.8929 9.625 20.125ZM11.375 18.375C11.6071 18.375 11.8296 18.2828 11.9937 18.1187C12.1578 17.9546 12.25 17.7321 12.25 17.5C12.25 17.2679 12.1578 17.0454 11.9937 16.8813C11.8296 16.7172 11.6071 16.625 11.375 16.625C11.1429 16.625 10.9204 16.7172 10.7563 16.8813C10.5922 17.0454 10.5 17.2679 10.5 17.5C10.5 17.7321 10.5922 17.9546 10.7563 18.1187C10.9204 18.2828 11.1429 18.375 11.375 18.375ZM12.25 20.125C12.25 20.3571 12.1578 20.5796 11.9937 20.7437C11.8296 20.9078 11.6071 21 11.375 21C11.1429 21 10.9204 20.9078 10.7563 20.7437C10.5922 20.5796 10.5 20.3571 10.5 20.125C10.5 19.8929 10.5922 19.6704 10.7563 19.5063C10.9204 19.3422 11.1429 19.25 11.375 19.25C11.6071 19.25 11.8296 19.3422 11.9937 19.5063C12.1578 19.6704 12.25 19.8929 12.25 20.125ZM14 18.375C14.2321 18.375 14.4546 18.2828 14.6187 18.1187C14.7828 17.9546 14.875 17.7321 14.875 17.5C14.875 17.2679 14.7828 17.0454 14.6187 16.8813C14.4546 16.7172 14.2321 16.625 14 16.625C13.7679 16.625 13.5454 16.7172 13.3813 16.8813C13.2172 17.0454 13.125 17.2679 13.125 17.5C13.125 17.7321 13.2172 17.9546 13.3813 18.1187C13.5454 18.2828 13.7679 18.375 14 18.375ZM14.875 20.125C14.875 20.3571 14.7828 20.5796 14.6187 20.7437C14.4546 20.9078 14.2321 21 14 21C13.7679 21 13.5454 20.9078 13.3813 20.7437C13.2172 20.5796 13.125 20.3571 13.125 20.125C13.125 19.8929 13.2172 19.6704 13.3813 19.5063C13.5454 19.3422 13.7679 19.25 14 19.25C14.2321 19.25 14.4546 19.3422 14.6187 19.5063C14.7828 19.6704 14.875 19.8929 14.875 20.125ZM16.625 18.375C16.8571 18.375 17.0796 18.2828 17.2437 18.1187C17.4078 17.9546 17.5 17.7321 17.5 17.5C17.5 17.2679 17.4078 17.0454 17.2437 16.8813C17.0796 16.7172 16.8571 16.625 16.625 16.625C16.3929 16.625 16.1704 16.7172 16.0063 16.8813C15.8422 17.0454 15.75 17.2679 15.75 17.5C15.75 17.7321 15.8422 17.9546 16.0063 18.1187C16.1704 18.2828 16.3929 18.375 16.625 18.375ZM17.5 20.125C17.5 20.3571 17.4078 20.5796 17.2437 20.7437C17.0796 20.9078 16.8571 21 16.625 21C16.3929 21 16.1704 20.9078 16.0063 20.7437C15.8422 20.5796 15.75 20.3571 15.75 20.125C15.75 19.8929 15.8422 19.6704 16.0063 19.5063C16.1704 19.3422 16.3929 19.25 16.625 19.25C16.8571 19.25 17.0796 19.3422 17.2437 19.5063C17.4078 19.6704 17.5 19.8929 17.5 20.125ZM19.25 18.375C19.4821 18.375 19.7046 18.2828 19.8687 18.1187C20.0328 17.9546 20.125 17.7321 20.125 17.5C20.125 17.2679 20.0328 17.0454 19.8687 16.8813C19.7046 16.7172 19.4821 16.625 19.25 16.625C19.0179 16.625 18.7954 16.7172 18.6313 16.8813C18.4672 17.0454 18.375 17.2679 18.375 17.5C18.375 17.7321 18.4672 17.9546 18.6313 18.1187C18.7954 18.2828 19.0179 18.375 19.25 18.375ZM17.5 14.875C17.5 15.1071 17.4078 15.3296 17.2437 15.4937C17.0796 15.6578 16.8571 15.75 16.625 15.75C16.3929 15.75 16.1704 15.6578 16.0063 15.4937C15.8422 15.3296 15.75 15.1071 15.75 14.875C15.75 14.6429 15.8422 14.4204 16.0063 14.2563C16.1704 14.0922 16.3929 14 16.625 14C16.8571 14 17.0796 14.0922 17.2437 14.2563C17.4078 14.4204 17.5 14.6429 17.5 14.875ZM19.25 15.75C19.4821 15.75 19.7046 15.6578 19.8687 15.4937C20.0328 15.3296 20.125 15.1071 20.125 14.875C20.125 14.6429 20.0328 14.4204 19.8687 14.2563C19.7046 14.0922 19.4821 14 19.25 14C19.0179 14 18.7954 14.0922 18.6313 14.2563C18.4672 14.4204 18.375 14.6429 18.375 14.875C18.375 15.1071 18.4672 15.3296 18.6313 15.4937C18.7954 15.6578 19.0179 15.75 19.25 15.75Z"
                                            fill="#133E87" />
                                    </svg>

                                    <div class="p-2">Selesai</div>
                                </div>
                                <div class="py-2 fw-bold text-end">{{ $end_time_detail }}</div>
                            </div>
                            <div class="d-flex flex-fill justify-content-between" style="width: 100%;">
                                <div class="separate d-flex align-items-center">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M12 2.25C6.615 2.25 2.25 6.615 2.25 12C2.25 17.385 6.615 21.75 12 21.75C17.385 21.75 21.75 17.385 21.75 12C21.75 6.615 17.385 2.25 12 2.25ZM12.75 6C12.75 5.80109 12.671 5.61032 12.5303 5.46967C12.3897 5.32902 12.1989 5.25 12 5.25C11.8011 5.25 11.6103 5.32902 11.4697 5.46967C11.329 5.61032 11.25 5.80109 11.25 6V12C11.25 12.414 11.586 12.75 12 12.75H16.5C16.6989 12.75 16.8897 12.671 17.0303 12.5303C17.171 12.3897 17.25 12.1989 17.25 12C17.25 11.8011 17.171 11.6103 17.0303 11.4697C16.8897 11.329 16.6989 11.25 16.5 11.25H12.75V6Z"
                                            fill="#133E87" />
                                    </svg>

                                    <div class="p-2">Durasi</div>
                                </div>
                                <div class="py-2 fw-bold text-end">{{ $duration }}</div>
                            </div>
                            <div class="d-flex flex-fill justify-content-between" style="width: 100%;">
                                <div class="separate d-flex align-items-center">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M12 7.5C11.4033 7.5 10.831 7.73705 10.409 8.15901C9.98705 8.58097 9.75 9.15326 9.75 9.75C9.75 10.3467 9.98705 10.919 10.409 11.341C10.831 11.7629 11.4033 12 12 12C12.5967 12 13.169 11.7629 13.591 11.341C14.0129 10.919 14.25 10.3467 14.25 9.75C14.25 9.15326 14.0129 8.58097 13.591 8.15901C13.169 7.73705 12.5967 7.5 12 7.5Z"
                                            fill="#133E87" />
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M1.5 4.875C1.5 3.839 2.34 3 3.375 3H20.625C21.66 3 22.5 3.84 22.5 4.875V14.625C22.5 15.661 21.66 16.5 20.625 16.5H3.375C3.12877 16.5 2.88495 16.4515 2.65747 16.3573C2.42998 16.263 2.22328 16.1249 2.04917 15.9508C1.87506 15.7767 1.73695 15.57 1.64273 15.3425C1.5485 15.115 1.5 14.8712 1.5 14.625V4.875ZM8.25 9.75C8.25 8.75544 8.64509 7.80161 9.34835 7.09835C10.0516 6.39509 11.0054 6 12 6C12.9946 6 13.9484 6.39509 14.6517 7.09835C15.3549 7.80161 15.75 8.75544 15.75 9.75C15.75 10.7446 15.3549 11.6984 14.6517 12.4017C13.9484 13.1049 12.9946 13.5 12 13.5C11.0054 13.5 10.0516 13.1049 9.34835 12.4017C8.64509 11.6984 8.25 10.7446 8.25 9.75ZM18.75 9C18.5511 9 18.3603 9.07902 18.2197 9.21967C18.079 9.36032 18 9.55109 18 9.75V9.758C18 10.172 18.336 10.508 18.75 10.508H18.758C18.9569 10.508 19.1477 10.429 19.2883 10.2883C19.429 10.1477 19.508 9.95691 19.508 9.758V9.75C19.508 9.55109 19.429 9.36032 19.2883 9.21967C19.1477 9.07902 19.508 9 18.758 9H18.75ZM4.5 9.75C4.5 9.55109 4.57902 9.36032 4.71967 9.21967C4.86032 9.36032 5.05109 9.55109 5.25 9H5.258C5.45691 9 5.64768 9.07902 5.78833 9.21967C5.92898 9.36032 6.008 9.55109 6.008 9.75V9.758C6.008 9.95691 5.92898 10.1477 5.78833 10.2883C5.64768 10.429 5.45691 10.508 5.258 10.508H5.25C5.05109 10.508 4.86032 10.429 4.71967 10.2883C4.57902 10.1477 4.5 9.95691 4.5 9.758V9.75Z"
                                            fill="#133E87" />
                                        <path
                                            d="M2.25 18C2.05109 18 1.86032 18.079 1.71967 18.2197C1.57902 18.3603 1.5 18.5511 1.5 18.75C1.5 18.9489 1.57902 19.1397 1.71967 19.2803C1.86032 19.421 2.05109 19.5 2.25 19.5C7.65 19.5 12.88 20.222 17.85 21.575C19.04 21.899 20.25 21.017 20.25 19.755V18.75C20.25 18.5511 20.171 18.3603 20.0303 18.2197C19.8897 18.079 19.6989 18 19.5 18H2.25Z"
                                            fill="#133E87" />
                                    </svg>

                                    <div class="p-2">Upah</div>
                                </div>
                                <div class="py-2 fw-bold text-end">{{ $formatted }}</div>
                            </div>
                            <div class="d-flex flex-fill justify-content-between" style="width: 100%;">
                                <div class="separate d-flex align-items-center">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M11.54 22.351L11.61 22.391L11.638 22.407C11.749 22.467 11.8733 22.4985 11.9995 22.4985C12.1257 22.4985 12.25 22.467 12.361 22.407L12.389 22.392L12.46 22.351C12.8511 22.1191 13.2328 21.8716 13.604 21.609C14.5651 20.9305 15.463 20.1667 16.287 19.327C18.231 17.337 20.25 14.347 20.25 10.5C20.25 8.31196 19.3808 6.21354 17.8336 4.66637C16.2865 3.11919 14.188 2.25 12 2.25C9.81196 2.25 7.71354 3.11919 6.16637 4.66637C4.61919 6.21354 3.75 8.31196 3.75 10.5C3.75 14.346 5.77 17.337 7.713 19.327C8.53664 20.1667 9.43427 20.9304 10.395 21.609C10.7666 21.8716 11.1485 22.1191 11.54 22.351ZM12 13.5C12.7956 13.5 13.5587 13.1839 14.1213 12.6213C14.6839 12.0587 15 11.2956 15 10.5C15 9.70435 14.6839 8.94129 14.1213 8.37868C13.5587 7.81607 12.7956 7.5 12 7.5C11.2044 7.5 10.4413 7.81607 9.87868 8.37868C9.31607 8.94129 9 9.70435 9 10.5C9 11.2956 9.31607 12.0587 9.87868 12.6213C10.4413 13.1839 11.2044 13.5 12 13.5Z"
                                            fill="#133E87" />
                                    </svg>

                                    <div class="p-2">Lokasi</div>
                                </div>
                                <div class="py-2 fw-bold text-end">
                                    <a href={{ $mapsLink }} target="_blank" class="text-end"
                                        style="text-decoration: none; color: #007BFF;">
                                        {{ $request->location }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-12 px-0 pe-md-2">
                        @if ($transaction->status !== 'cancelled')
                            <div class="contain bg-light px-4 py-3 rounded-4 d-flex flex-fill align-items-center justify-content-between"
                                style="border: 1px solid #cacadd; max-height: 150px; height:100%;">
                                <div class="info d-flex flex-column">
                                    @if ($transaction->status == 'accepted')
                                        <div class="d-flex">
                                            <div class="svg align-items-center d-flex py-2 px-1 ms-2">
                                                <svg width="16" height="111" viewBox="0 0 16 111" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <circle cx="8" cy="8" r="8" fill="#294287" />
                                                    <circle cx="8" cy="103" r="8" fill="#D9D9D9" />
                                                    <circle cx="8" cy="56" r="8" fill="#D9D9D9" />
                                                    <path d="M7.99989 16L8 95" stroke="#D9D9D9" stroke-width="2" />
                                                </svg>
                                            </div>
                                            <div class="column">
                                                <div class="px-4 rounded-pill bg-warning text-dark fs-6 mt-1"
                                                    style="">Diterima</div>
                                                <div class="px-4 rounded-5 bg-info text-dark fs-6"
                                                    style="margin-top:25px; margin-bottom: 25px;">Dikerjain</div>
                                                <div class="px-4 rounded-pill text-dark fs-6">Selesai</div>
                                            </div>
                                        </div>
                                    @elseif($transaction->status == 'in progress')
                                        <div class="d-flex">
                                            <div class="svg align-items-center d-flex py-2 px-1 ms-2">
                                                <svg width="16" height="111" viewBox="0 0 16 111" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M8 64L8 95" stroke="#D9D9D9" stroke-width="2" />
                                                    <path d="M8 16L8 48" stroke="url(#paint0_linear_0_1)"
                                                        stroke-width="2" />
                                                    <circle cx="8" cy="8" r="8" fill="#294287" />
                                                    <circle cx="8" cy="103" r="8" fill="#D9D9D9" />
                                                    <circle cx="8" cy="56" r="8" fill="#309FFF" />
                                                    <defs>
                                                        <linearGradient id="paint0_linear_0_1" x1="8"
                                                            y1="48" x2="8" y2="16"
                                                            gradientUnits="userSpaceOnUse">
                                                            <stop stop-color="#309FFF" />
                                                            <stop offset="1" stop-color="#294287" />
                                                        </linearGradient>
                                                    </defs>
                                                </svg>
                                            </div>
                                            <div class="column">
                                                <div class="px-4 rounded-pill bg-warning text-dark fs-6 mt-1"
                                                    style="">Diterima</div>
                                                <div class="px-4 rounded-5 bg-info text-dark fs-6"
                                                    style="margin-top:25px; margin-bottom: 25px;">Dikerjain</div>
                                                <div class="px-4 rounded-pill text-dark fs-6">Selesai</div>
                                            </div>
                                        </div>
                                    @elseif($transaction->status == 'completed')
                                        <div class="d-flex">
                                            <div class="svg align-items-center d-flex py-2 px-1 ms-2">
                                                <svg width="16" height="111" viewBox="0 0 16 111" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M8 64L8 95" stroke="url(#paint0_linear_0_1)"
                                                        stroke-width="2" />
                                                    <path d="M8 16L8 48" stroke="url(#paint1_linear_0_1)"
                                                        stroke-width="2" />
                                                    <circle cx="8" cy="8" r="8" fill="#294287" />
                                                    <circle cx="8" cy="103" r="8" fill="#D3FA0D" />
                                                    <circle cx="8" cy="56" r="8" fill="#309FFF" />
                                                    <defs>
                                                        <linearGradient id="paint0_linear_0_1" x1="8"
                                                            y1="95" x2="8" y2="64"
                                                            gradientUnits="userSpaceOnUse">
                                                            <stop stop-color="#D3FA0D" />
                                                            <stop offset="1" stop-color="#309FFF" />
                                                        </linearGradient>
                                                        <linearGradient id="paint1_linear_0_1" x1="8"
                                                            y1="48" x2="8" y2="16"
                                                            gradientUnits="userSpaceOnUse">
                                                            <stop stop-color="#309FFF" />
                                                            <stop offset="1" stop-color="#294287" />
                                                        </linearGradient>
                                                    </defs>
                                                </svg>
                                            </div>
                                            <div class="column">
                                                <div class="px-4 rounded-pill bg-warning text-dark fs-6 mt-1"
                                                    style="">Diterima</div>
                                                <div class="px-4 rounded-5 bg-info text-dark fs-6"
                                                    style="margin-top:25px; margin-bottom: 25px;">Dikerjain</div>
                                                <div class="px-4 rounded-pill text-dark fs-6">Selesai</div>
                                            </div>
                                        </div>
                                    @elseif($transaction->status == 'submitted')
                                        <div class="d-flex">
                                            <div class="svg align-items-center d-flex py-2 px-1 ms-2">
                                                <svg width="16" height="111" viewBox="0 0 16 111" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M8 64L8 95" stroke="url(#paint0_linear_1857_7195)"
                                                        stroke-width="2" />
                                                    <path d="M8 16L8 48" stroke="url(#paint1_linear_1857_7195)"
                                                        stroke-width="2" />
                                                    <circle cx="8" cy="8" r="8" fill="#294287" />
                                                    <circle cx="8" cy="103" r="8" fill="#294287" />
                                                    <circle cx="8" cy="56" r="8" fill="#309FFF" />
                                                    <defs>
                                                        <linearGradient id="paint0_linear_1857_7195" x1="8"
                                                            y1="95" x2="8" y2="64"
                                                            gradientUnits="userSpaceOnUse">
                                                            <stop stop-color="#294287" />
                                                            <stop offset="1" stop-color="#309FFF" />
                                                        </linearGradient>
                                                        <linearGradient id="paint1_linear_1857_7195" x1="8"
                                                            y1="48" x2="8" y2="16"
                                                            gradientUnits="userSpaceOnUse">
                                                            <stop stop-color="#309FFF" />
                                                            <stop offset="1" stop-color="#294287" />
                                                        </linearGradient>
                                                    </defs>
                                                </svg>

                                            </div>
                                            <div class="column">
                                                <div class="px-4 rounded-pill bg-warning text-dark fs-6 mt-1"
                                                    style="">Diterima</div>
                                                <div class="px-4 rounded-5 bg-info text-dark fs-6"
                                                    style="margin-top:25px; margin-bottom: 25px;">Dikerjain</div>
                                                <div class="px-4 rounded-pill text-dark fs-6">Ditinjau</div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="col-12 col-md-6 col-lg-12 px-0 pe-lg-2">
                        @if ($transaction->status !== 'cancelled')
                            <div class="contain bg-light px-4 py-3 rounded-4 d-flex flex-column align-items-center justify-content-center"
                                style="border: 1px solid #cacadd; height:100%;">

                                @if ($transaction->status === 'accepted')
                                    {{-- Tombol Mulai Kerja --}}
                                    <form id="start-work-form" action="{{ route('worker.startWork', $transaction->id) }}"
                                        method="POST">
                                        @csrf
                                        <button type="submit" id="start-work-button"
                                            class="btn px-4 py-2 rounded-5 text-light fs-4 fw-bold"
                                            style="background-color:#294287;">
                                            Mulai Kerja
                                        </button>
                                    </form>
                                    <button class="btn px-4 py-2 rounded-5 d-inline fw-semibold text-danger fs-5"
                                        data-bs-toggle="modal" data-bs-target="#cancelWorkModal">Batalkan Kerja
                                    </button>
                                @elseif($transaction->status === 'completed')
                                    {{-- Tombol Sudah Dikerjakan --}}
                                    <a class="btn btn-success px-4 py-2 rounded-pill fs-4 fw-bold"
                                        style="width:88%;cursor: not-allowed;pointer-events: none;">
                                        Selesai
                                    </a>
                                @elseif($transaction->status === 'submitted')
                                    <a class="text-decoration-none px-4 py-2 rounded-5 text-light fs-4 fw-bold"
                                        style="background-color:#294287;">
                                        Ditinjau
                                    </a>
                                    <button class="btn px-4 py-2 rounded-5 d-inline fw-semibold text-info fs-5"
                                        data-bs-toggle="modal" data-bs-target="#completionModal">Berikan Penilaian
                                    </button>
                                @elseif($transaction->status === 'in progress')
                                    {{-- Tombol untuk buka modal --}}
                                    <button class="btn px-4 py-2 rounded-5 text-light fs-4 fw-bold"
                                        style="background-color:#309FFF;" data-bs-toggle="modal"
                                        data-bs-target="#completionProofModal">
                                        Selesai Kerja
                                    </button>
                                    <div class="px-4 py-2 rounded-5 d-inline fw-semibold text-black-50 fs-5">Batalkan Kerja
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @livewire('job-taker.chat-work', ['selectedRoomId' => $room->id])
        </div>
    </div>
    <div class="modal fade" id="completionModal" tabindex="-1" aria-labelledby="completionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" style="max-width: 1000px; width: 100%; margin-top:5vh;">
            {{-- Form for review submission will be dynamically handled by JS --}}
            <form id="reviewForm" method="POST">
                @csrf
                <div class="modal-content p-3">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold fs-3" id="completionModalLabel">
                            Detail Penyelesaian</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body overflow-auto overflow-lg-visible" style="max-height: 90vh;">
                        <div class="d-flex flex-column flex-lg-row gap-3">
                            <div class="d-flex flex-column flex-grow-1">
                                <div class="d-flex flex-fill">
                                    <div class="text flex-fill" style="width:50%;">
                                        <p class="m-0 p-0 text-black-50 fw-semibold">Judul
                                            Pesanan</p>
                                        <p class="fw-medium" id="modalRequestTitle"></p>
                                    </div>
                                    <div class="text" style="width:50%;">
                                        <p class="m-0 p-0 text-black-50 fw-semibold">Nomor
                                            Pesanan</p>
                                        <p class="fw-medium" id="modalOrderNumber"></p>
                                    </div>
                                </div>
                                <div class="d-flex">
                                    <div class="text" style="width:50%;">
                                        <p class="m-0 p-0 text-black-50 fw-semibold">Nama
                                            Klien</p>
                                        <p class="fw-medium" id="modalRequesterName"></p>
                                    </div>
                                    <div class="text" style="width:50%;">
                                        <p class="m-0 p-0 text-black-50 fw-semibold">Lokasi
                                        </p>
                                        <p class="fw-medium" id="modalRequestLocation"></p>
                                    </div>
                                </div>
                                <div class="d-flex flex-fill">
                                    <div class="text" style="width:50%;">
                                        <p class="m-0 p-0 text-black-50 fw-semibold">
                                            Tanggal Pemesanan</p>
                                        <p class="fw-medium" id="modalTransactionCreatedAt"></p>
                                    </div>
                                    <div class="text" style="width:50%;">
                                        <p class="m-0 p-0 text-black-50 fw-semibold">
                                            Tanggal Selesai</p>
                                        <p class="fw-medium" id="modalTransactionUpdatedAt"></p>
                                    </div>
                                </div>
                                <div class="d-flex">
                                    <div class="text" style="width:50%;">
                                        <p class="m-0 p-0 text-black-50 fw-semibold">Mulai
                                            Kerja</p>
                                        <p class="fw-medium" id="modalStartWork"></p>
                                    </div>
                                    <div class="text" style="width:50%;">
                                        <p class="m-0 p-0 text-black-50 fw-semibold">
                                            Selesai Kerja</p>
                                        <p class="fw-medium" id="modalFinishWork"></p>
                                    </div>
                                </div>
                                <hr class="my-1 border border-dark">
                                <div class="d-flex mt-1">
                                    <div class="text d-flex justify-content-between align-items-center"
                                        style="width:50%;">
                                        <p class="p-0 m-0 text-black-50 fw-semibold fs-6">
                                            Total</p>
                                        <p class="p-0 m-0 fw-medium fs-lg-6 text-end">Rp
                                            <span id="modalRequestPrice"></span>
                                        </p>
                                    </div>
                                    {{-- Removed Invoice Button as per user request --}}
                                </div>
                            </div>

                            <div class="vr d-none d-lg-block mx-3"></div>

                            {{-- Section for User Review and Report --}}
                            <div class="d-flex flex-column align-items-center justify-content-center flex-grow-1">
                                <h4 class="fw-semibold mt-3 mb-1" id="reviewSectionHeading"></h4>

                                {{-- Container for existing review or review form --}}
                                <div id="review-section-container" class="w-100">
                                    {{-- This content will be dynamically populated by JavaScript --}}
                                </div>

                                {{-- Action buttons for review submission and reporting --}}
                                <div class="d-flex flex-column mt-3 justify-content-center">
                                    <button type="submit" class="btn btn-primary fw-medium rounded-3"
                                        id="submitReviewButton">Kirim</button>
                                    <div class="m-1 text-center">Atau</div>
                                    <button type="button" class="m-0 p-0 fw-medium btn text-danger"
                                        onclick="openReportModal()" id="reportProblemButton">Laporkan masalah</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="reportWorkModal" tabindex="-1" aria-labelledby="reportWorkModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg" style="max-width: 900px;">
            <form id="reportForm" method="POST" enctype="multipart/form-data">
                @csrf
                {{-- Hidden inputs for report submission data --}}
                <input type="hidden" name="transaction_id" id="reportTransactionId">
                <input type="hidden" name="reporter_id" value="{{ auth()->id() }}">
                <input type="hidden" name="reported_id" id="reportReportedId">

                <div class="modal-content">
                    <div class="modal-header border-0 justify-content-center">
                        <h3 class="modal-title fw-bold text-center w-100" id="reportWorkModalLabel">Laporan</h3>
                        <button type="button" class="btn-close position-absolute end-0 me-3" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <hr class="mx-auto mb-3" style="width: 50px; height: 4px; background-color: #D3FA0D; border: none;">

                    <div class="modal-body" style="max-height: 75vh; overflow-y: auto;">
                        <div class="row mb-3">
                            <div class="col-md-6 col-lg-3">
                                <p class="text-black-50 fw-semibold mb-0">Judul Pesanan</p>
                                <p class="fw-medium" id="reportModalRequestTitle"></p>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <p class="text-black-50 fw-semibold mb-0">Nomor Pesanan</p>
                                <p class="fw-medium" id="reportModalOrderNumber"></p>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <p class="text-black-50 fw-semibold mb-0">Nama Klien</p>
                                <p class="fw-medium" id="reportModalRequesterName"></p>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <p class="text-black-50 fw-semibold mb-0">Lokasi</p>
                                <p class="fw-medium" id="reportModalRequestLocation"></p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6 col-lg-3">
                                <p class="text-black-50 fw-semibold mb-0">Tanggal Pemesanan</p>
                                <p class="fw-medium" id="reportModalTransactionCreatedAt"></p>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <p class="text-black-50 fw-semibold mb-0">Tanggal Selesai</p>
                                <p class="fw-medium" id="reportModalTransactionUpdatedAt"></p>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <p class="text-black-50 fw-semibold mb-0">Waktu Mulai</p>
                                <p class="fw-medium" id="reportModalStartWork"></p>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <p class="text-black-50 fw-semibold mb-0">Waktu Selesai</p>
                                <p class="fw-medium" id="reportModalFinishWork"></p>
                            </div>
                        </div>

                        {{-- Total price display in report modal --}}
                        <div class="d-flex justify-content-between mb-4">
                            <p class="text-black-50 fw-semibold mb-0">Total</p>
                            <p class="fw-medium fs-5 mb-0">Rp <span id="reportModalRequestPrice"></span></p>
                        </div>

                        {{-- Image upload section for report proof --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Upload Bukti (Gambar, maks 5MB per gambar):</label>
                            <div class="d-flex flex-wrap gap-3 align-items-start" id="reportImagePreviewContainer">
                                {{-- Images will be appended here dynamically by JS --}}
                                <div class="add-image-button pb-2"
                                    onclick="document.getElementById('reportImageInput').click()"
                                    style="width: 80px; height: 80px; border: 2px dashed #294287; background-color: #f7f7ff; display: flex; align-items: center; justify-content: center; cursor: pointer;">
                                    <span class="text-center" style="font-size: 32px; color:#294287;">+</span>
                                </div>
                                <span id="image-count"
                                    class="text-secondary fw-medium align-self-center ms-auto">0/7</span>
                            </div>
                            <input type="file" class="d-none" id="reportImageInput" name="photo[]" accept="image/*"
                                multiple>
                        </div>

                        {{-- Textarea for reporting reasons --}}
                        <div class="mb-4">
                            <label for="reportNote" class="form-label fw-semibold">Keluh Kesah Anda</label>
                            <textarea name="reasons" id="reportNote" class="form-control rounded-4" rows="4"
                                placeholder="Ceritakan masalah yang Anda alami..." style="background-color: #f7f7ff; resize: none;"></textarea>
                        </div>
                    </div>

                    {{-- Report submission button --}}
                    <div class="modal-footer border-0 d-flex justify-content-end">
                        <button type="button" id="submitReportButton" class="btn btn-danger px-4 py-2">Kirim
                            Laporan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="cancelWorkModal" tabindex="-1" aria-labelledby="cancelWorkModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" style="max-width: 600px;">
            <div class="modal-content p-4">

                <div class="d-flex align-items-center justify-content-center">
                    <svg width="42" height="42" viewBox="0 0 42 42" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M0.6875 21C0.6875 9.78125 9.78125 0.6875 21 0.6875C32.2188 0.6875 41.3125 9.78125 41.3125 21C41.3125 32.2188 32.2188 41.3125 21 41.3125C9.78125 41.3125 0.6875 32.2188 0.6875 21ZM21 13.1875C21.4144 13.1875 21.8118 13.3521 22.1049 13.6451C22.3979 13.9382 22.5625 14.3356 22.5625 14.75V22.5625C22.5625 22.9769 22.3979 23.3743 22.1049 23.6674C21.8118 23.9604 21.4144 24.125 21 24.125C20.5856 24.125 20.1882 23.9604 19.8951 23.6674C19.6021 23.3743 19.4375 22.9769 19.4375 22.5625V14.75C19.4375 14.3356 19.6021 13.9382 19.8951 13.6451C20.1882 13.3521 20.5856 13.1875 21 13.1875ZM21 30.375C21.4144 30.375 21.8118 30.2104 22.1049 29.9174C22.3979 29.6243 22.5625 29.2269 22.5625 28.8125C22.5625 28.3981 22.3979 28.0007 22.1049 27.7076C21.8118 27.4146 21.4144 27.25 21 27.25C20.5856 27.25 20.1882 27.4146 19.8951 27.7076C19.6021 28.0007 19.4375 28.3981 19.4375 28.8125C19.4375 29.2269 19.6021 29.6243 19.8951 29.9174C20.1882 30.2104 20.5856 30.375 21 30.375Z"
                            fill="#B02A37" />
                    </svg>
                    <h4 class="text-danger text-center fw-bold mb-0 mx-3 fs-4">Batalkan Pekerjaan?</h4>
                    <svg width="42" height="42" viewBox="0 0 42 42" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M0.6875 21C0.6875 9.78125 9.78125 0.6875 21 0.6875C32.2188 0.6875 41.3125 9.78125 41.3125 21C41.3125 32.2188 32.2188 41.3125 21 41.3125C9.78125 41.3125 0.6875 32.2188 0.6875 21ZM21 13.1875C21.4144 13.1875 21.8118 13.3521 22.1049 13.6451C22.3979 13.9382 22.5625 14.3356 22.5625 14.75V22.5625C22.5625 22.9769 22.3979 23.3743 22.1049 23.6674C21.8118 23.9604 21.4144 24.125 21 24.125C20.5856 24.125 20.1882 23.9604 19.8951 23.6674C19.6021 23.3743 19.4375 22.9769 19.4375 22.5625V14.75C19.4375 14.3356 19.6021 13.9382 19.8951 13.6451C20.1882 13.3521 20.5856 13.1875 21 13.1875ZM21 30.375C21.4144 30.375 21.8118 30.2104 22.1049 29.9174C22.3979 29.6243 22.5625 29.2269 22.5625 28.8125C22.5625 28.3981 22.3979 28.0007 22.1049 27.7076C21.8118 27.4146 21.4144 27.25 21 27.25C20.5856 27.25 20.1882 27.4146 19.8951 27.7076C19.6021 28.0007 19.4375 28.3981 19.4375 28.8125C19.4375 29.2269 19.6021 29.6243 19.8951 29.9174C20.1882 30.2104 20.5856 30.375 21 30.375Z"
                            fill="#B02A37" />
                    </svg>
                </div>

                <div class="text-center my-4 fw-semibold">
                    <div>Apakah kamu yakin ingin membatalkan pekerjaan ini?</div>
                    <div>Tindakan ini bisa mempengaruhi reputasimu di platform KerjaIn.</div>
                </div>

                <form action="{{ route('transaction.cancel', $transaction->id) }}" method="POST"
                    class="d-flex flex-column flex-lg-row gap-3 mt-4">
                    @csrf
                    <input type="hidden" name="redirect_to" value="job-taker.home">
                    <button type="button" class="btn btn-outline-primary rounded-4 flex-fill p-3 fw-semibold"
                        data-bs-dismiss="modal" style="border-width:2px;">
                        Lanjut Kerja
                    </button>
                    <button type="submit" class="btn btn-danger rounded-4 flex-fill p-3 fw-semibold">
                        Ya, Tetap Batalin
                    </button>
                </form>

            </div>
        </div>
    </div>

    <div class="modal fade" id="completionProofModal" tabindex="-1" aria-labelledby="completionProofModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" style="max-width: 800px;">
            <div class="modal-content p-4">
                <div class="flex-fill text-center fs-4 mb-3">Konfirmasi Penyelesaian Pekerjaan</div>
                <form id="proofForm" action="{{ route('worker.uploadProof', $transaction->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf

                    <div class="d-flex flex-column flex-lg-row gap-3">

                        <div class="w-100">
                            <label for="photo" class="form-label fw-semibold">Upload Foto Bukti Pekerjaan</label>
                            <div id="uploadAreaProof"
                                class="rounded p-4 text-center d-flex flex-column align-items-center justify-content-center"
                                style="cursor: pointer; min-height: 200px; border-style: dashed; border-color:#cacadd; background-color: #F4f4f4;">
                                <div id="previewContainerProof"
                                    class="d-flex flex-wrap gap-2 justify-content-center w-100 h-100 align-items-center">
                                    {{-- Placeholder & preview will be generated by JavaScript --}}
                                </div>
                                <input type="file" id="photoInputProof" name="photo[]" accept="image/*" multiple
                                    class="d-none">
                            </div>
                            {{-- Error message display area for photo input --}}
                            <div id="photoError" class="text-danger small mt-1"></div>
                        </div>

                        <div class="vr d-none d-lg-block mx-3"></div>

                        <div class="w-100">
                            <label for="note" class="form-label fw-semibold">Catatan (Opsional)</label>
                            <textarea name="note" id="note" rows="3" class="form-control form-control-sm"
                                style="border-color:#b4b4b4; height:70%;"></textarea>
                            {{-- Error message display area for note input --}}
                            <div id="noteError" class="text-danger small mt-1"></div>

                            <div class="d-flex mt-3 flex-column flex-sm-row gap-2">
                                <button type="button" class="btn flex-fill fw-semibold"
                                    style="color:#294287; border-color:#294287; border-width: 2px;"
                                    data-bs-dismiss="modal">
                                    Kembali
                                </button>
                                <button type="button" class="btn flex-fill fw-semibold text-light"
                                    style="background-color:#309FFF;" onclick="uploadProof()">
                                    Selesaikan Pekerjaan
                                </button>
                            </div>
                        </div>

                    </div>

                </form>
            </div>
        </div>
    </div>

    {{-- JavaScript section --}}
    <script>
        window.isOpeningReportModal = false; // Initialize global flag

        // Global variables for managing report images
        let reportFiles = []; // Array of File objects or URLs for the report modal
        const MAX_REPORT_IMAGES = 7; // Define max images constant

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize upload area for "Completion Proof" (image placeholder)
            initPhotoPreview('photoInputProof', 'previewContainerProof', 'uploadAreaProof', 'image');

            // --- Image Preview Logic for Report Modal ---
            const reportImageInput = document.getElementById('reportImageInput');
            const reportImagePreviewContainer = document.getElementById('reportImagePreviewContainer');
            const addImageButton = reportImagePreviewContainer ? reportImagePreviewContainer.querySelector(
                '.add-image-button') : null;
            const imageCountSpan = document.getElementById('image-count');

            /**
             * Updates the display of uploaded report images and the image count.
             * Newest image appears to the left of the '+' button.
             * This function handles both newly added images and pre-existing images from a report.
             */
            function updateReportImagePreview() {
                // Clear all current image preview wrappers to re-render them
                if (reportImagePreviewContainer) {
                    reportImagePreviewContainer.querySelectorAll('.image-preview-wrapper').forEach(el => el
                    .remove());

                    // Iterate in order for existing, and correctly place them relative to the addImageButton
                    reportFiles.forEach((fileOrUrl, index) => {
                        const isFileObject = fileOrUrl instanceof File;
                        const src = isFileObject ? URL.createObjectURL(fileOrUrl) : fileOrUrl;

                        const wrapper = document.createElement('div');
                        wrapper.className = 'image-preview-wrapper position-relative';
                        wrapper.style.width = '80px';
                        wrapper.style.height = '80px';

                        const img = document.createElement('img');
                        img.src = src;
                        img.className = 'rounded border img-thumbnail';
                        img.style.width = '100%';
                        img.style.height = '100%';
                        img.style.objectFit = 'cover';

                        const deleteButton = document.createElement('button');
                        deleteButton.className =
                            'btn-close position-absolute top-0 end-0 m-1';
                        deleteButton.style.fontSize = '0.7rem';
                        deleteButton.style.backgroundColor = '#dc3545';
                        deleteButton.style.borderRadius = '50%';
                        deleteButton.style.padding = '0.25em';
                        deleteButton.type = 'button';
                        deleteButton.onclick = function() {
                            deleteReportImage(index); // Use captured index for correct deletion
                        };

                        wrapper.appendChild(img);
                        wrapper.appendChild(deleteButton);

                        // Insert the new image wrapper right before the 'add-image-button'
                        // This ensures new images stack to the right, and the '+' button remains at the end
                        reportImagePreviewContainer.insertBefore(wrapper, addImageButton);
                    });

                    // Update image count and add button visibility
                    imageCountSpan.textContent = `${reportFiles.length}/${MAX_REPORT_IMAGES}`;
                    if (reportFiles.length >= MAX_REPORT_IMAGES) {
                        if (addImageButton) addImageButton.style.display = 'none'; // Hide the add button
                    } else {
                        if (addImageButton) addImageButton.style.display = 'flex'; // Show the add button
                    }
                }
            }

            /**
             * Deletes an image from the reportFiles array at the specified index and updates the preview.
             * @param {number} index - The index of the image to delete.
             */
            function deleteReportImage(index) {
                reportFiles.splice(index, 1); // Remove the file from the array
                updateReportImagePreview(); // Re-render previews to reflect deletion and update indices
            }

            if (reportImageInput) {
                reportImageInput.addEventListener('change', function(event) {
                    // Iterate over selected files and apply validation
                    Array.from(event.target.files).forEach(file => {
                        // Client-side validation for file type
                        if (!file.type.startsWith('image/')) {
                            window.showCustomAlert('File yang diunggah harus berupa gambar.',
                                'error');
                            return; // Skip this file and continue to next
                        }
                        // Client-side validation for file size
                        const maxSizeBytes = 5 * 1024 * 1024; // 5 MB
                        if (file.size > maxSizeBytes) {
                            window.showCustomAlert('Ukuran foto bukti laporan maksimal 5 MB.',
                                'error');
                            return; // Skip this file and continue to next
                        }

                        // Add file to array if max limit not reached
                        if (reportFiles.length < MAX_REPORT_IMAGES) {
                            reportFiles.push(file);
                        } else {
                            window.showCustomAlert(
                                `Maksimal ${MAX_REPORT_IMAGES} foto bukti laporan dapat diunggah.`,
                                'error');
                            // Stop processing further files if limit is hit
                            return;
                        }
                    });
                    event.target.value = ''; // Clear the input value to allow re-selection of same files
                    updateReportImagePreview(); // Update the visual display
                });
            }


            // --- Function to Open Report Modal ---
            window.openReportModal = function() {
                window.isOpeningReportModal = true;
                // Close the completion modal before opening the report modal
                const completionModal = bootstrap.Modal.getInstance(document.getElementById('completionModal'));
                if (completionModal) {
                    completionModal.hide();
                }

                const reportModalElement = document.getElementById('reportWorkModal');
                const reportModal = new bootstrap.Modal(reportModalElement);

                // Populate report modal fields with data from the current transaction
                document.getElementById('reportModalRequestTitle').textContent =
                `{{ $request->title ?? '-' }}`;
                document.getElementById('reportModalOrderNumber').textContent =
                    `{{ $transaction->order_number ?? '-' }}`;
                document.getElementById('reportModalRequesterName').textContent =
                    `{{ $request->requester->first_name ?? '' }} {{ $request->requester->last_name ?? '' }}`;
                document.getElementById('reportModalRequestLocation').textContent =
                    `{{ $request->location ?? '-' }}`;
                document.getElementById('reportModalTransactionCreatedAt').textContent =
                    `{{ \Carbon\Carbon::parse($transaction->created_at)->format('d M Y') ?? '-' }}`;
                document.getElementById('reportModalTransactionUpdatedAt').textContent =
                    `{{ \Carbon\Carbon::parse($transaction->updated_at)->format('d M Y') ?? '-' }}`;
                document.getElementById('reportModalRequestPrice').textContent =
                    `{{ number_format($request->final_price ?? 0, 0, ',', '.') ?? '-' }}`;
                document.getElementById('reportModalStartWork').textContent =
                    `{{ $transaction->start_work ? \Carbon\Carbon::parse($transaction->start_work)->format('H.i') : '-' }}`;
                document.getElementById('reportModalFinishWork').textContent =
                    `{{ $transaction->finish_work ? \Carbon\Carbon::parse($transaction->finish_work)->format('H.i') : '-' }}`;


                // Set hidden form fields for submission
                document.getElementById('reportTransactionId').value = `{{ $transaction->id }}`;
                document.getElementById('reportReportedId').value = `{{ $transaction->requester->id }}`;

                // Set form action for report submission
                document.getElementById('reportForm').action =
                    `{{ route('worker.submitReport', $transaction->id) }}`;

                // Check if a report already exists for this transaction
                const hasWorkerReport = `{{ $hasWorkerReport ? 'true' : 'false' }}` === 'true';
                const reportPhotoUrlsJson = `{{ json_encode($workerReport->decoded_photo_urls ?? []) }}`;
                const reportReasons = `{{ $workerReport->reasons ?? '' }}`;

                if (hasWorkerReport) {
                    try {
                        reportFiles = JSON.parse(reportPhotoUrlsJson); // Load existing URLs
                        document.getElementById('reportNote').value = reportReasons; // Populate reasons
                        // Disable fields if report already exists
                        document.getElementById('reportNote').disabled = true;
                        if (reportImageInput) reportImageInput.disabled = true;
                        if (document.getElementById('submitReportButton')) document.getElementById(
                            'submitReportButton').style.display = 'none'; // Hide submit button
                        if (addImageButton) addImageButton.style.display = 'none'; // Hide add image button
                    } catch (e) {
                        console.error('Error parsing report photo URLs:', e);
                        reportFiles = []; // Fallback to empty
                    }
                } else {
                    // Reset for new report
                    reportFiles = [];
                    document.getElementById('reportNote').value = '';
                    document.getElementById('reportNote').disabled = false;
                    if (reportImageInput) reportImageInput.disabled = false;
                    if (document.getElementById('submitReportButton')) document.getElementById(
                        'submitReportButton').style.display = 'block'; // Show submit button
                    if (addImageButton) addImageButton.style.display = 'flex'; // Show add image button
                }
                updateReportImagePreview(); // Render initial state (either empty or existing images)


                // Show the report modal after a brief delay
                setTimeout(() => {
                    reportModal.show();
                }, 300);
            };

            // --- Star Rating Functionality for Review Modal ---
            // Updates the visual fill of the stars
            function updateStarDisplay(rating) {
                const stars = document.querySelectorAll('#review-section-container .star-rating');
                stars.forEach(star => {
                    const sVal = parseInt(star.getAttribute('data-value'));
                    if (sVal <= rating) {
                        star.classList.add('star-blue');
                        star.classList.remove('text-secondary');
                    } else {
                        star.classList.remove('star-blue');
                        star.classList.add('text-secondary');
                    }
                });
            }


            // --- Function to Submit Review ---
            window.submitReview = function() {
                const comment = document.getElementById('comment').value.trim();
                const ratingInput = document.getElementById('rating-input');
                const rating = ratingInput ? parseInt(ratingInput.value) : 0;

                // Client-side validation for rating and comment
                if (rating == 0) {
                    window.showCustomAlert('Silakan pilih rating terlebih dahulu.', 'error');
                    return;
                }

                if (comment == '') {
                    window.showCustomAlert('Silakan isi komentar.', 'error');
                    return;
                }

                // Set the form action dynamically (important for when reviewForm is rendered)
                const reviewForm = document.getElementById('reviewForm');
                reviewForm.action = `{{ route('reviews.store', $transaction->id) }}`;

                // Prepare FormData for submission
                const formData = new FormData();
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute(
                    'content'));
                formData.append('transaction_id', `{{ $transaction->id }}`);
                formData.append('reviewer_id', `{{ auth()->id() }}`);
                formData.append('reviewee_id', `{{ $transaction->requester_id }}`);
                formData.append('rating', rating);
                formData.append('comment', comment);


                // Optionally, disable button and show loading here, as page will reload
                const submitBtn = document.getElementById('submitReviewButton');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Mengirim...';
                }

                fetch(reviewForm.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest', // Important for Laravel to detect AJAX
                        },
                        credentials: 'same-origin'
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(errorData => {
                                throw errorData;
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log("Review submission response:", data);
                        if (data.success) {
                            window.showCustomAlert(data.message, "success");
                            // Close the completion modal
                            const completionModal = bootstrap.Modal.getInstance(document.getElementById(
                                'completionModal'));
                            if (completionModal) completionModal.hide();
                            // Reload the page to reflect the new state (review submitted)
                            location.reload();
                        } else {
                            let errorMessage = data.message || 'Terjadi kesalahan saat menyimpan ulasan.';
                            if (data.errors) {
                                for (const key in data.errors) {
                                    if (data.errors.hasOwnProperty(key)) {
                                        data.errors[key].forEach(msg => {
                                            errorMessage += `\n- ${msg}`;
                                        });
                                    }
                                }
                            }
                            window.showCustomAlert(errorMessage, "error");
                        }
                    })
                    .catch(error => {
                        console.error('Error submitting review:', error);
                        let errorMessage = 'Terjadi kesalahan saat menyimpan ulasan.';
                        if (error.message) {
                            errorMessage = error.message;
                        } else if (error.errors) { // Handle Laravel validation errors
                            errorMessage = 'Validasi gagal:';
                            for (const key in error.errors) {
                                errorMessage += `\n- ${error.errors[key].join(', ')}`;
                            }
                        }
                        window.showCustomAlert(errorMessage, "error");
                    })
                    .finally(() => {
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.textContent = 'Kirim';
                        }
                    });
            }

            // --- Function to Submit Report ---
            window.submitReport = function(event) {
                if (event) event
            .preventDefault(); // Prevent default form submission if called from an event listener

                const form = document.getElementById('reportForm');
                const reasons = document.getElementById('reportNote').value.trim();

                if (reportFiles.length === 0) {
                    window.showCustomAlert("Silakan upload minimal satu foto bukti laporan.", 'error');
                    return;
                }
                if (!reasons) {
                    window.showCustomAlert('Harap isi keluh kesah Anda terlebih dahulu.', 'error');
                    return;
                }

                // Re-validate files in reportFiles array before submission, as user might delete/add
                let hasInvalidFile = false;
                // Filter out existing URLs and only send File objects
                const filesToSend = reportFiles.filter(item => item instanceof File);

                for (const file of filesToSend) { // Use for...of for easy breaking
                    if (!file.type.startsWith('image/')) {
                        window.showCustomAlert('File yang diunggah harus berupa gambar.', 'error');
                        hasInvalidFile = true;
                        break; // Exit loop
                    }
                    const maxSizeBytes = 5 * 1024 * 1024; // 5 MB
                    if (file.size > maxSizeBytes) {
                        window.showCustomAlert('Ukuran foto bukti laporan maksimal 5 MB.', 'error');
                        hasInvalidFile = true;
                        break; // Exit loop
                    }
                }
                if (hasInvalidFile) {
                    return;
                }

                const formData = new FormData(); // Create new FormData object for submission
                // Append static form fields
                formData.append('transaction_id', document.getElementById('reportTransactionId').value);
                formData.append('reporter_id', document.getElementById('reportForm').querySelector(
                        'input[name="reporter_id"]')
                    .value);
                formData.append('reported_id', document.getElementById('reportReportedId').value);
                formData.append('reasons', reasons);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute(
                    'content')); // Manually add CSRF token

                // Append all collected files from our `reportFiles` array
                filesToSend.forEach((file, index) => {
                    formData.append(`photo[${index}]`, file);
                });

                const submitBtn = document.getElementById('submitReportButton');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Mengirim Laporan...';
                }

                // Submit the form using fetch, expecting a redirect
                fetch(form.action, { // Use the form's action which includes transaction ID
                        method: 'POST',
                        body: formData,
                        headers: {
                            // DO NOT set 'Content-Type': 'multipart/form-data' explicitly when using FormData,
                            // the browser does it correctly with a boundary.
                            'Accept': 'application/json, text/plain, */*' // Accept various response types
                        },
                        redirect: 'follow' // Instructs fetch to follow redirects
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(errorData => {
                                let errorMessage = '';
                                if (errorData.errors) {
                                    for (let key in errorData.errors) {
                                        errorMessage += `${errorData.errors[key].join(', ')}\n`;
                                    }
                                    window.showCustomAlert('Validasi Gagal:\n' + errorMessage,
                                        'error');
                                } else {
                                    throw new Error(errorData.message || 'Server error: ' + response
                                        .statusText);
                                }
                            });
                        }
                        return response.text(); // Consume response body, even if it's a redirect
                    })
                    .then(text => {
                        // This block is generally only hit if the server did NOT redirect, but responded with success.
                        // If a redirect happens, the page will reload and this block won't be reached.
                        console.log("Fetch completed, but no redirect occurred:", text);
                    })
                    .catch(error => {
                        console.error('Error during report submission:', error);
                        window.showCustomAlert('Terjadi kesalahan saat mengirim laporan.\nDetails: ' +
                            error.message,
                            'error');
                    })
                    .finally(() => {
                        // This finally block will always run.
                        // For a successful redirect, the page will reload, making these UI updates moot.
                        // But for client-side errors or server-side errors that don't redirect, they are important.
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.textContent = 'Kirim Laporan';
                        }
                    });
            }

            // Function to render the review form
            function renderReviewForm() {
                reviewSectionHeading.textContent = 'Kasih penilaian, yuk!';
                const reviewSectionContainer = document.getElementById('review-section-container');
                if (!reviewSectionContainer) return;

                reviewSectionContainer.innerHTML = `
                    <div class="text-center mt-0 mb-3 w-100">
                        @for ($i = 1; $i <= 5; $i++)
                            <i class="bi bi-star-fill text-secondary star-rating fs-2" data-value="{{ $i }}"></i>
                        @endfor
                        <input type="hidden" name="rating" id="rating-input" value="0">
                    </div>
                    <div class="ps-3 flex-fill d-flex flex-column w-100">
                        <label for="comment" class="form-label text-start">Komentar</label>
                        <textarea name="comment" id="comment" class="form-control" rows="3"
                            placeholder="Tulis komentarmu di sini..." style="border-color:#8a8a8a; resize: none;"></textarea>
                    </div>
                `;
                const newStars = reviewSectionContainer.querySelectorAll('.star-rating');
                const newRatingInput = document.getElementById('rating-input');
                let currentSelectedRating = 0; // Local state for new review form interaction

                newStars.forEach(star => {
                    star.addEventListener('mouseover', function() {
                        const val = parseInt(this.getAttribute('data-value'));
                        updateStarDisplay(val);
                    });
                    star.addEventListener('mouseout', function() {
                        updateStarDisplay(
                        currentSelectedRating); // Revert to selected rating on mouseout
                    });
                    star.addEventListener('click', function() {
                        currentSelectedRating = parseInt(this.getAttribute('data-value'));
                        if (newRatingInput) newRatingInput.value = currentSelectedRating;
                        updateStarDisplay(currentSelectedRating);
                    });
                });
                const submitReviewButton = document.getElementById('submitReviewButton');
                if (submitReviewButton) {
                    submitReviewButton.style.display = 'block';
                }
            }

            // Function to render the existing review display
            function renderExistingReview(rating, comment) {
                reviewSectionHeading.textContent = 'Ini Penilaian Klien Untukmu';
                const reviewSectionContainer = document.getElementById('review-section-container');
                if (!reviewSectionContainer) return;

                let starHtml = '';
                for (let i = 1; i <= 5; i++) {
                    starHtml +=
                        `<i class="bi bi-star-fill fs-2 ${i <= rating ? 'star-blue' : 'text-secondary'} star-animate"></i>`;
                }

                reviewSectionContainer.innerHTML = `
                    <div class="text-center mt-0 mb-3 w-100">
                        ${starHtml}
                    </div>
                    <div class="ps-3 flex-fill d-flex flex-column w-100">
                        <label for="comment" class="form-label text-start">Komentar</label>
                        <textarea id="comment" class="form-control" rows="3" disabled
                            style="border-color:#8a8a8a; resize: none;">${comment}</textarea>
                    </div>
                `;
                const submitReviewButton = document.getElementById('submitReviewButton');
                if (submitReviewButton) {
                    submitReviewButton.style.display = 'none';
                }
            }


            // Dynamic content and button states for Completion Modal
            const completionModalElement = document.getElementById('completionModal');
            if (completionModalElement) {
                completionModalElement.addEventListener('show.bs.modal', function() {
                    // Populate the completion modal with data from Blade variables
                    document.getElementById('modalRequestTitle').textContent =
                        `{{ $request->title ?? '-' }}`;
                    document.getElementById('modalOrderNumber').textContent =
                        `{{ $transaction->order_number ?? '-' }}`;
                    document.getElementById('modalRequesterName').textContent =
                        `{{ $transaction->requester->first_name ?? '' }} {{ $transaction->requester->last_name ?? '' }}`;
                    document.getElementById('modalRequestLocation').textContent =
                        `{{ $request->location ?? '-' }}`;
                    document.getElementById('modalTransactionCreatedAt').textContent =
                        `{{ \Carbon\Carbon::parse($transaction->created_at)->format('d M Y') ?? '-' }}`;
                    document.getElementById('modalTransactionUpdatedAt').textContent =
                        `{{ \Carbon\Carbon::parse($transaction->updated_at)->format('d M Y') ?? '-' }}`;
                    document.getElementById('modalRequestPrice').textContent =
                        `{{ number_format($request->final_price ?? 0, 0, ',', '.') ?? '-' }}`;
                    document.getElementById('modalStartWork').textContent =
                        `{{ $transaction->start_work ? \Carbon\Carbon::parse($transaction->start_work)->format('H.i') : '-' }}`;
                    document.getElementById('modalFinishWork').textContent =
                        `{{ $transaction->finish_work ? \Carbon\Carbon::parse($transaction->finish_work)->format('H.i') : '-' }}`;

                    // Conditional rendering of review section
                    const hasReviewRequester = `{{ $hasReviewRequester ? 'true' : 'false' }}` === 'true';
                    if (hasReviewRequester) {
                        const requesterRating = `{{ $receivedReviewRequester->rating ?? 0 }}`;
                        const requesterComment = `{{ $receivedReviewRequester->comment ?? '' }}`;
                        renderExistingReview(parseInt(requesterRating), requesterComment);
                    } else {
                        renderReviewForm();
                    }

                    // Handle Report Button State
                    const reportProblemButton = document.getElementById('reportProblemButton');
                    const hasWorkerReport = `{{ $hasWorkerReport ? 'true' : 'false' }}` === 'true';
                    if (reportProblemButton) {
                        if (hasWorkerReport) {
                            reportProblemButton.textContent = 'Laporan sudah terkirim';
                            reportProblemButton.disabled = true;
                            reportProblemButton.classList.remove('text-danger');
                            reportProblemButton.classList.add('text-secondary');
                            reportProblemButton.onclick = null; // Remove click listener
                        } else {
                            reportProblemButton.textContent = 'Laporkan masalah';
                            reportProblemButton.disabled = false;
                            reportProblemButton.classList.remove('text-secondary');
                            reportProblemButton.classList.add('text-danger');
                            reportProblemButton.onclick = window
                            .openReportModal; // Re-attach click listener
                        }
                    }
                });

                // Reset review form state when the completion modal is hidden
                completionModalElement.addEventListener('hidden.bs.modal', function() {
                    // Only reload if we are NOT in the process of opening the report modal
                    if (!window.isOpeningReportModal) {
                        location.reload();
                    }
                    // Reset flag after modal is hidden
                    window.isOpeningReportModal = false;
                });
            }


            const submitReportButtonForReportModal = document.getElementById('submitReportButton');
            if (submitReportButtonForReportModal) {
                submitReportButtonForReportModal.addEventListener('click', window.submitReport);
            }

            // Logic for "Mulai Kerja" button
            const startWorkForm = document.getElementById('start-work-form');
            if (startWorkForm) {
                startWorkForm.addEventListener('submit', function(event) {
                    event.preventDefault(); // Prevent default form submission

                    const form = event.target;
                    const formData = new FormData(form);

                    fetch(form.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .getAttribute('content'),
                                'X-Requested-With': 'XMLHttpRequest', // Important for Laravel to detect AJAX
                                'Accept': 'application/json' // Explicitly ask for JSON response
                            },
                            credentials: 'same-origin'
                        })
                        .then(response => {
                            if (!response.ok) {
                                return response.json().then(errorData => {
                                    throw errorData;
                                });
                            }
                            return response.json();
                        })
                        .then(data => {
                            console.log('Start work response:', data);
                            if (data.success) {
                                window.showCustomAlert(data.message, "success");
                                // Reload the page to reflect the new status
                                location.reload();
                            } else {
                                window.showCustomAlert(data.message, "error");
                            }
                        })
                        .catch(error => {
                            console.error('Error starting work:', error);
                            let errorMessage = 'Terjadi kesalahan saat memulai pekerjaan.';
                            if (error.message) {
                                errorMessage = error.message;
                            } else if (error.errors) {
                                errorMessage += '\n' + Object.values(error.errors).flat().join('\n');
                            }
                            window.showCustomAlert(errorMessage, "error");
                        });
                });
            }

            // Clear report form state and re-enable button when the report modal is hidden
            const reportWorkModalElement = document.getElementById('reportWorkModal'); // Changed to new modal ID
            if (reportWorkModalElement) {
                reportWorkModalElement.addEventListener('hidden.bs.modal', function() {
                    // When report modal is closed, re-open the completion modal
                    const completionModal = new bootstrap.Modal(document.getElementById('completionModal'));
                    completionModal.show(); // This will re-trigger its show.bs.modal event and re-populate
                    // Clear report form state
                    document.getElementById('reportNote').value = ''; // Clear textarea
                    reportFiles = []; // Clear the global array of files
                    updateReportImagePreview(); // Reset preview container
                    if (reportImageInput) reportImageInput.value = ''; // Clear file input
                    if (document.getElementById('submitReportButton')) {
                        document.getElementById('submitReportButton').disabled = false;
                        document.getElementById('submitReportButton').textContent = 'Kirim Laporan';
                    }
                });
            }

            // === FIX FOR PERSISTENT OVERLAY ===
            // Listen for any Bootstrap modal to be hidden and manually remove any remaining backdrops.
            document.querySelectorAll('.modal').forEach(modalElement => {
                modalElement.addEventListener('hidden.bs.modal', function() {
                    const backdrops = document.querySelectorAll('.modal-backdrop');
                    backdrops.forEach(backdrop => backdrop.remove());

                    // Also ensure body scrolling is re-enabled, as it sometimes gets stuck
                    document.body.classList.remove('modal-open');
                    document.body.style.overflow = '';
                    // Clear any padding added by Bootstrap for scrollbar
                    document.body.style.paddingRight = '';
                });
            });
            // === END FIX ===

        }); // End of DOMContentLoaded

        // Existing uploadProof function - MODIFIED to remove review modal's reload listener
        function uploadProof() {
            var form = document.getElementById('proofForm');
            var formData = new FormData(form);

            const photoInput = document.getElementById('photoInputProof');
            const photoErrorDiv = document.getElementById('photoError');
            const noteErrorDiv = document.getElementById('noteError');

            if (photoErrorDiv) photoErrorDiv.textContent = '';
            if (noteErrorDiv) noteErrorDiv.textContent = '';

            if (photoInput && photoInput.files.length === 0) {
                if (photoErrorDiv) photoErrorDiv.textContent = 'Minimal satu foto diperlukan.';
                return;
            }

            fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(errorData => {
                            throw errorData;
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Upload proof response:', data);
                    if (data.success) {
                        window.showCustomAlert(data.message, "success");

                        // Close the completion proof modal
                        var modal = bootstrap.Modal.getInstance(document.getElementById('completionProofModal'));
                        if (modal) {
                            modal.hide();
                        }

                        // Show the completionModal (review modal) immediately after the alert
                        setTimeout(() => {
                            var completionModal = new bootstrap.Modal(document.getElementById(
                                'completionModal'));
                            completionModal.show();
                        }, 50);

                    } else {
                        window.showCustomAlert(data.message, "error");
                    }
                })
                .catch(error => {
                    console.error('Error uploading proof:', error);
                    if (error.errors) {
                        let errorMessage = 'Terjadi kesalahan validasi:';
                        for (const key in error.errors) {
                            if (error.errors.hasOwnProperty(key)) {
                                error.errors[key].forEach(msg => {
                                    errorMessage += `\n- ${msg}`;
                                    if (photoErrorDiv && (key === 'photo' || key.startsWith('photo.'))) {
                                        photoErrorDiv.textContent = msg;
                                    } else if (noteErrorDiv && key === 'note') {
                                        noteErrorDiv.textContent = msg;
                                    }
                                });
                            }
                        }
                        window.showCustomAlert(errorMessage, "error");
                    } else if (error.message) {
                        window.showCustomAlert(error.message, "error");
                    } else {
                        window.showCustomAlert('Terjadi kesalahan saat upload foto. Silakan coba lagi.', "error");
                    }
                });
        }


        // Function to initialize photo preview logic (retained from original, but slightly adapted for `plus` type)
        function initPhotoPreview(inputId, previewContainerId, uploadAreaId, placeholderType = 'image') {
            const inputEl = document.getElementById(inputId);
            const previewContainer = document.getElementById(previewContainerId);
            const uploadArea = document.getElementById(uploadAreaId);

            if (!inputEl || !previewContainer || !uploadArea || uploadArea.dataset.initialized) {
                return; // Stop if elements are missing or already initialized
            }

            const createPlaceholder = () => {
                const placeholder = document.createElement('div');
                if (placeholderType === 'plus') {
                    placeholder.className = 'pb-2 add-image-button'; // Add class for easy targeting later
                    placeholder.style.cssText =
                        "width: 80px; height: 80px; border: 2px dashed #294287; background-color: #f7f7ff; display: flex; align-items: center; justify-content: center; cursor: pointer;";
                    placeholder.innerHTML =
                        `<span class="text-center" style="font-size: 32px; color:#294287;">+</span>`;
                } else { // default to 'image'
                    placeholder.className =
                        'text-center text-muted d-flex flex-column align-items-center justify-content-center w-100 h-100';
                    placeholder.innerHTML = `
                        <svg width="60" height="60" viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M7.5 30C7.5 27.0163 8.68526 24.1548 10.795 22.045C12.9048 19.9353 15.7663 18.75 18.75 18.75H101.25C104.234 18.75 107.095 19.9353 109.205 22.045C111.315 24.1548 112.5 27.0163 112.5 30V90C112.5 92.9837 111.315 95.8452 109.205 97.9549C107.095 100.065 104.234 101.25 101.25 101.25H18.75C15.7663 101.25 12.9048 100.065 10.795 97.9549C8.68526 95.8452 7.5 92.9837 7.5 90V30ZM15 80.3V90C15 92.07 16.68 93.75 18.75 93.75H101.25C102.245 93.75 103.198 93.3549 103.902 92.6517C104.605 91.9484 105 90.9946 105 90V80.3L91.55 66.855C90.1437 65.4505 88.2375 64.6616 86.25 64.6616C84.2625 64.6616 82.3563 65.4505 80.95 66.855L76.55 71.25L81.4 76.1C81.7684 76.4433 82.0639 76.8573 82.2689 77.3173C82.4739 77.7773 82.5841 78.2739 82.593 78.7774C82.6018 79.2809 82.5092 79.781 82.3206 80.248C82.132 80.7149 81.8513 81.1391 81.4952 81.4952C81.1391 81.8513 80.7149 82.132 80.248 82.3206C79.781 82.5092 79.2809 82.6018 78.7774 82.593C78.2739 82.5841 77.7773 82.4739 77.3173 82.2689C76.8573 82.0639 76.4433 81.7684 76.1 81.4L50.3 55.605C48.8937 54.2005 46.9875 53.4116 45 53.4116C43.0125 53.4116 41.1063 54.2005 39.7 55.605L15 80.305V80.3ZM65.625 41.25C65.625 39.7582 66.2176 38.3274 67.2725 37.2725C68.3274 36.2176 69.7582 35.625 71.25 35.625C72.7418 35.625 74.1726 36.2176 75.2275 37.2725C76.2824 38.3274 76.875 39.7582 76.875 41.25C76.875 42.7418 76.2824 44.1726 75.2275 45.2275C74.1726 46.2824 72.7418 46.875 71.25 46.875C69.7582 46.875 68.3274 46.2824 67.2725 45.2275C66.2176 44.1726 65.625 42.7418 65.625 41.25Z" fill="#294287"/></svg>
                        <p class="mb-0 mt-2 small">Klik untuk upload bukti pekerjaan</p>
                    `;
                }
                return placeholder;
            };

            const updatePreview = () => {
                previewContainer.innerHTML = '';
                const files = inputEl.files;

                Array.from(files).forEach(file => {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const previewWrapper = document.createElement('div');
                        previewWrapper.className = 'position-relative m-1';
                        previewWrapper.innerHTML =
                            `<img src="${e.target.result}" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">`;
                        previewContainer.appendChild(previewWrapper);
                    };
                    reader.readAsDataURL(file);
                });

                // If no files (e.g., after deletion), or if placeholder is '+', add the placeholder
                if (files.length === 0 || placeholderType === 'plus') {
                    previewContainer.appendChild(createPlaceholder());
                }
            };

            uploadArea.addEventListener('click', (e) => {
                // Ensure clicking on an image preview doesn't open the file dialog
                if (e.target.tagName !== 'IMG') {
                    inputEl.click();
                }
            });

            inputEl.addEventListener('change', updatePreview);
            updatePreview();
            uploadArea.dataset.initialized = 'true'; // Mark as initialized
        }
    </script>
    <style>
        /* Star Rating Styles */
        .star-rating,
        .star-animate {
            cursor: pointer;
            transition: color 0.2s ease-in-out;
            margin-right: 0.1em;
            /* Consistent right margin */
            margin-left: 0.1em;
            /* Consistent left margin */
            display: inline-block;
            /* Ensure margins are respected */
        }

        .star-rating:last-of-type,
        .star-animate:last-of-type {
            margin-right: 0;
            /* No right margin for the last star */
        }

        .star-blue {
            color: gold !important;
        }

        /* Tab Navigation (from previous context, might not be fully used here but included for completeness) */
        .tabs-wrapper {
            height: 3.5rem;
            border-radius: 1.5rem;
            position: relative;
            z-index: 0;
            display: flex;
            flex-wrap: wrap;
        }

        .tab-button {
            border-radius: 0.5rem;
            color: #6b7280;
            font-weight: 500;
            transition: all 0.2s ease-in-out;
            background-color: transparent;
            border: none;
            cursor: pointer;
            white-space: nowrap;
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            padding: 0.5rem 1rem;
        }

        .tab-button:hover {
            color: #4f46e5;
        }

        .tab-button.active {
            background-color: #ffffff;
            color: #1f2937;
            font-weight: 600;
            z-index: 10;
            border-top-left-radius: 1.5rem;
            border-top-right-radius: 1.5rem;
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
            border-top: 8px solid #bfff00;
            padding-top: calc(0.5rem - 8px);
            padding-bottom: calc(0.5rem - 2px);
            margin-bottom: -4px;
            transition: all 0.3s ease-in-out;
        }

        /* Order List Row Hover Effect (from previous context) */
        .hoverable-row {
            cursor: pointer;
            transition: background-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
            border-radius: 0.25rem;
        }

        .hoverable-row:hover {
            background-color: #f8f9fa;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.175);
            transform: translateY(-2px);
        }

        /* Status Badge Styling (from previous context) */
        .status-badge {
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 500;
            display: inline-block;
        }

        .status-badge-fixed {
            min-width: 90px;
            text-align: center;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        /* Global Table Cell Styling for Text Wrapping (from previous context) */
        .tableHeader .col,
        .order-row .col {
            word-break: break-word;
            white-space: normal;
        }

        .tableHeader .col {
            font-size: 14px;
        }

        .order-row .col {
            font-size: 14px;
        }

        /* Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes scaleIn {
            from {
                transform: scale(0.95);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .order-list-fade-in {
            animation: fadeIn 0.5s ease-out;
        }

        .modal.fade .modal-dialog {
            transition: transform 0.3s ease-out, opacity 0.3s ease-out;
            transform: scale(0.9);
            opacity: 0;
        }

        .modal.fade.show .modal-dialog {
            transform: scale(1);
            opacity: 1;
        }

        /* Responsive Adjustments for Mobile (max-width: 767px) */
        @media (max-width: 767px) {
            .tabs-wrapper {
                display: none !important;
            }

            .tabs-dropdown-wrapper {
                display: block !important;
            }

            .tabs-dropdown-wrapper select {
                border-color: #bfff00 !important;
                border-width: 2px !important;
            }

            .tableHeader .col,
            .order-row .col {
                font-size: 12px !important;
                padding-left: 0.3rem;
                padding-right: 0.3rem;
                white-space: normal !important;
            }

            .tableHeader .col:first-child {
                padding-left: 0.8rem !important;
            }

            .order-row .title-col {
                padding-left: 0.8rem !important;
            }

            .order-row .badge {
                font-size: 12px !important;
                padding: .2em .4em !important;
            }

            .status-badge-fixed {
                min-width: 70px !important;
                text-align: center;
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }

            .order-row .col.m-0.p-0 {
                margin: 0 !important;
            }

            .modal-body p {
                font-size: 14px !important;
            }

            .modal-body .fw-medium {
                font-size: 16px !important;
            }

            .modal-header h5 {
                font-size: 20px !important;
            }

            .modal-body label {
                font-size: 14px !important;
            }

            .modal-body textarea {
                font-size: 14px !important;
            }

            .modal-footer button {
                font-size: 14px !important;
            }

            #reviewSectionHeading {
                font-size: 18px !important;
            }

            #modalInvoiceLink .ms-2 {
                font-size: 16px !important;
            }

            .star-rating {
                font-size: 28px !important;
            }
        }

        /* Specific Adjustments for Smaller Screens (max-width: 500px) */
        @media (max-width: 500px) {

            .tableHeader .col,
            .order-row .col {
                padding-left: 0.6rem !important;
                padding-right: 0.6rem !important;
            }

            .tableHeader .col:first-child {
                padding-left: 0.6rem !important;
            }

            .order-row .title-col {
                padding-left: 0.6rem !important;
            }

            .order-row .badge {
                font-size: 10px !important;
            }

            .status-badge-fixed {
                min-width: 60px !important;
            }

            .modal-body p,
            .modal-body label,
            .modal-body textarea {
                font-size: 12px !important;
            }

            .modal-body .fw-medium {
                font-size: 14px !important;
            }

            .modal-header h5 {
                font-size: 18px !important;
            }

            #reviewSectionHeading {
                font-size: 16px !important;
            }

            #modalInvoiceLink .ms-2 {
                font-size: 14px !important;
            }

            .star-rating {
                font-size: 24px !important;
            }
        }

        /* Specific Adjustments for Very Small Screens (max-width: 433px) */
        @media (max-width: 440px) {

            .tableHeader .col,
            .order-row .col {
                font-size: 11px !important;
                padding-left: 0.4rem !important;
                padding-right: 0.4rem !important;
            }

            .tableHeader .col:first-child {
                padding-left: 0.4rem !important;
            }

            .order-row .title-col {
                padding-left: 0.4rem !important;
            }

            .order-row .badge {
                font-size: 9px !important;
            }

            .modal-body p,
            .modal-body label,
            .modal-body textarea {
                font-size: 12px !important;
            }

            .modal-body .fw-medium {
                font-size: 14px !important;
            }

            .modal-header h5 {
                font-size: 18px !important;
            }

            #reviewSectionHeading {
                font-size: 16px !important;
            }

            #modalInvoiceLink .ms-2 {
                font-size: 14px !important;
            }

            .star-rating {
                font-size: 24px !important;
            }
        }

        /* Desktop Specific Styles (min-width: 768px) */
        @media (min-width: 768px) {
            .tabs-wrapper {
                display: flex !important;
            }

            .tabs-dropdown-wrapper {
                display: none !important;
            }

            .tableHeader .col {
                font-size: 16px;
            }

            .tableHeader .col:first-child {
                padding-left: 1rem;
            }

            .order-row .col {
                font-size: 14px;
            }

            .order-row .title-col {
                padding-left: 1rem;
            }

            .order-row .badge {
                font-size: 14px;
            }

            .modal-body p,
            .modal-body label,
            .modal-body textarea {
                font-size: 16px;
            }

            .modal-body .fw-medium {
                font-size: 18px;
            }

            .modal-header h5 {
                font-size: 24px;
            }

            #reviewSectionHeading {
                font-size: 22px;
            }

            #modalInvoiceLink .ms-2 {
                font-size: 18px;
            }

            .star-rating {
                font-size: 32px;
            }
        }
    </style>
@endsection
