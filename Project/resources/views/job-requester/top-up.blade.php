@extends('master.master-job-req') {{-- Pastikan path ini sesuai dengan lokasi file master Anda --}}

@section('content')
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-7 col-lg-5">

                <div class="card shadow-sm">
                    <div class="card-body p-4">

                        {{-- Judul Halaman --}}
                        <h3 class="text-center mb-4">Top Up Saldo</h3>

                        {{-- Bagian Menampilkan Saldo Saat Ini --}}
                        <div class="text-center border rounded p-3 mb-4">
                            <h6 class="text-muted mb-1">Saldo Kerjain Anda</h6>
                            <h2 id="user-balance" class="fw-bold m-0">
                                {{-- Mengambil saldo dari user yang sedang login --}}
                                Rp{{ number_format(auth()->user()->balance, 0, ',', '.') }}
                            </h2>
                        </div>

                        {{-- Form untuk Input Nominal Top Up --}}
                        {{-- Ganti route() dengan nama route yang akan Anda gunakan untuk proses top up --}}
                        <form action="{{ route('topup.create') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="amount" class="form-label">Nominal Top Up</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control form-control-lg" id="amount" name="amount"
                                        placeholder="50000" min="10000" required>
                                </div>
                                <div class="form-text">Minimum top up adalah Rp10.000.</div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 btn-lg py-2 mt-3">
                                Top Up Sekarang
                            </button>
                        </form>

                    </div>
                </div>

                {{-- Catatan di Bawah --}}
                <p class="text-center text-muted small mt-3">
                    💡 Saldo akan bertambah setelah pembayaran berhasil.
                </p>

            </div>
        </div>
    </div>
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
