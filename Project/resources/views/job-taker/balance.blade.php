@extends('master.master-job-req') {{-- Ganti dengan layout master Anda --}}

@section('content')
    <div class="container-fluid p-4" style="background-color: #f0f3f7;">
        <div class="row">
            {{-- Kolom Kiri: Info Saldo & Aksi --}}
            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="text-muted">Saldo Aktif</h5>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <h1 class="fw-bold m-0" style="font-size: 2.5rem;">
                                Rp{{ number_format($user->balance, 0, ',', '.') }}
                            </h1>
                            <a href="{{ route('top-up.job-taker') }}" class="btn btn-outline-primary fw-bold">Top-up</a>
                        </div>
                    </div>
                </div>
                {{-- Anda bisa menambahkan card lain di sini jika perlu, seperti di contoh --}}
            </div>

            {{-- Kolom Kanan: Riwayat Transaksi --}}
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h4 class="card-title fw-bold mb-4">Riwayat Transaksi</h4>

                        {{-- Kontainer untuk daftar transaksi --}}
                        <div class="transaction-list">
                            @forelse ($walletTransactions as $transaction)
                                <div class="d-flex justify-content-between align-items-center py-3">
                                    <div class="d-flex align-items-center">
                                        {{-- Ikon berdasarkan tipe transaksi --}}
                                        <div class="me-3">
                                            @if ($transaction->type == 'credit')
                                                <i class="fas fa-arrow-down-circle fa-2x text-success"></i>
                                            @else
                                                <i class="fas fa-arrow-up-circle fa-2x text-danger"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="fw-bold mb-0">{{ $transaction->description }}</p>
                                            <p class="text-muted small mb-0">
                                                {{ $transaction->created_at->format('d M Y, H:i') }}</p>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <h5 class="fw-bold mb-0 {{ $transaction->type == 'credit' ? 'text-success' : '' }}">
                                            {{ $transaction->type == 'credit' ? '+' : '-' }}Rp{{ number_format($transaction->amount, 0, ',', '.') }}
                                        </h5>
                                    </div>
                                </div>
                                @if (!$loop->last)
                                    <hr class="my-0">
                                @endif
                            @empty
                                {{-- Tampilan jika tidak ada transaksi --}}
                                <div class="text-center py-5">
                                    <img src="{{ asset('images/empty-transaction.svg') }}" alt="Ilustrasi Transaksi Kosong"
                                        style="width: 150px;" class="mb-3">
                                    <h5 class="fw-bold">Belum ada transaksi</h5>
                                    <p class="text-muted">Semua riwayat pengisian saldo dan pembayaran akan muncul di sini.
                                    </p>
                                </div>
                            @endforelse
                        </div>

                        {{-- Link Paginasi --}}
                        <div class="d-flex justify-content-center mt-4">
                            {{ $walletTransactions->links() }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Pastikan Anda sudah memuat Font Awesome di layout master Anda untuk ikon --}}
    <style>
        .fa-arrow-down-circle {
            color: #28a745;
        }

        .fa-arrow-up-circle {
            color: #dc3545;
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
