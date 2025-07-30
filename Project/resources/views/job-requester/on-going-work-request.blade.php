@extends('master.master-job-req')

@section('content')
    @php
        // Prepare data for display and JavaScript usage
        $startTimeDetail = $request->start_time->format('d M Y, H:i');
        $endTimeDetail = $request->end_time->format('d M Y, H:i');

        $start = new DateTime($request->start_time);
        $end = new DateTime($request->end_time);
        $interval = $start->diff($end);
        $days = $interval->d;
        $hours = $interval->h;
        $minutes = $interval->i;
        $total_hours = $days * 24 + $hours;

        // Format durasi baru
        $duration = $total_hours . ' ' . __('ongoing.jam') . ' ' . $minutes . ' ' . __('ongoing.menit'); // <-- GANTI DENGAN INI

        $amount = $request->final_price;
        $formattedAmount = 'Rp ' . number_format($amount, 2, ',', '.');

        $alamatEncoded = urlencode($request->location);
        $mapsLink = "https://www.google.com/maps/search/?api=1&query={$alamatEncoded}";

        $workerCreatedAtYear = date('F Y', strtotime($worker->created_at));

        $transactionStartWorkFormatted = $transaction->start_work
            ? \Carbon\Carbon::parse($transaction->start_work)->format('d M Y H:i')
            : '-';
        $transactionFinishWorkFormatted = $transaction->finish_work
            ? \Carbon\Carbon::parse($transaction->finish_work)->format('d M Y H:i')
            : '-';

        $transactionCreatedAtFormatted = \Carbon\Carbon::parse($transaction->created_at)->format('d M Y');
        $transactionUpdatedAtFormatted = \Carbon\Carbon::parse($transaction->updated_at)->format('d M Y');

        // Fetch existing review for the current transaction by the authenticated user (requester)
        $existingReview = App\Models\Review::where('transaction_id', $transaction->id)
            ->where('reviewer_id', Auth::id())
            ->first();
    @endphp

    <div class="container-fluid pembatas-x mb-5">
        <div class="row mb-3 p-1">
            <div class="col-12 mb-2 mt-5">
                <h2 style="font-weight: 800;">{{ __('ongoing.pesanan_kamu') }}</h2>
            </div>
            <div class="col-12 contain bg-light mt-2 px-4 py-3 rounded-4 d-flex align-items-center"
                style="border: 1px solid #cacadd;">
                @if ($transaction->status == 'submitted')
                    <div class="badge px-4 py-3 rounded-pill bg-primary text-light fs-6" style="background-color:#294287;">
                        {{ __('ongoing.status_ditinjau') }}</div>
                @elseif($transaction->status == 'cancelled')
                    <div class="badge px-4 py-3 rounded-pill bg-warning text-light fs-6" style="background-color:crimson;">
                        {{ __('ongoing.status_dibatalkan') }}</div>
                @elseif($transaction->status == 'accepted')
                    <div class="badge px-4 py-3 rounded-pill bg-warning text-light fs-6" style="background-color:#294287;">
                        {{ __('ongoing.status_diterima') }}</div>
                @elseif($transaction->status == 'in progress')
                    <div class="badge px-4 py-3 rounded-5 bg-info text-light fs-6" style="background-color:#309FFF;">
                        {{ __('ongoing.status_dikerjakan') }}</div>
                @elseif($transaction->status == 'completed')
                    <div class="badge px-4 py-3 rounded-pill bg-success text-dark fs-6" style="background-color:#D3FA0D;">
                        {{ __('ongoing.status_selesai') }}</div>
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
                            <div class="fw-bold text-start">{{ __('ongoing.deskripsi') }}</div>
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
                                        {{-- Path for calendar body --}}
                                        <path
                                            d="M19.5 3.75H4.5C3.80964 3.75 3.14707 4.02656 2.65165 4.52198C2.15623 5.01739 1.875 5.67996 1.875 6.375V19.5C1.875 20.1904 2.15623 20.8529 2.65165 21.3483C3.14707 21.8437 3.80964 22.125 4.5 22.125H19.5C20.1904 22.125 20.8529 21.8437 21.3483 21.3483C21.8437 20.8529 22.125 20.1904 22.125 19.5V6.375C22.125 5.67996 21.8437 5.01739 21.3483 4.52198C20.8529 4.02656 20.1904 3.75 19.5 3.75ZM15.75 1.875V5.625M8.25 1.875V5.625M1.875 9.375H22.125"
                                            stroke="#133E87" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        {{-- Path for dots inside calendar --}}
                                        <path
                                            d="M14.875 14.875C14.875 15.1071 14.7828 15.3296 14.6187 15.4937C14.4546 15.6578 14.2321 15.75 14 15.75C13.7679 15.75 13.5454 15.6578 13.3813 15.4937C13.2172 15.3296 13.125 15.1071 13.125 14.875C13.125 14.6429 13.2172 14.4204 13.3813 14.2563C13.5454 14.0922 13.7679 14 14 14C14.2321 14 14.4546 14.0922 14.6187 14.2563C14.7828 14.4204 14.875 14.6429 14.875 14.875ZM8.75 18.375C8.98206 18.375 9.20462 18.2828 9.36872 18.1187C9.53281 17.9546 9.625 17.7321 9.625 17.5C9.625 17.2679 9.53281 17.0454 9.36872 16.8813C9.20462 16.7172 8.98206 16.625 8.75 16.625C8.51794 16.625 8.29538 16.7172 8.13128 16.8813C7.96719 17.0454 7.875 17.2679 7.875 17.5C7.875 17.7321 7.96719 17.9546 8.13128 18.1187C8.29538 18.2828 8.51794 18.375 8.75 18.375ZM9.625 20.125C9.625 20.3571 9.53281 20.5796 9.36872 20.7437C9.20462 20.9078 8.98206 21 8.75 21C8.51794 21 8.29538 20.9078 8.13128 20.7437C7.96719 20.5796 7.875 20.3571 7.875 20.125C7.875 19.8929 7.96719 19.6704 8.13128 19.5063C8.29538 19.3422 8.51794 19.25 8.75 19.25C8.98206 19.25 9.20462 19.3422 9.36872 19.5063C9.53281 19.6704 9.625 19.8929 9.625 20.125ZM11.375 18.375C11.6071 18.375 11.8296 18.2828 11.9937 18.1187C12.1578 17.9546 12.25 17.7321 12.25 17.5C12.25 17.2679 12.1578 17.0454 11.9937 16.8813C11.8296 16.7172 11.6071 16.625 11.375 16.625C11.1429 16.625 10.9204 16.7172 10.7563 16.8813C10.5922 17.0454 10.5 17.2679 10.5 17.5C10.5 17.7321 10.5922 17.9546 10.7563 18.1187C10.9204 18.2828 11.1429 18.375 11.375 18.375ZM12.25 20.125C12.25 20.3571 12.1578 20.5796 11.9937 20.7437C11.8296 20.9078 11.6071 21 11.375 21C11.1429 21 10.9204 20.9078 10.7563 20.7437C10.5922 20.5796 10.5 20.3571 10.5 20.125C10.5 19.8929 10.5922 19.6704 10.7563 19.5063C10.9204 19.3422 11.1429 19.25 11.375 19.25C11.6071 19.25 11.8296 19.3422 11.9937 19.5063C12.1578 19.6704 12.25 19.8929 12.25 20.125ZM14 18.375C14.2321 18.375 14.4546 18.2828 14.6187 18.1187C14.7828 17.9546 14.875 17.7321 14.875 17.5C14.875 17.2679 14.7828 17.0454 14.6187 16.8813C14.4546 16.7172 14.2321 16.625 14 16.625C13.7679 16.625 13.5454 16.7172 13.3813 16.8813C13.2172 17.0454 13.125 17.2679 13.125 17.5C13.125 17.7321 13.2172 17.9546 13.3813 18.1187C13.5454 18.2828 13.7679 18.375 14 18.375ZM14.875 20.125C14.875 20.3571 14.7828 20.5796 14.6187 20.7437C14.4546 20.9078 14.2321 21 14 21C13.7679 21 13.5454 20.9078 13.3813 20.7437C13.2172 20.5796 13.125 20.3571 13.125 20.125C13.125 19.8929 13.2172 19.6704 13.3813 19.5063C13.5454 19.3422 13.7679 19.25 14 19.25C14.2321 19.25 14.4546 19.3422 14.6187 19.5063C14.7828 19.6704 14.875 19.8929 14.875 20.125ZM16.625 18.375C16.8571 18.375 17.0796 18.2828 17.2437 18.1187C17.4078 17.9546 17.5 17.7321 17.5 17.5C17.5 17.2679 17.4078 17.0454 17.2437 16.8813C17.0796 16.7172 16.8571 16.625 16.625 16.625C16.3929 16.625 16.1704 16.7172 16.0063 16.8813C15.8422 17.0454 15.75 17.2679 15.75 17.5C15.75 17.7321 15.8422 17.9546 16.0063 18.1187C16.1704 18.2828 16.3929 18.375 16.625 18.375ZM17.5 20.125C17.5 20.3571 17.4078 20.5796 17.2437 20.7437C17.0796 20.9078 16.8571 21 16.625 21C16.3929 21 16.1704 20.9078 16.0063 20.7437C15.8422 20.5796 15.75 20.3571 15.75 20.125C15.75 19.8929 15.8422 19.6704 16.0063 19.5063C16.1704 19.3422 16.3929 19.25 16.625 19.25C16.8571 19.25 17.0796 19.3422 17.2437 19.5063C17.4078 19.6704 17.5 19.8929 17.5 20.125ZM19.25 18.375C19.4821 18.375 19.7046 18.2828 19.8687 18.1187C20.0328 17.9546 20.125 17.7321 20.125 17.5C20.125 17.2679 20.0328 17.0454 19.8687 16.8813C19.7046 16.7172 19.4821 16.625 19.25 16.625C19.0179 16.625 18.7954 16.7172 18.6313 16.8813C18.4672 17.0454 18.375 17.2679 18.375 17.5C18.375 17.7321 18.4672 17.9546 18.6313 18.1187C18.7954 18.2828 19.0179 18.375 19.25 18.375ZM17.5 14.875C17.5 15.1071 17.4078 15.3296 17.2437 15.4937C17.0796 15.6578 16.8571 15.75 16.625 15.75C16.3929 15.75 16.1704 15.6578 16.0063 15.4937C15.8422 15.3296 15.75 15.1071 15.75 14.875C15.75 14.6429 15.8422 14.4204 16.0063 14.2563C16.1704 14.0922 16.3929 14 16.625 14C16.8571 14 17.0796 14.0922 17.2437 14.2563C17.4078 14.4204 17.5 14.6429 17.5 14.875ZM19.25 15.75C19.4821 15.75 19.7046 15.6578 19.8687 15.4937C20.0328 15.3296 20.125 15.1071 20.125 14.875C20.125 14.6429 20.0328 14.4204 19.8687 14.2563C19.7046 14.0922 19.4821 14 19.25 14C19.0179 14 18.7954 14.0922 18.6313 14.2563C18.4672 14.4204 18.375 14.6429 18.375 14.875C18.375 15.1071 18.4672 15.3296 18.6313 15.4937C18.7954 15.6578 19.0179 15.75 19.25 15.75Z"
                                            fill="#133E87" />
                                    </svg>

                                    <div class="p-2">{{ __('ongoing.mulai') }}</div>
                                </div>
                                <div class="py-2 fw-bold text-end">{{ $startTimeDetail }}</div>
                            </div>
                            <div class="d-flex flex-fill justify-content-between" style="width: 100%;">
                                <div class="separate d-flex align-items-center">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        {{-- Path for calendar body --}}
                                        <path
                                            d="M19.5 3.75H4.5C3.80964 3.75 3.14707 4.02656 2.65165 4.52198C2.15623 5.01739 1.875 5.67996 1.875 6.375V19.5C1.875 20.1904 2.15623 20.8529 2.65165 21.3483C3.14707 21.8437 3.80964 22.125 4.5 22.125H19.5C20.1904 22.125 20.8529 21.8437 21.3483 21.3483C21.8437 20.8529 22.125 20.1904 22.125 19.5V6.375C22.125 5.67996 21.8437 5.01739 21.3483 4.52198C20.8529 4.02656 20.1904 3.75 19.5 3.75ZM15.75 1.875V5.625M8.25 1.875V5.625M1.875 9.375H22.125"
                                            stroke="#133E87" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        {{-- Path for dots inside calendar --}}
                                        <path
                                            d="M14.875 14.875C14.875 15.1071 14.7828 15.3296 14.6187 15.4937C14.4546 15.6578 14.2321 15.75 14 15.75C13.7679 15.75 13.5454 15.6578 13.3813 15.4937C13.2172 15.3296 13.125 15.1071 13.125 14.875C13.125 14.6429 13.2172 14.4204 13.3813 14.2563C13.5454 14.0922 13.7679 14 14 14C14.2321 14 14.4546 14.0922 14.6187 14.2563C14.7828 14.4204 14.875 14.6429 14.875 14.875ZM8.75 18.375C8.98206 18.375 9.20462 18.2828 9.36872 18.1187C9.53281 17.9546 9.625 17.7321 9.625 17.5C9.625 17.2679 9.53281 17.0454 9.36872 16.8813C9.20462 16.7172 8.98206 16.625 8.75 16.625C8.51794 16.625 8.29538 16.7172 8.13128 16.8813C7.96719 17.0454 7.875 17.2679 7.875 17.5C7.875 17.7321 7.96719 17.9546 8.13128 18.1187C8.29538 18.2828 8.51794 18.375 8.75 18.375ZM9.625 20.125C9.625 20.3571 9.53281 20.5796 9.36872 20.7437C9.20462 20.9078 8.98206 21 8.75 21C8.51794 21 8.29538 20.9078 8.13128 20.7437C7.96719 20.5796 7.875 20.3571 7.875 20.125C7.875 19.8929 7.96719 19.6704 8.13128 19.5063C8.29538 19.3422 8.51794 19.25 8.75 19.25C8.98206 19.25 9.20462 19.3422 9.36872 19.5063C9.53281 19.6704 9.625 19.8929 9.625 20.125ZM11.375 18.375C11.6071 18.375 11.8296 18.2828 11.9937 18.1187C12.1578 17.9546 12.25 17.7321 12.25 17.5C12.25 17.2679 12.1578 17.0454 11.9937 16.8813C11.8296 16.7172 11.6071 16.625 11.375 16.625C11.1429 16.625 10.9204 16.7172 10.7563 16.8813C10.5922 17.0454 10.5 17.2679 10.5 17.5C10.5 17.7321 10.5922 17.9546 10.7563 18.1187C10.9204 18.2828 11.1429 18.375 11.375 18.375ZM12.25 20.125C12.25 20.3571 12.1578 20.5796 11.9937 20.7437C11.8296 20.9078 11.6071 21 11.375 21C11.1429 21 10.9204 20.9078 10.7563 20.7437C10.5922 20.5796 10.5 20.3571 10.5 20.125C10.5 19.8929 10.5922 19.6704 10.7563 19.5063C10.9204 19.3422 11.1429 19.25 11.375 19.25C11.6071 19.25 11.8296 19.3422 11.9937 19.5063C12.1578 19.6704 12.25 19.8929 12.25 20.125ZM14 18.375C14.2321 18.375 14.4546 18.2828 14.6187 18.1187C14.7828 17.9546 14.875 17.7321 14.875 17.5C14.875 17.2679 14.7828 17.0454 14.6187 16.8813C14.4546 16.7172 14.2321 16.625 14 16.625C13.7679 16.625 13.5454 16.7172 13.3813 16.8813C13.2172 17.0454 13.125 17.2679 13.125 17.5C13.125 17.7321 13.2172 17.9546 13.3813 18.1187C13.5454 18.2828 13.7679 18.375 14 18.375ZM14.875 20.125C14.875 20.3571 14.7828 20.5796 14.6187 20.7437C14.4546 20.9078 14.2321 21 14 21C13.7679 21 13.5454 20.9078 13.3813 20.7437C13.2172 20.5796 13.125 20.3571 13.125 20.125C13.125 19.8929 13.2172 19.6704 13.3813 19.5063C13.5454 19.3422 13.7679 19.25 14 19.25C14.2321 19.25 14.4546 19.3422 14.6187 19.5063C14.7828 19.6704 14.875 19.8929 14.875 20.125ZM16.625 18.375C16.8571 18.375 17.0796 18.2828 17.2437 18.1187C17.4078 17.9546 17.5 17.7321 17.5 17.5C17.5 17.2679 17.4078 17.0454 17.2437 16.8813C17.0796 16.7172 16.8571 16.625 16.625 16.625C16.3929 16.625 16.1704 16.7172 16.0063 16.8813C15.8422 17.0454 15.75 17.2679 15.75 17.5C15.75 17.7321 15.8422 17.9546 16.0063 18.1187C16.1704 18.2828 16.3929 18.375 16.625 18.375ZM17.5 20.125C17.5 20.3571 17.4078 20.5796 17.2437 20.7437C17.0796 20.9078 16.8571 21 16.625 21C16.3929 21 16.1704 20.9078 16.0063 20.7437C15.8422 20.5796 15.75 20.3571 15.75 20.125C15.75 19.8929 15.8422 19.6704 16.0063 19.5063C16.1704 19.3422 16.3929 19.25 16.625 19.25C16.8571 19.25 17.0796 19.3422 17.2437 19.5063C17.4078 19.6704 17.5 19.8929 17.5 20.125ZM19.25 18.375C19.4821 18.375 19.7046 18.2828 19.8687 18.1187C20.0328 17.9546 20.125 17.7321 20.125 17.5C20.125 17.2679 20.0328 17.0454 19.8687 16.8813C19.7046 16.7172 19.4821 16.625 19.25 16.625C19.0179 16.625 18.7954 16.7172 18.6313 16.8813C18.4672 17.0454 18.375 17.2679 18.375 17.5C18.375 17.7321 18.4672 17.9546 18.6313 18.1187C18.7954 18.2828 19.0179 18.375 19.25 18.375ZM17.5 14.875C17.5 15.1071 17.4078 15.3296 17.2437 15.4937C17.0796 15.6578 16.8571 15.75 16.625 15.75C16.3929 15.75 16.1704 15.6578 16.0063 15.4937C15.8422 15.3296 15.75 15.1071 15.75 14.875C15.75 14.6429 15.8422 14.4204 16.0063 14.2563C16.1704 14.0922 16.3929 14 16.625 14C16.8571 14 17.0796 14.0922 17.2437 14.2563C17.4078 14.4204 17.5 14.6429 17.5 14.875ZM19.25 15.75C19.4821 15.75 19.7046 15.6578 19.8687 15.4937C20.0328 15.3296 20.125 15.1071 20.125 14.875C20.125 14.6429 20.0328 14.4204 19.8687 14.2563C19.7046 14.0922 19.4821 14 19.25 14C19.0179 14 18.7954 14.0922 18.6313 14.2563C18.4672 14.4204 18.375 14.6429 18.375 14.875C18.375 15.1071 18.4672 15.3296 18.6313 15.4937C18.7954 15.6578 19.0179 15.75 19.25 15.75Z"
                                            fill="#133E87" />
                                    </svg>

                                    <div class="p-2">{{ __('ongoing.selesai') }}</div>
                                </div>
                                <div class="py-2 fw-bold text-end">{{ $endTimeDetail }}</div>
                            </div>
                            <div class="d-flex flex-fill justify-content-between" style="width: 100%;">
                                <div class="separate d-flex align-items-center">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M12 2.25C6.615 2.25 2.25 6.615 2.25 12C2.25 17.385 6.615 21.75 12 21.75C17.385 21.75 21.75 17.385 21.75 12C21.75 6.615 17.385 2.25 12 2.25ZM12.75 6C12.75 5.80109 12.671 5.61032 12.5303 5.46967C12.3897 5.32902 12.1989 5.25 12 5.25C11.8011 5.25 11.6103 5.32902 11.4697 5.46967C11.329 5.61032 11.25 5.80109 11.25 6V12C11.25 12.414 11.586 12.75 12 12.75H16.5C16.6989 12.75 16.8897 12.671 17.0303 12.5303C17.171 12.3897 17.25 12.1989 17.25 12C17.25 11.8011 17.171 11.6103 17.0303 11.4697C16.8897 11.329 16.6989 11.25 16.5 11.25H12.75V6Z"
                                            fill="#133E87" />
                                    </svg>

                                    <div class="p-2">{{ __('ongoing.durasi') }}</div>
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

                                    <div class="p-2">{{ __('ongoing.upah') }}</div>
                                </div>
                                <div class="py-2 fw-bold text-end">{{ $formattedAmount }}</div>
                            </div>
                            <div class="d-flex flex-fill justify-content-between" style="width: 100%;">
                                <div class="separate d-flex align-items-center">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M11.54 22.351L11.61 22.391L11.638 22.407C11.749 22.467 11.8733 22.4985 11.9995 22.4985C12.1257 22.4985 12.25 22.467 12.361 22.407L12.389 22.392L12.46 22.351C12.8511 22.1191 13.2328 21.8716 13.604 21.609C14.5651 20.9305 15.463 20.1667 16.287 19.327C18.231 17.337 20.25 14.347 20.25 10.5C20.25 8.31196 19.3808 6.21354 17.8336 4.66637C16.2865 3.11919 14.188 2.25 12 2.25C9.81196 2.25 7.71354 3.11919 6.16637 4.66637C4.61919 6.21354 3.75 8.31196 3.75 10.5C3.75 14.346 5.77 17.337 7.713 19.327C8.53664 20.1667 9.43427 20.9304 10.395 21.609C10.7666 21.8716 11.1485 22.1191 11.54 22.351ZM12 13.5C12.7956 13.5 13.5587 13.1839 14.1213 12.6213C14.6839 12.0587 15 11.2956 15 10.5C15 9.70435 14.6839 8.94129 14.1213 8.37868C13.5587 7.81607 12.7956 7.5 12 7.5C11.2044 7.5 10.4413 7.81607 9.87868 8.37868C9.31607 8.94129 9 9.70435 9 10.5C9 11.2956 9.31607 12.0587 9.87868 12.6213C10.4413 13.1839 11.2044 13.5 12 13.5Z"
                                            fill="#133E87" />
                                    </svg>

                                    <div class="p-2">{{ __('ongoing.lokasi') }}</div>
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
                        <div class="contain bg-light px-4 py-3 rounded-4 d-flex flex-fill align-items-center justify-content-between"
                            style="border: 1px solid #cacadd; max-height: 150px; height:100%;">
                            <img src="{{ $transaction->worker->photo_url_worker ? asset($transaction->worker->photo_url_worker) : asset('Image/Icon/user-circle.svg') }}"
                                alt="Profil"
                                style="width:40%; max-height: 125px; object-fit: cover; border-radius: 16px;"
                                class="me-3 py-2">
                            <div class="info d-flex flex-column">
                                <div id="name" class="fw-bold">{{ $worker->first_name . ' ' . $worker->last_name }}
                                </div>
                                <div id="rating" class="d-flex">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M8.99008 2.67508C9.36342 1.77758 10.6368 1.77758 11.0101 2.67508L12.7451 6.84675L17.2484 7.20841C18.2184 7.28591 18.6118 8.49591 17.8726 9.12924L14.4418 12.0684L15.4893 16.4626C15.7151 17.4092 14.6859 18.1567 13.8559 17.6501L10.0001 15.2951L6.14425 17.6501C5.31425 18.1567 4.28508 17.4084 4.51092 16.4626L5.55842 12.0684L2.12758 9.12924C1.38842 8.49591 1.78175 7.28591 2.75175 7.20841L7.25508 6.84675L8.99008 2.67508Z"
                                            fill="#FFDD00" />
                                    </svg>
                                    <div class="rate fw-semibold">{{ $worker->rating }}</div>
                                    <div class="banyak">
                                        ({{ \App\Models\Transaction::where('worker_id', $worker->id)->where('status', 'completed')->count() }})
                                    </div>
                                </div>
                                <div id="join">
                                    <p style="font-size: 9px;">{{ __('ongoing.bergabung_sejak') }}
                                        <span>{{ $workerCreatedAtYear }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-12 px-0 pe-lg-2 statb">
                        <div class="contain bg-light px-4 py-3 rounded-4 d-flex flex-column align-items-center justify-content-center"
                            style="border: 1px solid #cacadd; height:100%;">
                            {{-- This section will be dynamically updated by JavaScript --}}
                            @if ($transaction->status == 'submitted')
                                <button type="button" class="btn px-4 py-2 rounded-pill text-light fs-4 fw-bold"
                                    data-bs-toggle="modal" data-bs-target="#completeJobModal" id="completeJobBtn"
                                    style="background-color:#294287; width:88%;">
                                    {{ __('ongoing.tombol_tandai_selesai') }}
                                </button>
                                <a class="btn px-4 py-2 rounded-5 d-inline fw-semibold fs-5" href="#"
                                    data-bs-toggle="modal" data-bs-target="#completionProofModal"
                                    style="color: #0d6efd; text-decoration: none;">
                                    {{ __('ongoing.tombol_lihat_bukti') }}
                                </a>
                            @elseif($transaction->status == 'completed')
                                <a class="btn btn-success px-4 py-2 rounded-pill fs-4 fw-bold"
                                    style="width:88%;cursor: not-allowed;pointer-events: none;">
                                    {{ __('ongoing.status_selesai') }}
                                </a>
                                <a class="btn px-4 py-2 rounded-5 d-inline fw-semibold fs-5" href="#"
                                    data-bs-toggle="modal" data-bs-target="#completionProofModal"
                                    style="color: #0d6efd; text-decoration: none;">
                                    {{ __('ongoing.tombol_lihat_bukti') }}
                                </a>
                            @elseif($transaction->status == 'in progress')
                                <div class="px-4 py-2 rounded-5 d-inline fw-semibold text-light fs-4 text-center"
                                    style="background-color:#9d9d9d; width:88%;">
                                    {{ __('ongoing.tombol_tandai_selesai') }}</div>
                                <a class="btn px-4 py-2 rounded-5 d-inline fw-semibold fs-5" href="#"
                                    style="color: #a7a7a7; text-decoration: none; cursor: not-allowed; pointer-events: none;">
                                    {{ __('ongoing.tombol_lihat_bukti') }}
                                </a>
                            @elseif($transaction->status == 'accepted')
                                <div class="px-4 py-2 rounded-5 d-inline fw-semibold text-light fs-4 text-center"
                                    style="background-color:#9d9d9d; width:88%;">
                                    {{ __('ongoing.tombol_tandai_selesai') }}</div>
                                <div class="btn px-4 py-2 rounded-5 d-inline fw-semibold text-danger fs-5"
                                    data-bs-toggle="modal" data-bs-target="#cancelWorkModal">
                                    {{ __('ongoing.tombol_batalkan_kerja') }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @livewire('job-requester.chat-work', ['selectedRoomId' => $room->id])
        </div>
    </div>


    <div class="modal fade" id="completeJobModal" tabindex="-1" aria-labelledby="completeJobModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form id="markCompleteForm" action="{{ route('transaction.markComplete', $transaction->id) }}"
                method="POST">
                @csrf
                <div class="modal-content p-4">
                    <div class="d-flex flex-md-row align-items-center justify-content-center text-center text-md-start">
                        <svg width="42" height="42" viewBox="0 0 42 42" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M0.6875 21C0.6875 9.78125 9.78125 0.6875 21 0.6875C32.2188 0.6875 41.3125 9.78125 41.3125 21C41.3125 32.2188 32.2188 41.3125 21 41.3125C9.78125 41.3125 0.6875 32.2188 0.6875 21ZM21 13.1875C21.4144 13.1875 21.8118 13.3521 22.1049 13.6451C22.3979 13.9382 22.5625 14.3356 22.5625 14.75V22.5625C22.5625 22.9769 22.3979 23.3743 22.1049 23.6674C21.8118 23.9604 21.4144 24.125 21 24.125C20.5856 24.125 20.1882 23.9604 19.8951 23.6674C19.6021 23.3743 19.4375 22.9769 19.4375 22.5625V14.75C19.4375 14.3356 19.6021 13.9382 19.8951 13.6451C20.1882 13.3521 20.5856 13.1875 21 13.1875ZM21 30.375C21.4144 30.375 21.8118 30.2104 22.1049 29.9174C22.3979 29.6243 22.5625 29.2269 22.5625 28.8125C22.5625 28.3981 22.3979 28.0007 22.1049 27.7076C21.8118 27.4146 21.4144 27.25 21 27.25C20.5856 27.25 20.1882 27.4146 19.8951 27.7076C19.6021 28.0007 19.4375 28.3981 19.4375 28.8125C19.4375 29.2269 19.6021 29.6243 19.8951 29.9174C20.1882 30.2104 20.5856 30.375 21 30.375Z"
                                fill="#D3FA0D" />
                        </svg>
                        <h4 class="mx-3 fw-bold fs-2 mt-1" style="color:#309FFF;">{{ __('ongoing.modal_selesai_judul') }}
                        </h4>
                        <svg width="42" height="42" viewBox="0 0 42 42" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M0.6875 21C0.6875 9.78125 9.78125 0.6875 21 0.6875C32.2188 0.6875 41.3125 9.78125 41.3125 21C41.3125 32.2188 32.2188 41.3125 21 41.3125C9.78125 41.3125 0.6875 32.2188 0.6875 21ZM21 13.1875C21.4144 13.1875 21.8118 13.3521 22.1049 13.6451C22.3979 13.9382 22.5625 14.3356 22.5625 14.75V22.5625C22.5625 22.9769 22.3979 23.3743 22.1049 23.6674C21.8118 23.9604 21.4144 24.125 21 24.125C20.5856 24.125 20.1882 23.9604 19.8951 23.6674C19.6021 23.3743 19.4375 22.9769 19.4375 22.5625V14.75C19.4375 14.3356 19.6021 13.9382 19.8951 13.6451C20.1882 13.3521 20.5856 13.1875 21 13.1875ZM21 30.375C21.4144 30.375 21.8118 30.2104 22.1049 29.9174C22.3979 29.6243 22.5625 29.2269 22.5625 28.8125C22.5625 28.3981 22.3979 28.0007 22.1049 27.7076C21.8118 27.4146 21.4144 27.25 21 27.25C20.5856 27.25 20.1882 27.4146 19.8951 27.7076C19.6021 28.0007 19.4375 28.3981 19.4375 28.8125C19.4375 29.2269 19.6021 29.6243 19.8951 29.9174C20.1882 30.2104 20.5856 30.375 21 30.375Z"
                                fill="#D3FA0D" />
                        </svg>
                    </div>
                    <div class="text-center my-4 fw-semibold" style="font-size: 16px;">
                        <div>{{ __('ongoing.modal_selesai_pesan1') }}</div>
                        <div>{{ __('ongoing.modal_selesai_pesan2') }}</div>
                    </div>
                    <div class="d-flex flex-column flex-md-row justify-content-center gap-2 gap-md-4 mt-4">
                        <button type="button" class="btn py-3 fw-semibold rounded-4"
                            style="color:#294287; border-color:#294287; border-width: 2px; width:100%;"
                            data-bs-dismiss="modal">
                            {{ __('ongoing.tombol_kembali') }}
                        </button>
                        <button type="button" id="completeWorkButton" class="btn py-3 text-light fw-semibold rounded-4"
                            style="background-color:#309FFF; width:100%;">
                            {{ __('ongoing.modal_selesai_tombol_konfirmasi') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="completionModal" tabindex="-1" aria-labelledby="completionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" style="max-width: 1000px; width: 100%; margin-top:5vh;">
            <form id="reviewForm" method="POST">
                @csrf
                <div class="modal-content p-3">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold fs-3" id="completionModalLabel">
                            {{ __('ongoing.modal_bukti_judul') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body overflow-auto overflow-lg-visible" style="max-height: 90vh;">
                        <div class="d-flex flex-column flex-lg-row gap-3">
                            {{-- Order details display section within the modal --}}
                            <div class="d-flex flex-column flex-grow-1">
                                <div class="d-flex flex-fill">
                                    <div class="text flex-fill" style="width:50%;">
                                        <p class="m-0 p-0 text-black-50 fw-semibold">
                                            {{ __('accepted.modal_penilaian.judul_pesanan') }}</p>
                                        <p class="fw-medium" id="modalRequestTitle"></p>
                                    </div>
                                    <div class="text" style="width:50%;">
                                        <p class="m-0 p-0 text-black-50 fw-semibold">
                                            {{ __('accepted.modal_penilaian.nomor_pesanan') }}</p>
                                        <p class="fw-medium" id="modalOrderNumber"></p>
                                    </div>
                                </div>
                                <div class="d-flex">
                                    <div class="text" style="width:50%;">
                                        <p class="m-0 p-0 text-black-50 fw-semibold">
                                            {{ __('ongoing.modal_penilaian.nama_klien') }}</p>
                                        <p class="fw-medium" id="modalWorkerName"></p>
                                    </div>
                                    <div class="text" style="width:50%;">
                                        <p class="m-0 p-0 text-black-50 fw-semibold">
                                            {{ __('accepted.modal_penilaian.lokasi') }}</p>
                                        <p class="fw-medium" id="modalRequestLocation"></p>
                                    </div>
                                </div>
                                <div class="d-flex flex-fill">
                                    <div class="text" style="width:50%;">
                                        <p class="m-0 p-0 text-black-50 fw-semibold">
                                            {{ __('accepted.modal_penilaian.tgl_pesan') }}</p>
                                        <p class="fw-medium" id="modalTransactionCreatedAt"></p>
                                    </div>
                                    <div class="text" style="width:50%;">
                                        <p class="m-0 p-0 text-black-50 fw-semibold">
                                            {{ __('accepted.modal_penilaian.tgl_selesai') }}</p>
                                        <p class="fw-medium" id="modalTransactionUpdatedAt"></p>
                                    </div>
                                </div>
                                <div class="d-flex">
                                    <div class="text" style="width:50%;">
                                        <p class="m-0 p-0 text-black-50 fw-semibold">
                                            {{ __('accepted.modal_penilaian.mulai_kerja') }}</p>
                                        <p class="fw-medium" id="modalStartWork"></p>
                                    </div>
                                    <div class="text" style="width:50%;">
                                        <p class="m-0 p-0 text-black-50 fw-semibold">
                                            {{ __('accepted.modal_penilaian.selesai_kerja') }}</p>
                                        <p class="fw-medium" id="modalFinishWork"></p>
                                    </div>
                                </div>
                                <hr class="my-1 border border-dark">
                                <div class="d-flex mt-1">
                                    <div class="text d-flex justify-content-between align-items-center"
                                        style="width:50%;">
                                        <p class="p-0 m-0 text-black-50 fw-semibold fs-6">Total</p>
                                        <p class="p-0 m-0 fw-medium fs-lg-6 text-end">Rp
                                            <span id="modalRequestPrice"></span>
                                        </p>
                                    </div>
                                    <div class="text d-flex justify-content-end align-items-center" style="width:50%;">
                                        <a href="#" id="modalInvoiceLink"
                                            class="d-flex text-decoration-none justify-content-center align-items-center">
                                            <svg width="17" height="17" viewBox="0 0 17 17" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M8.5029 12.668L3.29334 7.45843L4.75202 5.94766L7.46099 8.65663V0.165039H9.54482V8.65663L12.2538 5.94766L13.7125 7.45843L8.5029 12.668ZM2.25143 16.8356C1.67838 16.8356 1.18781 16.6316 0.779726 16.2235C0.371644 15.8154 0.167603 15.3249 0.167603 14.7518V11.6261H2.25143V14.7518H14.7544V11.6261H16.8382V14.7518C16.8382 15.3249 16.6342 15.8154 16.2261 16.2235C15.818 16.6316 15.3274 16.8356 14.7544 16.8356H2.25143Z"
                                                    fill="#294287" />
                                            </svg>
                                            <div class="ms-2 fw-medium fs-5">Invoice</div>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="vr d-none d-lg-block mx-3"></div>

                            {{-- Section for User Review and Report --}}
                            <div class="d-flex flex-column align-items-center justify-content-center flex-grow-1">
                                {{-- The heading will now be dynamic --}}
                                <h4 class="fw-semibold mt-3 mb-1" id="reviewSectionHeading"></h4>

                                {{-- Container for existing review or review form --}}
                                <div id="review-section-container" class="w-100">
                                    {{-- This content will be dynamically populated by JavaScript --}}
                                </div>

                                {{-- Action buttons for review submission and reporting --}}
                                <div class="d-flex flex-column mt-3 justify-content-center">
                                    <button type="submit" class="btn btn-primary fw-medium rounded-3"
                                        id="submitReviewButton">{{ __('accepted.modal_penilaian.tombol_kirim') }}</button>
                                    <div class="m-1 text-center">{{ __('accepted.modal_penilaian.atau') }}</div>
                                    <button type="button" class="m-0 p-0 fw-medium btn text-danger"
                                        onclick="openReportModal()"
                                        id="reportProblemButton">{{ __('accepted.modal_penilaian.judul') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="completionProofModal" tabindex="-1" aria-labelledby="completionProofModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" style="max-width: 800px; width: 100%; margin-top: 5vh;">
            <div class="modal-content p-3">
                <div class="modal-header">
                    <h3 class="modal-title text-center flex-fill fw-semibold" id="completionProofModalLabel">
                        {{ __('ongoing.modal_bukti_judul') }}
                    </h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body overflow-auto overflow-lg-visible" style="max-height: 85vh;">
                    <div class="d-flex flex-column flex-lg-row gap-3">

                        <div class="d-flex flex-column flex-grow-1">
                            <h6 class="fw-bold">{{ __('ongoing.modal_bukti_lampiran') }}</h6>
                            <div class="rounded-3 my-2 p-2 d-flex justify-content-center align-items-center"
                                style="border: 1px solid #8a8a8a; height: 40vh; background-color: #f9f9f9; overflow: hidden;">
                                @php
                                    // Decode string JSON into array
                                    $photoUrls = !empty($completionProof)
                                        ? json_decode($completionProof->photo_url, true)
                                        : [];
                                @endphp

                                @if (!empty($photoUrls) && isset($photoUrls[0]))
                                    {{-- Get the first image URL from the array --}}
                                    <img src="{{ $photoUrls[0] }}" alt="Bukti Foto"
                                        class="img-fluid h-100 w-100 rounded" style="object-fit: cover;">
                                @else
                                    <div
                                        class="text-center text-muted d-flex flex-column justify-content-center align-items-center">
                                        <svg width="80" height="80" viewBox="0 0 120 120" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M7.5 30C7.5 27.0163 8.68526 24.1548 10.795 22.045C12.9048 19.9353 15.7663 18.75 18.75 18.75H101.25C104.234 18.75 107.095 19.9353 109.205 22.045C111.315 24.1548 112.5 27.0163 112.5 30V90C112.5 92.9837 111.315 95.8452 109.205 97.9549C107.095 100.065 104.234 101.25 101.25 101.25H18.75C15.7663 101.25 12.9048 100.065 10.795 97.9549C8.68526 95.8452 7.5 92.9837 7.5 90V30ZM15 80.3V90C15 92.07 16.68 93.75 18.75 93.75H101.25C102.245 93.75 103.198 93.3549 103.902 92.6517C104.605 91.9484 105 90.9946 105 90V80.3L91.55 66.855C90.1437 65.4505 88.2375 64.6616 86.25 64.6616C84.2625 64.6616 82.3563 65.4505 80.95 66.855L76.55 71.25L81.4 76.1C81.7684 76.4433 82.0639 76.8573 82.2689 77.3173C82.4739 77.7773 82.5841 78.2739 82.593 78.7774C82.6018 79.2809 82.5092 79.781 82.3206 80.248C82.132 80.7149 81.8513 81.1391 81.4952 81.4952C81.1391 81.8513 80.7149 82.132 80.248 82.3206C79.781 82.5092 79.2809 82.6018 78.7774 82.593C78.2739 82.5841 77.7773 82.4739 77.3173 82.2689C76.8573 82.0639 76.4433 81.7684 76.1 81.4L50.3 55.605C48.8937 54.2005 46.9875 53.4116 45 53.4116C43.0125 53.4116 41.1063 54.2005 39.7 55.605L15 80.305V80.3ZM65.625 41.25C65.625 39.7582 66.2176 38.3274 67.2725 37.2725C68.3274 36.2176 69.7582 35.625 71.25 35.625C72.7418 35.625 74.1726 36.2176 75.2275 37.2725C76.2824 38.3274 76.875 39.7582 76.875 41.25C76.875 42.7418 76.2824 44.1726 75.2275 45.2275C74.1726 46.2824 72.7418 46.875 71.25 46.875C69.7582 46.875 68.3274 46.2824 67.2725 45.2275C66.2176 44.1726 65.625 42.7418 65.625 41.25Z"
                                                fill="#294287" />
                                        </svg>
                                        <div class="fw-semibold">{{ __('ongoing.modal_bukti_tidak_ada') }}</div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="vr d-none d-lg-block mx-3"></div>

                        <div class="d-flex flex-column flex-grow-1">
                            <h6 class="fw-bold">{{ __('ongoing.modal_bukti_catatan') }}</h6>
                            <div class="rounded-3 mt-2 mb-4 p-2"
                                style="border-color:#8a8a8a; border-style:solid; width:100%; height:64%; border-width:1px;">
                                <p class="m-0">
                                    {{ $completionProof->note ?? 'Tidak ada catatan dari pekerja.' }}</p>
                            </div>

                            <div class="flex-fill" style="width:100%; height:10%;">
                                <button type="button" class="flex-fill rounded-4 py-3 px-4 fw-bold bg-light"
                                    data-bs-dismiss="modal"
                                    style="border-width:2px; border-color:#294287; color:#294287; width:100%;">{{ __('ongoing.tombol_kembali') }}</button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
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
                    <h4 class="text-danger text-center fw-bold mb-0 mx-3 fs-4">{{ __('ongoing.modal_batal_judul') }}</h4>
                    <svg width="42" height="42" viewBox="0 0 42 42" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M0.6875 21C0.6875 9.78125 9.78125 0.6875 21 0.6875C32.2188 0.6875 41.3125 9.78125 41.3125 21C41.3125 32.2188 32.2188 41.3125 21 41.3125C9.78125 41.3125 0.6875 32.2188 0.6875 21ZM21 13.1875C21.4144 13.1875 21.8118 13.3521 22.1049 13.6451C22.3979 13.9382 22.5625 14.3356 22.5625 14.75V22.5625C22.5625 22.9769 22.3979 23.3743 22.1049 23.6674C21.8118 23.9604 21.4144 24.125 21 24.125C20.5856 24.125 20.1882 23.9604 19.8951 23.6674C19.6021 23.3743 19.4375 22.9769 19.4375 22.5625V14.75C19.4375 14.3356 19.6021 13.9382 19.8951 13.6451C20.1882 13.3521 20.5856 13.1875 21 13.1875ZM21 30.375C21.4144 30.375 21.8118 30.2104 22.1049 29.9174C22.3979 29.6243 22.5625 29.2269 22.5625 28.8125C22.5625 28.3981 22.3979 28.0007 22.1049 27.7076C21.8118 27.4146 21.4144 27.25 21 27.25C20.5856 27.25 20.1882 27.4146 19.8951 27.7076C19.6021 28.0007 19.4375 28.3981 19.4375 28.8125C19.4375 29.2269 19.6021 29.6243 19.8951 29.9174C20.1882 30.2104 20.5856 30.375 21 30.375Z"
                            fill="#B02A37" />
                    </svg>
                </div>

                <div class="text-center my-4 fw-semibold">
                    <div>{{ __('ongoing.modal_batal_pesan1') }}</div>
                    <div>{{ __('ongoing.modal_batal_pesan2') }}</div>
                </div>

                <form action="{{ route('transaction.cancel', $transaction->id) }}" method="POST"
                    class="d-flex flex-column flex-lg-row gap-3 mt-4">
                    @csrf
                    <input type="hidden" name="redirect_to" value="job-req.beranda">
                    <button type="button" class="btn btn-outline-primary rounded-4 flex-fill p-3 fw-semibold"
                        data-bs-dismiss="modal" style="border-width:2px;">
                        {{ __('ongoing.modal_batal_tombol_kembali') }}
                    </button>
                    <button type="submit" class="btn btn-danger rounded-4 flex-fill p-3 fw-semibold">
                        {{ __('ongoing.modal_batal_tombol_konfirmasi') }}
                    </button>
                </form>

            </div>
        </div>
    </div>

    <div class="modal fade" id="reportWorkModal" tabindex="-1" aria-labelledby="reportWorkModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg" style="max-width: 900px;">
            <div class="modal-content">
                <form id="reportForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    {{-- Hidden inputs for report submission data --}}
                    <input type="hidden" name="transaction_id" id="reportTransactionId">
                    <input type="hidden" name="reporter_id" value="{{ auth()->id() }}">
                    <input type="hidden" name="reported_id" id="reportReportedId">

                    <div class="modal-header border-0 justify-content-center">
                        <h3 class="modal-title fw-bold text-center w-100" id="reportWorkModalLabel">
                            {{ __('history-job-req.modal_laporan_judul') }}</h3>
                        <button type="button" class="btn-close position-absolute end-0 me-3" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <hr class="mx-auto mb-3" style="width: 50px; height: 4px; background-color: #294287; border: none;">

                    <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                        <div class="row mb-3">
                            <div class="col-md-6 col-lg-3">
                                <p class="text-black-50 fw-semibold mb-0">{{ __('history-job-req.modal_judul_pesanan') }}
                                </p>
                                <p class="fw-medium" id="reportModalRequestTitle"></p>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <p class="text-black-50 fw-semibold mb-0">{{ __('history-job-req.modal_nomor_pesanan') }}
                                </p>
                                <p class="fw-medium" id="reportModalOrderNumber"></p>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <p class="text-black-50 fw-semibold mb-0">
                                    {{ __('history-job-req.modal_laporan_nama_klien') }}</p>
                                <p class="fw-medium" id="reportModalRequesterName"></p>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <p class="text-black-50 fw-semibold mb-0">{{ __('history-job-req.modal_lokasi') }}</p>
                                <p class="fw-medium" id="reportModalRequestLocation"></p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6 col-lg-3">
                                <p class="text-black-50 fw-semibold mb-0">{{ __('history-job-req.modal_tgl_pesan') }}</p>
                                <p class="fw-medium" id="reportModalTransactionCreatedAt"></p>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <p class="text-black-50 fw-semibold mb-0">{{ __('history-job-req.modal_tgl_selesai') }}
                                </p>
                                <p class="fw-medium" id="reportModalTransactionUpdatedAt"></p>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <p class="text-black-50 fw-semibold mb-0">{{ __('history-job-req.modal_mulai_kerja') }}
                                </p>
                                <p class="fw-medium" id="reportModalStartWork"></p>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <p class="text-black-50 fw-semibold mb-0">{{ __('history-job-req.modal_selesai_kerja') }}
                                </p>
                                <p class="fw-medium" id="reportModalFinishWork"></p>
                            </div>
                        </div>

                        {{-- Total price display in report modal --}}
                        <div class="d-flex justify-content-between mb-3">
                            <p class="text-black-50 fw-semibold mb-0">{{ __('history-job-req.modal_total') }}</p>
                            <p class="fw-medium fs-5 mb-0">Rp <span id="reportModalRequestPrice"></span></p>
                        </div>

                        {{-- Image upload section for report proof --}}
                        <div class="mb-3">
                            <label
                                class="form-label fw-semibold">{{ __('history-job-req.modal_laporan_upload_bukti') }}</label>
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
                        <div class="mb-3">
                            <label for="reportNote"
                                class="form-label fw-semibold">{{ __('history-job-req.modal_laporan_keluh_kesah') }}</label>
                            <textarea name="reasons" id="reportNote" class="form-control rounded-4" rows="4"
                                placeholder="{{ __('history-job-req.placeholder_keluh_kesah') }}"
                                style="background-color: #f7f7ff; resize: none;"></textarea>
                        </div>

                        {{-- Report submission button --}}
                        <div class="modal-footer border-0 d-flex justify-content-end p-0 m-0">
                            <button type="button" id="submitReportButton"
                                class="btn btn-danger px-4 py-2">{{ __('history-job-req.tombol_kirim_laporan') }}</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <script>
        const lang = @json(__('accepted.js_messages'));
        const reviewLang = @json(__('accepted.modal_penilaian'));
        const proofLang = @json(__('accepted.modal_bukti'));
        // Global variables for managing transaction and worker IDs across modals
        let currentTransactionId = `{{ $transaction->id }}`;
        let reportedWorkerId = `{{ $worker->id }}`;
        let reportFiles = []; // Array of File objects or URLs for report images
        const MAX_REPORT_IMAGES = 7; // Define max images constant

        // Add a global variable to store the existing review data
        let existingReviewData = @json($existingReview);

        // --- Image Preview Logic for Report Modal ---
        const reportImageInput = document.getElementById('reportImageInput');
        const reportImagePreviewContainer = document.getElementById('reportImagePreviewContainer');
        const addImageButton = reportImagePreviewContainer ? reportImagePreviewContainer.querySelector(
            '.add-image-button') : null;
        const imageCountSpan = document.getElementById('image-count');

        /**
         * Updates the display of uploaded report images and the image count.
         * This function handles both newly added images and pre-existing images from a report.
         */
        function updateReportImagePreview() {
            if (!reportImagePreviewContainer || !imageCountSpan || !addImageButton) return;

            // Remove all current image preview wrappers to re-render them
            reportImagePreviewContainer.querySelectorAll('.image-preview-wrapper').forEach(el => el.remove());

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
                deleteButton.className = 'btn-close position-absolute top-0 end-0 m-1';
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
                reportImagePreviewContainer.insertBefore(wrapper, addImageButton);
            });

            // Update image count and add button visibility
            imageCountSpan.textContent = `${reportFiles.length}/${MAX_REPORT_IMAGES}`;
            if (reportFiles.length >= MAX_REPORT_IMAGES) {
                addImageButton.style.display = 'none'; // Hide the add button
            } else {
                // Check if the report input is not disabled before showing the add button
                if (reportImageInput && !reportImageInput.disabled) {
                    addImageButton.style.display = 'flex'; // Show the add button
                } else {
                    addImageButton.style.display = 'none'; // Keep hidden if disabled
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
                        window.showCustomAlert(lang.file_harus_gambar,
                            'error');
                        return; // Skip this file and continue to next
                    }
                    // Client-side validation for file size
                    const maxSizeBytes = 5 * 1024 * 1024; // 5 MB
                    if (file.size > maxSizeBytes) {
                        window.showCustomAlert(lang.ukuran_file_maksimal,
                            'error');
                        return; // Skip this file and continue to next
                    }

                    // Add file to array if max limit not reached
                    if (reportFiles.length < MAX_REPORT_IMAGES) {
                        reportFiles.push(file);
                    } else {
                        window.showCustomAlert(lang.maksimal_upload_gambar.replace(':max',
                            MAX_REPORT_IMAGES), 'error');
                        // Stop processing further files if limit is hit
                        return;
                    }
                });
                event.target.value = ''; // Clear the input value to allow re-selection of same files
                updateReportImagePreview(); // Update the visual display
            });
        }


        // --- Function to Open Report Modal ---
        function openReportModal() {
            // Close the completion modal before opening the report modal
            var completionModal = bootstrap.Modal.getInstance(document.getElementById('completionModal'));
            if (completionModal) {
                completionModal.hide();
            }

            // Populate report modal fields with data from the current transaction
            const reportModalElement = document.getElementById('reportWorkModal');
            const reportModal = new bootstrap.Modal(reportModalElement);

            // Fetch data directly from Blade variables (or assume they are available from the main view)
            document.getElementById('reportModalRequestTitle').textContent = `{{ $request->title ?? '-' }}`;
            document.getElementById('reportModalOrderNumber').textContent = `{{ $transaction->order_number ?? '-' }}`;
            document.getElementById('reportModalRequesterName').textContent =
                `{{ $transaction->worker->first_name ?? '' }} {{ $transaction->worker->last_name ?? '' }}`; // Worker is reported for requester
            document.getElementById('reportModalRequestLocation').textContent =
                `{{ $request->location ?? '-' }}`; // Keep current location display logic

            document.getElementById('reportModalTransactionCreatedAt').textContent =
                `{{ $transactionCreatedAtFormatted ?? '-' }}`;
            document.getElementById('reportModalTransactionUpdatedAt').textContent =
                `{{ $transactionUpdatedAtFormatted ?? '-' }}`;
            document.getElementById('reportModalRequestPrice').textContent =
                `{{ number_format($request->final_price ?? 0, 0, ',', '.') ?? '-' }}`;
            document.getElementById('reportModalStartWork').textContent =
                `{{ $transaction->start_work ? \Carbon\Carbon::parse($transaction->start_work)->format('H.i') : '-' }}`;
            document.getElementById('reportModalFinishWork').textContent =
                `{{ $transaction->finish_work ? \Carbon\Carbon::parse($transaction->finish_work)->format('H.i') : '-' }}`;


            // Set hidden form fields for submission
            document.getElementById('reportTransactionId').value = `{{ $transaction->id }}`;
            document.getElementById('reportReportedId').value = `{{ $worker->id }}`; // Report about the worker

            // Set form action for report submission
            document.getElementById('reportForm').action =
                `{{ route('user.submitReport', $transaction->id) }}`; // Correct route for requester reporting

            // Always reset for new report (as per new requirement)
            reportFiles = [];
            if (document.getElementById('reportNote')) document.getElementById('reportNote').value = '';
            if (document.getElementById('reportNote')) document.getElementById('reportNote').disabled = false;
            if (reportImageInput) reportImageInput.disabled = false;
            if (document.getElementById('submitReportButton')) document.getElementById('submitReportButton').style.display =
                'block'; // Always show submit button
            updateReportImagePreview(); // Render initial state (empty for new report)

            // Show the report modal after a brief delay
            setTimeout(() => {
                reportModal.show();
            }, 300);
        }

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
        function submitReview() {
            const comment = document.getElementById('comment').value.trim();
            const ratingInput = document.getElementById('rating-input'); // Get rating input element
            const rating = ratingInput ? parseInt(ratingInput.value) : 0; // Get value from input

            // Client-side validation for rating and comment
            if (rating == 0) {
                window.showCustomAlert(lang.pilih_rating_dulu, 'error');
                return;
            }

            if (comment == '') {
                window.showCustomAlert(lang.isi_komentar_dulu, 'error');
                return;
            }

            // Prepare FormData for submission
            const formData = new FormData();
            formData.append('transaction_id', currentTransactionId);
            formData.append('reviewer_id', `{{ auth()->id() }}`);
            formData.append('reviewee_id', `{{ $worker->id }}`); // Reviewing the worker
            formData.append('rating', rating);
            formData.append('comment', comment);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

            const submitBtn = document.getElementById('submitReviewButton');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = lang.mengirim_laporan;
            }

            fetch(`{{ route('reviews.store', $transaction->id) }}`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest', // Crucial for Laravel AJAX detection
                        'Accept': 'application/json',
                    },
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
                    if (data.success) {
                        // Reload the page first, then display the alert.
                        window.location.reload();
                        setTimeout(() => {
                            window.showCustomAlert(data.message, 'success');
                        }, 500); // Small delay to ensure reload starts
                    } else {
                        window.showCustomAlert(data.message || 'Terjadi kesalahan saat mengirim ulasan.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error submitting review:', error);
                    let errorMessage = 'Terjadi kesalahan saat menyimpan ulasan.';
                    if (error.errors) {
                        errorMessage = 'Validasi gagal:';
                        for (const key in error.errors) {
                            errorMessage += `\n- ${error.errors[key].join(', ')}`;
                        }
                    } else if (error.message) {
                        errorMessage = error.message;
                    }
                    window.showCustomAlert(errorMessage, 'error');
                })
                .finally(() => {
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.textContent = lang.mengirim;
                    }
                });
        }

        // --- Function to Submit Report ---
        function submitReport(event) {
            if (event) event.preventDefault(); // Prevent default form submission if called from an event listener

            const form = document.getElementById('reportForm');
            const reasons = document.getElementById('reportNote').value.trim();

            if (reportFiles.length === 0) {
                window.showCustomAlert(lang.upload_bukti_dulu, 'error');
                return;
            }
            if (!reasons) {
                window.showCustomAlert(lang.isi_keluhan_dulu, 'error');
                return;
            }

            // Re-validate files in reportFiles array before submission, as user might delete/add
            let hasInvalidFile = false;
            // Filter out existing URLs and only send File objects
            const filesToSend = reportFiles.filter(item => item instanceof File);

            for (const file of filesToSend) { // Use for...of for easy breaking
                if (!file.type.startsWith('image/')) {
                    window.showCustomAlert(lang.file_harus_gambar,
                        'error');
                    hasInvalidFile = true;
                    break; // Exit loop
                }
                const maxSizeBytes = 5 * 1024 * 1024; // 5 MB
                if (file.size > maxSizeBytes) {
                    window.showCustomAlert(lang.ukuran_file_maksimal,
                        'error');
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
            formData.append('reporter_id', document.getElementById('reportForm').querySelector('input[name="reporter_id"]')
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
            submitBtn.disabled = true;
            submitBtn.textContent = lang.mengirim_laporan;

            // Submit the form using fetch, expecting a redirect
            fetch(form.action, { // Use the form's action which includes transaction ID
                    method: 'POST',
                    body: formData,
                    headers: {
                        // DO NOT set 'Content-Type': 'multipart/form-data' explicitly when using FormData,
                        // the browser does it correctly with a boundary.
                        'Accept': 'application/json', // Expect JSON response
                        'X-Requested-With': 'XMLHttpRequest',
                    },
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
                    if (data.success) {
                        window.showCustomAlert(data.message, 'success');
                        const reportModal = bootstrap.Modal.getInstance(document.getElementById('reportWorkModal'));
                        if (reportModal) reportModal.hide(); // Hide report modal

                        // Re-open the completion modal
                        const completionModal = new bootstrap.Modal(document.getElementById('completionModal'));
                        completionModal.show();
                        // Update UI to reflect report submitted state (without checking hasUserReport)
                        updateReportButtonState(true); // Always set to true after submission
                    } else {
                        window.showCustomAlert(data.message || 'Terjadi kesalahan saat mengirim laporan.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error during report submission:', error);
                    let errorMessage = 'Terjadi kesalahan saat mengirim laporan.\n';
                    if (error.errors) {
                        errorMessage += 'Validasi Gagal:\n';
                        for (let key in error.errors) {
                            errorMessage += `- ${error.errors[key].join(', ')}\n`;
                        }
                    } else if (error.message) {
                        errorMessage += error.message;
                    }
                    window.showCustomAlert(errorMessage, 'error');
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.textContent = reviewLang.tombol_kirim;
                });
        }

        // Handles click event for generating and downloading invoice
        function handleInvoiceLinkClick(e) {
            e.preventDefault();
            // The transaction ID is available via a Blade variable here
            const transactionId = `{{ $transaction->id }}`;
            if (transactionId) {
                const invoiceUrl = `/generate-invoice/${transactionId}`;
                window.open(invoiceUrl, '_blank');
            } else {
                console.error('Transaction ID not found for invoice generation.');
                window.showCustomAlert('Terjadi kesalahan: ID transaksi tidak ditemukan untuk pembuatan invoice.', 'error');
            }
        }

        // Function to update the main transaction status badge and buttons
        function updateTransactionStatus(newStatus) {
            // --- Update Status Badge Section ---
            const statusBadgeSection = document.querySelector(
                '.col-12.contain.bg-light.mt-2.px-4.py-3.rounded-4.d-flex.align-items-center');
            if (!statusBadgeSection) return;

            let statusText = '';
            let bgColor = '';
            let textColor = 'text-light'; // Default text color

            // Determine status text and color
            if (newStatus === 'submitted') {
                statusText = 'Ditinjau';
                bgColor = '#294287';
            } else if (newStatus === 'cancelled') {
                statusText = 'Dibatalin';
                bgColor = 'crimson';
            } else if (newStatus === 'accepted') {
                statusText = 'Diterima';
                bgColor = '#294287';
            } else if (newStatus === 'in progress') {
                statusText = 'Dikerjain';
                bgColor = '#309FFF';
            } else if (newStatus === 'completed') {
                statusText = 'Selesai';
                bgColor = '#D3FA0D';
                textColor = 'text-dark';
            }

            // Reconstruct the innerHTML for the status badge section
            statusBadgeSection.innerHTML = `
                <div class="badge px-4 py-3 rounded-pill ${textColor} fs-6" style="background-color:${bgColor};">
                    ${statusText}
                </div>
                <h3 class="d-inline mx-3 mt-1" style="color:#294287; font-weight: 800;">{{ $request->title }}</h3>
            `;


            // --- Update Action Buttons Section ---
            // Target the specific .contain div inside the .statb column
            const actionContainer = document.querySelector('.statb > .contain');
            if (!actionContainer) return;

            actionContainer.innerHTML = ''; // Clear existing buttons

            // Add content based on newStatus
            if (newStatus === 'submitted') {
                actionContainer.innerHTML += `
                    <button type="button" class="btn px-4 py-2 rounded-pill text-light fs-4 fw-bold"
                        data-bs-toggle="modal" data-bs-target="#completeJobModal" id="completeJobBtn"
                        style="background-color:#294287; width:88%;">
                        Tandai Selesai
                    </button>
                    <a class="btn px-4 py-2 rounded-5 d-inline fw-semibold fs-5" href="#"
                        data-bs-toggle="modal" data-bs-target="#completionProofModal"
                        style="color: #0d6efd; text-decoration: none;">
                        Lihat Bukti Penyelesaian
                    </a>
                `;
            } else if (newStatus === 'completed') {
                actionContainer.innerHTML += `
                    <a class="btn btn-success px-4 py-2 rounded-pill fs-4 fw-bold"
                        style="width:88%;cursor: not-allowed;pointer-events: none;">
                        Selesai
                    </a>
                    <a class="btn px-4 py-2 rounded-5 d-inline fw-semibold fs-5" href="#"
                        data-bs-toggle="modal" data-bs-target="#completionProofModal"
                        style="color: #0d6efd; text-decoration: none;">
                        Lihat Bukti Penyelesaian
                    </a>
                `;
            } else if (newStatus === 'accepted') {
                actionContainer.innerHTML += `
                    <div class="px-4 py-2 rounded-5 d-inline fw-semibold text-light fs-4 text-center"
                        style="background-color:#9d9d9d; width:88%;">Tandai Selesai</div>
                    <div class="btn px-4 py-2 rounded-5 d-inline fw-semibold text-danger fs-5"
                        data-bs-toggle="modal" data-bs-target="#cancelWorkModal">Batalkan Kerja</div>
                `;
            } else if (newStatus === 'in progress') {
                actionContainer.innerHTML += `
                    <div class="px-4 py-2 rounded-5 d-inline fw-semibold text-light fs-4 text-center"
                        style="background-color:#9d9d9d; width:88%;">Tandai Selesai</div>
                    <a class="btn px-4 py-2 rounded-5 d-inline fw-semibold fs-5" href="#"
                        style="color: #a7a7a7; text-decoration: none; cursor: not-allowed; pointer-events: none;">
                        Lihat Bukti Penyelesaian
                    </a>
                    <div class="btn px-4 py-2 rounded-5 d-inline fw-semibold text-danger fs-5"
                        data-bs-toggle="modal" data-bs-target="#cancelWorkModal">Batalkan Kerja</div>
                `;
            } else if (newStatus === 'cancelled') {
                actionContainer.innerHTML += `
                    <div class="text-center text-secondary fw-semibold fs-5" style="width:100%; height:100%; display: flex; align-items: center; justify-content: center;">
                        Pekerjaan Dibatalkan
                    </div>
                `;
            }

            // Re-attach event listeners for newly created elements
            attachEventListeners();
        }

        function renderReviewForm() {
            const reviewSectionHeading = document.getElementById('reviewSectionHeading'); // Get the heading element
            if (reviewSectionHeading) {
                reviewSectionHeading.textContent = reviewLang.penilaian_heading_baru;
            }

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
                        <label for="comment" class="form-label text-start">${reviewLang.label_komentar}</label>
                        <textarea name="comment" id="comment" class="form-control" rows="3"
                            placeholder="${reviewLang.placeholder_komentar}" style="border-color:#8a8a8a; resize: none;"></textarea>
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
            const reviewSectionHeading = document.getElementById('reviewSectionHeading'); // Get the heading element
            if (reviewSectionHeading) {
                reviewSectionHeading.textContent = reviewLang.penilaian_heading_sudah;
            }

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
                        <label for="comment" class="form-label text-start">${reviewLang.label_komentar}</label>
                        <textarea id="comment" class="form-control" rows="3" disabled
                            style="border-color:#8a8a8a; resize: none;">${comment}</textarea>
                    </div>
                `;
            const submitReviewButton = document.getElementById('submitReviewButton');
            if (submitReviewButton) {
                submitReviewButton.style.display = 'none'; // Hide submit button if review already exists
            }
        }

        // Function to update the report button state (text and disability)
        function updateReportButtonState(hasReported) {
            const reportProblemButton = document.getElementById('reportProblemButton');
            if (reportProblemButton) {
                if (hasReported) {
                    reportProblemButton.textContent = 'Laporan sudah terkirim';
                    reportProblemButton.disabled = true;
                    reportProblemButton.classList.remove('text-danger');
                    reportProblemButton.classList.add('text-secondary');
                    reportProblemButton.onclick = null;
                } else {
                    reportProblemButton.textContent = reviewLang.laporkan_masalah;
                    reportProblemButton.disabled = false;
                    reportProblemButton.classList.remove('text-secondary');
                    reportProblemButton.classList.add('text-danger');
                    reportProblemButton.onclick = openReportModal;
                }
            }
        }

        function attachEventListeners() {
            // Attach submitReport to the "Kirim Laporan" button inside the report modal
            const submitReportButtonForReportModal = document.getElementById('submitReportButton');
            if (submitReportButtonForReportModal) {
                submitReportButtonForReportModal.removeEventListener('click', submitReport); // Prevent duplicates
                submitReportButtonForReportModal.addEventListener('click', submitReport);
            }

            // Attach submitReview to its button (if it exists)
            const submitReviewButton = document.getElementById('submitReviewButton');
            if (submitReviewButton) {
                submitReviewButton.removeEventListener('click', submitReview); // Prevent duplicates
                submitReviewButton.addEventListener('click', submitReview);
            }

            // Logic for "Ya, Selesaikan Pekerjaan" button
            const completeButton = document.getElementById('completeWorkButton');
            if (completeButton) {
                completeButton.removeEventListener('click', handleCompleteButtonClick); // Prevent duplicates
                completeButton.addEventListener('click', handleCompleteButtonClick);
            }

            // Setup invoice link
            const invoiceLink = document.getElementById('modalInvoiceLink');
            if (invoiceLink) {
                invoiceLink.removeEventListener('click', handleInvoiceLinkClick); // Prevent duplicates
                invoiceLink.addEventListener('click', handleInvoiceLinkClick);
            }
            // Attach event listener for the main "Tandai Selesai" button
            const markCompleteMainButton = document.getElementById('completeJobBtn');
            if (markCompleteMainButton) {
                markCompleteMainButton.removeEventListener('click', function() {
                    // No direct action here, just opens the modal
                });
                markCompleteMainButton.addEventListener('click', function() {
                    // No direct action here, just opens the modal
                });
            }
        }

        // Centralized handler for complete button to keep `this` context for disabling
        function handleCompleteButtonClick() {
            const form = document.getElementById('markCompleteForm');
            if (!form) return;

            this.disabled = true;
            this.innerHTML = '{{ __('accepted.memproses') }}';

            fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                            .getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest', // Crucial for Laravel AJAX detection
                        'Accept': 'application/json',
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(errorData => {
                            throw errorData; // Throw the error data object
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    console.log("Response data from markComplete:", data);
                    if (data.success) {
                        window.showCustomAlert(data.message, "success");
                        const confirmModal = bootstrap.Modal.getInstance(document
                            .getElementById('completeJobModal'));
                        if (confirmModal) confirmModal.hide();

                        // Update existingReviewData with the newly submitted review info if needed,
                        // or simply trigger a page reload which will refetch all data.
                        // For simplicity, we'll trigger a reload after review.
                        // But for immediate display, you might set existingReviewData = data.review;

                        // When job is marked complete, open the completion/review modal
                        const completionModal = new bootstrap.Modal(document.getElementById(
                            'completionModal'));
                        completionModal.show();
                        // Update UI status
                        updateTransactionStatus('completed'); // Update the main status badge AND action buttons
                    } else {
                        let errorMessage = data.message || 'Terjadi kesalahan.';
                        if (data.errors) {
                            for (const key in data.errors) {
                                if (data.errors.errors.hasOwnProperty(key)) {
                                    data.errors.errors[key].forEach(msg => {
                                        errorMessage += `\n- ${msg}`;
                                    });
                                }
                            }
                        }
                        window.showCustomAlert(errorMessage, "error");
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    let errorMessage = 'Terjadi kesalahan. Silakan coba lagi.';
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
                    this.disabled = false;
                    this.innerHTML = 'Ya, Selesaikan Pekerjaan';
                });
        }


        document.addEventListener('DOMContentLoaded', function() {
            // Initial attachment of event listeners
            attachEventListeners();

            // Dynamic content and button states for Completion Modal
            const completionModalElement = document.getElementById('completionModal');
            if (completionModalElement) {
                completionModalElement.addEventListener('show.bs.modal', function() {
                    // Populate the completion modal with data from Blade variables
                    document.getElementById('modalRequestTitle').textContent =
                        `{{ $request->title ?? '-' }}`;
                    document.getElementById('modalOrderNumber').textContent =
                        `{{ $transaction->order_number ?? '-' }}`;
                    document.getElementById('modalWorkerName').textContent =
                        `{{ $worker->first_name ?? '' }} {{ $worker->last_name ?? '' }}`;
                    document.getElementById('modalRequestLocation').textContent =
                        `{{ $request->location ?? '-' }}`;
                    document.getElementById('modalTransactionCreatedAt').textContent =
                        `{{ $transactionCreatedAtFormatted ?? '-' }}`;
                    document.getElementById('modalTransactionUpdatedAt').textContent =
                        `{{ $transactionUpdatedAtFormatted ?? '-' }}`;
                    document.getElementById('modalRequestPrice').textContent =
                        `{{ number_format($request->final_price ?? 0, 0, ',', '.') ?? '-' }}`;
                    document.getElementById('modalStartWork').textContent =
                        `{{ $transaction->start_work ? \Carbon\Carbon::parse($transaction->start_work)->format('H.i') : '-' }}`;
                    document.getElementById('modalFinishWork').textContent =
                        `{{ $transaction->finish_work ? \Carbon\Carbon::parse($transaction->finish_work)->format('H.i') : '-' }}`;

                    // Conditional rendering of review form or existing review
                    if (existingReviewData) {
                        renderExistingReview(existingReviewData.rating, existingReviewData.comment);
                    } else {
                        renderReviewForm();
                        selectedRating = 0; // Reset selectedRating for new review
                    }

                    // Always enable the report button, ignoring existing report status
                    updateReportButtonState(false); // Force to false to make it active
                });

                // Reset review form state when the completion modal is hidden
                completionModalElement.addEventListener('hidden.bs.modal', function() {
                    // No need to reset selectedRating if we re-render every time it's shown.
                    // If you wanted to preserve unsaved review data, you'd need more complex state management.
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
                    reportImageInput.value = ''; // Clear file input
                    document.getElementById('submitReportButton').disabled = false;
                    document.getElementById('submitReportButton').textContent = 'Kirim Laporan';
                    // The report button state in the completion modal will be set by its 'show.bs.modal' listener
                    // based on whether an existing report exists (which isn't implemented for display yet).
                    // For now, it will always revert to 'Laporkan masalah' (active).
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
