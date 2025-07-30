@extends('master.master-job-req')
<!-- this file name balance.blade.php -->
@section('content')
<div class="wallet-page-wrapper">
    {{-- Header dengan background gradasi --}}
    <div class="wallet-header">
        <div class="container-fluid pembatas-x">
            <h2 class="text-white fw-bold">{{ __('balances.wallet_and_balance') }}</h2>
        </div>
    </div>

    <div class="container-fluid pembatas-x wallet-content">
        <div class="row">
            {{-- Kolom Kiri: Info Saldo & Aksi --}}
            <div class="col-lg-4 mb-4">
                <div class="card balance-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="card-text text-muted mb-1">{{ __('balances.active_balance') }}</p>
                                <h2 class="card-title fw-bolder" id="user-balance">
                                    Rp{{ number_format($user->balance, 0, ',', '.') }}
                                </h2>
                            </div>
                            <a href="{{ route('top-up.job-req') }}" class="btn btn-primary fw-bold px-3">{{ __('balances.top_up') }}</a>
                        </div>
                    </div>
                </div>
                <div class="card locked-balance-card mt-3">
                    <div class="card-body">
                        <p class="card-text text-muted mb-1">{{ __('balances.held_balance_escrow') }}</p>
                        <h4 class="card-title fw-bolder">
                            Rp{{ number_format($user->locked_balance, 0, ',', '.') }}
                        </h4>
                    </div>
                </div>
            </div>

            {{-- Kolom Kanan: Riwayat Transaksi --}}
            <div class="col-lg-8">
                <div class="card transaction-card">
                    <div class="card-body">
                        <h4 class="card-title fw-bold mb-4">{{ __('balances.transaction_history') }}</h4>

                        <div class="transaction-list">
                            @forelse ($walletTransactions as $transaction)
                            <div class="transaction-item">
                                <div class="transaction-icon">
                                    @if ($transaction->type == 'credit')
                                    <i class="fas fa-arrow-down-circle credit"></i>
                                    @else
                                    <i class="fas fa-arrow-up-circle debit"></i>
                                    @endif
                                </div>
                                <div class="transaction-details">
                                    <p class="fw-bold mb-0">
                                        @if(App::getLocale() == 'en' && isset($transaction->description_en))
                                        {{ $transaction->description_en }}
                                        @elseif(App::getLocale() == 'id' && isset($transaction->description_id))
                                        {{ $transaction->description_id }}
                                        @else
                                        {{ $transaction->description }} {{-- Fallback jika bahasa tidak ditemukan atau kolom tidak ada --}}
                                        @endif
                                    </p>
                                    <p class="text-muted small mb-0">
                                        {{ $transaction->created_at->format('d M Y, H:i') }}
                                    </p>
                                </div>
                                <div class="transaction-amount">
                                    <h5
                                        class="fw-bold mb-0 {{ $transaction->type == 'credit' ? 'credit' : 'debit' }}">
                                        {{ $transaction->type == 'credit' ? '-' : '+' }}Rp{{ number_format($transaction->amount, 0, ',', '.') }}
                                    </h5>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-5">
                                <h5 class="fw-bold">{{ __('balances.no_transactions_yet') }}</h5>
                                <p class="text-muted">{{ __('balances.top_up_payment_history_desc') }}
                                </p>
                            </div>
                            @endforelse
                        </div>

                        {{-- Link Paginasi --}}
                        @if ($walletTransactions->hasPages())
                        <div class="pagination-wrapper d-flex justify-content-center mt-4">
                            <nav>
                                <ul class="pagination">
                                    {{-- Tombol Halaman Sebelumnya --}}
                                    @if ($walletTransactions->onFirstPage())
                                    <li class="page-item disabled" aria-disabled="true">
                                        <span class="page-link">&lsaquo;</span>
                                    </li>
                                    @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $walletTransactions->previousPageUrl() }}"
                                            rel="prev">&lsaquo;</a>
                                    </li>
                                    @endif

                                    {{-- Link Nomor Halaman --}}
                                    @foreach ($walletTransactions->links()->elements[0] as $page => $url)
                                    @if ($page == $walletTransactions->currentPage())
                                    <li class="page-item active" aria-current="page"><span
                                            class="page-link">{{ $page }}</span></li>
                                    @else
                                    <li class="page-item"><a class="page-link"
                                            href="{{ $url }}">{{ $page }}</a></li>
                                    @endif
                                    @endforeach

                                    {{-- Tombol Halaman Selanjutnya --}}
                                    @if ($walletTransactions->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $walletTransactions->nextPageUrl() }}"
                                            rel="next">&rsaquo;</a>
                                    </li>
                                    @else
                                    <li class="page-item disabled" aria-disabled="true">
                                        <span class="page-link">&rsaquo;</span>
                                    </li>
                                    @endif
                                </ul>
                            </nav>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Layout Utama */
    .wallet-page-wrapper {
        padding-bottom: 2rem;
    }

    .wallet-header {
        background: linear-gradient(90deg, #00A99D, #007BFF);
        padding: 2rem 0;
    }

    .wallet-content {
        margin-top: -40px;
    }

    /* Cards */
    .card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .balance-card .card-title {
        color: #00A99D;
    }

    .locked-balance-card .card-title {
        color: #6c757d;
    }

    .transaction-card {
        min-height: 400px;
    }

    /* Daftar Transaksi */
    .transaction-item {
        display: flex;
        align-items: center;
        padding: 1rem 0;
    }

    .transaction-item:not(:last-child) {
        border-bottom: 1px solid #f0f3f7;
    }

    .transaction-icon {
        font-size: 1.5rem;
        margin-right: 1rem;
    }

    .transaction-details {
        flex-grow: 1;
    }

    .transaction-amount {
        margin-left: 1rem;
        text-align: right;
    }

    .debit {
        color: #28a745;
    }

    .credit {
        color: #dc3545;
    }

    /* Pagination Baru */
    .pagination-wrapper .pagination {
        gap: 0.5rem;
    }

    .pagination-wrapper .page-item .page-link {
        border-radius: 8px !important;
        border: none;
        font-weight: 600;
        color: #6c757d;
        background-color: #f0f3f7;
    }

    .pagination-wrapper .page-item.active .page-link {
        background-color: #007BFF;
        color: white;
    }

    .pagination-wrapper .page-item .page-link:hover {
        background-color: #e2e6ea;
    }

    .pagination-wrapper .page-item.disabled .page-link {
        background-color: #f8f9fa;
        color: #adb5bd;
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