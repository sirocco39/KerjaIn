@extends('Master.master-job_req')

@section('content')
    <div class="container-fluid pembatas-x pembatas-y">
        <h1 class="text-3xl font-bold text-gray-800 mb-6 border-b-4 border-yellow-400 pb-2 inline-block">Riwayat Pemesanan
        </h1>

        <div class="flex flex-col md:flex-row justify-start items-start md:items-center">
            <div class="tabs-wrapper">
                <div class="col tab-button active" data-tab="all">
                    All Order ({{ $allOrders->count() }})
                </div>
                <div class="col tab-button" data-tab="pending">
                    Pending ({{ $pendingOrders->count() }})
                </div>
                <div class="col tab-button" data-tab="completed">
                    Completed ({{ $completedOrders->count() }})
                </div>
                <div class="col tab-button" data-tab="cancelled">
                    Cancelled ({{ $cancelledOrders->count() }})
                </div>
            </div>
        </div>

        <div class="flex flex-col md:flex-row justify-start items-start md:items-center">
            <div class="w-full bg-white rounded-lg">
                <div class="row text-center tableHeader d-flex align-items-center justify-content-center fw-semibold m-0 p-0"
                    style="height: 4rem;">
                    <div class="col m-0 p-0">Judul</div>
                    <div class="col m-0 p-0">Status</div>
                    <div class="col m-0 p-0">Tanggal Selesai</div>
                    <div class="col m-0 p-0">Klien</div>
                    <div class="col m-0 p-0">Lokasi</div>
                    <div class="col m-0 p-0">Harga</div>
                </div>
                <hr class="mx-auto border-2 opacity-100 my-0 p-0" style="width: 98%; border-color: #294287;">
                <div id="order-list-container">
                    @forelse ($allOrders as $order)
                        <div class="order-row hoverable-row {{ str_replace(' ', '-', $order->status) }}-tab">
                            <div class="row text-center text-sm d-flex justify-content-center align-items-center m-0 p-0"
                                style="height: 3.5rem">
                                {{-- Request Title --}}
                                <div class="col m-0 p-0"> {{ $order->request->title ?? '-' }}</div>
                                {{-- Status Badge --}}
                                <div class="col m-0 p-0">
                                    <span class="badge rounded-pill"
                                        style="
                                        padding: .5em .9em;
                                        font-size: 0.85em;
                                        @if ($order->status_text == 'Selesai')
                                            background-color: #D3FA0D;
                                            color: #333;
                                        @elseif($order->status_text == 'Dikerjain')
                                            background-color: #309FFF;
                                            color: #FFF;
                                        @elseif($order->status_text == 'Dibatalin')
                                            background-color: #E63C3C;
                                            color: #FFF;
                                        @elseif($order->status_text == 'Diselesaiin')
                                            background-color: #309FFF;
                                            color: #FFF;
                                        @elseif($order->status_text == 'Diterima')
                                            background-color: #309FFF;
                                            color: #FFF;
                                        @else
                                            background-color: #6c757d;
                                            color: #FFF;
                                        @endif
                                    ">
                                        {{ $order->status_text ?? '-' }}
                                    </span>
                                </div>
                                {{-- Updated At Date (Tanggal Selesai) --}}
                                <div class="col m-0 p-0">
                                    {{ \Carbon\Carbon::parse($order->updated_at)->format('d - m - Y') ?? '-' }}</div>
                                {{-- Requester Name (Klien) --}}
                                <div class="col m-0 p-0">{{ $order->worker->full_name ?? '-' }}</div>
                                {{-- Request Location (Lokasi) --}}
                                <div class="col m-0 p-0">{{ $order->request->location ?? '-' }}</div>
                                {{-- Price (Harga) --}}
                                <div class="col m-0 p-0">Rp
                                    {{ number_format($order->request->price ?? 0, 0, ',', '.') ?? '-' }}</div>
                            </div>
                        </div>
                    @empty
                        <div id="no-transaction-message"
                            class="my-0 py-6 px-6 text-center text-gray-500 justify-content-center flex items-center w-full"
                            style="height: 3rem">
                            Belum Ada Transaksi
                        </div>
                    @endforelse
                </div>

            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabButtons = document.querySelectorAll('.tab-button');
            const orderRows = document.querySelectorAll('.order-row');
            const noTransactionMessage = document.getElementById('no-transaction-message');
            const orderListContainer = document.getElementById('order-list-container');

            // Function to update visibility based on selected tab
            function updateTabContent(selectedTab) {
                let hasVisibleOrders = false;
                let visibleRows = []; // To store currently visible rows

                orderRows.forEach(row => {
                    const orderStatus = row.classList.contains('accepted-tab') ? 'pending' :
                        row.classList.contains('in-progress-tab') ? 'pending' :
                        row.classList.contains('submitted-tab') ? 'pending' :
                        row.classList.contains('completed-tab') ? 'completed' :
                        row.classList.contains('cancelled-tab') ? 'cancelled' :
                        'all'; // Default to 'all' if no specific status class

                    if (selectedTab === 'all' || selectedTab === orderStatus) {
                        row.style.display = ''; // Show relevant rows
                        hasVisibleOrders = true;
                        visibleRows.push(row); // Add to visible rows
                    } else {
                        row.style.display = 'none'; // Hide others
                    }
                });

                // Remove all existing HRs before adding new ones
                const existingHrs = orderListContainer.querySelectorAll('.order-divider');
                existingHrs.forEach(hr => hr.remove());

                // Add HRs dynamically only between visible rows
                for (let i = 0; i < visibleRows.length - 1; i++) {
                    const hr = document.createElement('hr');
                    hr.classList.add('mx-auto', 'border-1', 'opacity-100', 'my-0', 'p-0', 'order-divider');
                    hr.style.cssText = 'width: 98%; border-color: #294287;';
                    visibleRows[i].parentNode.insertBefore(hr, visibleRows[i].nextSibling);
                }

                // Show/hide the "No Transaction Yet" message
                if (noTransactionMessage) { // Check if the element exists
                    if (hasVisibleOrders) {
                        noTransactionMessage.style.display = 'none';
                    } else {
                        noTransactionMessage.style.display = '';
                    }
                }
            }

            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    tabButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');

                    const selectedTab = this.dataset.tab;
                    updateTabContent(selectedTab);
                });
            });

            // Initialize display for the default active tab (All Order)
            updateTabContent(document.querySelector('.tab-button.active').dataset.tab);
        });
    </script>
@endsection
