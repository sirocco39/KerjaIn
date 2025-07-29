@extends('master.master-job-taker')
<!-- this file name top-up.blade.php -->

@section('content')
<div class="topup-page-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-7 col-lg-5">

                {{-- Card Top Up di tengah halaman --}}
                <div class="card topup-card">
                    <div class="card-body p-4 p-md-5">

                        <h3 class="text-center fw-bold mb-4">{{ __('balances.fill_balance') }}</h3>

                        {{-- Bagian Menampilkan Saldo Saat Ini --}}
                        <div class="text-center border rounded p-3 mb-4">
                            <h6 class="text-muted mb-1">{{ __('balances.your_active_balance') }}</h6>
                            <h2 id="user-balance" class="fw-bold m-0" style="color: #00A99D;">
                                Rp{{ number_format(auth()->user()->balance, 0, ',', '.') }}
                            </h2>
                        </div>

                        {{-- Tampilkan pesan error jika ada --}}
                        @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        {{-- Form untuk Input Nominal Top Up --}}
                        <form action="{{ route('topup.create') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="amount" class="form-label fw-semibold">{{ __('balances.top_up_amount') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control form-control-lg" id="amount"
                                        name="amount" placeholder="50000" min="10000" required>
                                </div>
                                <div class="form-text mt-2">{{ __('balances.min_top_up_amount_desc') }}</div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 btn-lg py-2 mt-3">
                                {{ __('balances.continue_payment') }}
                            </button>
                        </form>

                    </div>
                </div>

                <p class="text-center text-muted small mt-4">
                    {{ __('balances.balance_auto_added_desc') }}
                </p>

            </div>
        </div>
    </div>
</div>

{{-- Style untuk tema --}}
<style>
    .topup-page-container {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: calc(100vh - 200px);
        /* Adjust 200px based on your header/footer height */
        background-color: #f0f3f7;
        padding: 2rem 0;
    }

    .topup-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        /* Shadow lebih tebal agar "nonjol" */
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const balanceElement = document.getElementById('user-balance');
        const urlParams = new URLSearchParams(window.location.search);
        const orderId = urlParams.get('order_id');

        let pollingInterval;

        const fetchLatestBalance = () => {
            fetch('{{ route('balance.get') }}')
                .then(response => response.json())
                .then(data => {
                    balanceElement.textContent = data.formatted_balance;
                });
        }

        const startPolling = () => {
            if (pollingInterval) return; // Jangan mulai polling jika sudah berjalan

            let attempts = 0;
            pollingInterval = setInterval(() => {
                // Hentikan setelah 5 kali coba (15 detik) untuk mencegah loop tak terbatas
                if (attempts >= 5) {
                    clearInterval(pollingInterval);
                    return;
                }

                fetch(`/topup/status/${orderId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'paid') {
                            fetchLatestBalance(); // Ambil saldo terbaru dan update tampilan
                            clearInterval(pollingInterval); // Hentikan polling
                        }
                    });

                attempts++;
            }, 3000);
        };

        // Jika ada order_id di URL, cek statusnya sekarang juga!
        if (orderId) {
            fetch(`/topup/status/${orderId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'paid') {
                        // Jika sudah lunas, langsung update saldo
                        fetchLatestBalance();
                    } else {
                        // Jika belum, mulai polling untuk menunggu webhook
                        startPolling();
                    }
                })
                .catch(error => console.error('Gagal memeriksa status awal:', error));
        }
    });
</script>
@endsection