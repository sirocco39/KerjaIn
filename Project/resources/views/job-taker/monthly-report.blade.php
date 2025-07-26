@extends('Master.master-job-taker')
@section('content')
<!-- Banner Section -->
<section class="banner-section">
    <div class="container-fluid p-0 position-relative">
        <div class="d-flex flex-column flex-md-row align-items-start p-1" style="min-height: 500px;">

            <!-- Konten Teks -->
            <div class="text-content custom-translate mb-5" style="margin-top: 15px;">
                <h1 class="display-6 display-md-3 display-lg-2" style="font-weight: bold;">
                    Hai <span style="color: #D3FA0D;">{{ $worker->first_name . ' ' . $worker->last_name }}</span>, ini
                    Rekap Hebatmu di Bulan {{ $reportPeriod }}
                </h1>
            </div>

            <!-- Bulat-bulat Warna-warni -->
            <div class="color-dots"
                style="width: 300px; min-width: 300px; position: relative; height: 400px; z-index: 1;">
                <div class="dot dot-1"></div>
                <div class="dot dot-2"></div>
                <div class="dot dot-3"></div>
            </div>

            <!-- Create user avatar with name in line with avatar -->
            <div class="d-flex align-items-center mb-4 mb-md-0"
                style="
            position: static;
            align-self: flex-start;
            margin-top: 20px;
            margin-left: 20px;
            top: 20px;
            right: 20px;
            padding: 12px;
            z-index: 3;
            ">
                <img src="https://cdn-icons-png.freepik.com/512/9203/9203764.png" alt="" class="rounded-circle"
                    style="width: 60px; height: 60px;">
                <div class="d-flex flex-column ms-2">
                    <span
                        style="font-size: 16px; font-weight: bold; color: white;">{{ $worker->first_name . ' ' . $worker->last_name }}</span>
                    <span style="font-size: 14px; color: #dfe6e9;">Pekerja</span>
                    <span style="font-size: 14px; color: #dfe6e9;">Status Verifikasi:
                        <span
                            class="font-semibold {{ $verificationStatus == 'Terverifikasi' ? 'text-green-600' : 'text-yellow-600' }}">
                            {{ $verificationStatus }}
                        </span>
                    </span>
                </div>
            </div>


        </div>

    </div>
</section>

<div class="mt-2">
    <label for="reportMonth" style="margin-left: 60px; margin-top: 20px; font-size: 25px; font-weight: bold;">Aktivitas
        Saya</label>
    <select id="reportMonth" name="reportMonth"
        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
        onchange="this.form.submit()" style="margin-left: 10px">
        {{-- Loop untuk generate opsi bulan --}}
        @foreach ($availableMonths as $monthOption)
        <option value="{{ $monthOption['value'] }}"
            {{ $monthOption['value'] == request('report_month', \Carbon\Carbon::now()->format('Y-m')) ? 'selected' : '' }}>
            {{ $monthOption['label'] }}
        </option>
        @endforeach
    </select>
</div>
<form id="reportForm" action="{{ route('monthly.report') }}" method="GET" class="hidden">
    <input type="hidden" name="report_month" id="hiddenReportMonth">
</form>

<div class="container mt-4">
    <div class="row g-3 justify-content-center">

        <div class="col-6 col-md-3 col-lg-2">
            <div class="stat-card text-center">
                <div class="stat-number">{{ $totalJobsCompleted }}</div>
                <p class="stat-label">Total pekerjaan diselesaikan</p>
            </div>
        </div>

        <div class="col-6 col-md-3 col-lg-2">
            <div class="stat-card text-center">
                <div class="stat-number">{{ number_format($totalHoursWorked, 1) }}</div>
                <p class="stat-label">Total jam kerja</p>
            </div>
        </div>

        <div class="col-6 col-md-3 col-lg-3">
            <div class="stat-card text-center">
                <div class="stat-number">Rp {{ number_format($totalEarnings, 0, ',', '.') }}</div>
                <p class="stat-label">Total pendapatan</p>
            </div>
        </div>

        <div class="col-6 col-md-3 col-lg-3">
            <div class="stat-card text-center">
                <div class="stat-number">⭐ {{ $averageRating }} / 5</div>
                <p class="stat-label">Rating rata-rata</p>
            </div>
        </div>

        <div class="col-6 col-md-3 col-lg-2">
            <div class="stat-card text-center">
                <div class="stat-number">{{ $distinctClients }}</div>
                <p class="stat-label">Total Klien berbeda</p>
            </div>
        </div>

    </div>
</div>


<div class="card-custom" style="margin-left: 60px; margin-top: 20px; margin-right: 60px">
    <h5>Pekerjaan</h5>
    @if ($jobHistory->isEmpty())
    <p class="text-gray-600">Belum ada pekerjaan yang diselesaikan bulan ini. Mari mulai bekerja! 💪</p>
    @else
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                        style="text-align: center">Tanggal</th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                        style="text-align: center">Pekerjaan</th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                        style="text-align: center">Lokasi</th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                        style="text-align: center">Klien</th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                        style="text-align: center">Durasi</th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                        style="text-align: center">Upah</th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                        style="text-align: center">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($jobHistory as $transaction)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" style="text-align: center">
                        {{ $transaction->updated_at->format('d M Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" style="text-align: center">
                        {{ $transaction->request->title ?? 'N/A' }}
                    </td> {{-- Assuming 'job_title' on Request --}}
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" style="text-align: center">
                        {{ $transaction->request->location ?? 'N/A' }}
                    </td> {{-- Assuming 'location' on Request --}}
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" style="text-align: center">
                        {{ $transaction->request->requester->first_name . ' ' . $transaction->request->requester->last_name ?? 'N/A' }}
                    </td> {{-- Assuming 'name' on Requester (User) --}}
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" style="text-align: center">
                        @php
                        $durationMinutes = 0;
                        if ($transaction->start_work && $transaction->finish_work) {
                        $durationMinutes = $transaction->start_work->diffInMinutes(
                        $transaction->finish_work,
                        );
                        }
                        @endphp
                        {{ $durationMinutes > 0 ? number_format($durationMinutes / 60, 1) . ' jam' : 'N/A' }}

                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" style="text-align: center">
                        Rp {{ number_format($transaction->request->price ?? 0, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" style="text-align: center">
                        <span
                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            {{ ucfirst($transaction->status) }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<div class="row g-4 mb-5" style="padding-top: 3%">

    {{-- 4. Grafik Aktivitas / Pendapatan --}}
    <div class="col-md-6 mt-3" style="padding-left: 5%">
        <div class="card-custom h-100" style="max-height: 400px; overflow: hidden;">
            <h5 class="p-3 mb-0">Pendapatan</h5>
            <div class="chart-container" style="position: relative; height: calc(100% - 60px); width: 100%;">
                <canvas id="earningsChart" data-chart-labels='{!! $chartLabels !!}'
                    data-chart-earnings='{!! $chartEarningsData !!}' data-chart-jobs='{!! $chartJobsCompletedData ?? ' []' !!}'>
                </canvas>
            </div>
        </div>
    </div>

    {{-- Skrip untuk Chart.js --}}
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // PERBAIKAN UTAMA DI SINI:
            // Dapatkan elemen canvas itu sendiri, BUKAN hanya konteksnya, untuk mengakses dataset.
            const chartElement = document.getElementById('earningsChart');

            // Pastikan elemen chart ditemukan sebelum mencoba mengakses dataset
            if (chartElement) {
                const ctx = chartElement.getContext('2d');

                // Ambil data dari dataset elemen canvas
                const labels = JSON.parse(chartElement.dataset.chartLabels);
                console.log(labels); // Ini akan menampilkan array labels di console browser Anda
                const earningsData = JSON.parse(chartElement.dataset.chartEarnings);
                const jobsCompletedData = JSON.parse(chartElement.dataset.chartJobs);

                new Chart(ctx, {
                    type: 'line', // Jenis grafik: 'line', 'bar', 'pie', dll.
                    data: {
                        labels: labels,
                        datasets: [{
                                label: 'Pendapatan Harian (IDR)', // Ubah label sesuai dengan data harian
                                data: earningsData,
                                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                borderColor: 'rgba(75, 192, 192, 1)',
                                borderWidth: 2,
                                tension: 0.3,
                                fill: true,
                            },
                            { // Tambahkan dataset untuk Pekerjaan Selesai
                                label: 'Pekerjaan Selesai',
                                data: jobsCompletedData,
                                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                                borderColor: 'rgba(255, 99, 132, 1)',
                                borderWidth: 2,
                                tension: 0.3,
                                fill: false, // Biasanya tidak diisi untuk jumlah
                                yAxisID: 'yJobs', // ID sumbu Y terpisah jika diperlukan
                                hidden: true // Sembunyikan secara default jika Anda ingin fokus pada pendapatan
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: { // Sumbu Y untuk Pendapatan
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Pendapatan (IDR)'
                                },
                                ticks: {
                                    callback: function(value, index, values) {
                                        return 'Rp ' + value.toLocaleString('id-ID');
                                    }
                                }
                            },
                            yJobs: { // Sumbu Y opsional untuk Pekerjaan Selesai (jika skalanya sangat berbeda)
                                type: 'linear',
                                display: true,
                                position: 'right', // Tampilkan di sisi kanan
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Jumlah Pekerjaan'
                                },
                                grid: {
                                    drawOnChartArea: false, // Jangan gambar grid untuk sumbu ini
                                },
                                // Hanya tampilkan sumbu ini jika dataset jobsCompletedData tidak kosong
                                min: 0, // Pastikan dimulai dari 0
                                max: Math.max(...jobsCompletedData) +
                                    1 // Sesuaikan maks sedikit di atas nilai tertinggi
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Tanggal' // Ubah label sumbu X menjadi 'Tanggal'
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.y !== null) {
                                            // Format berdasarkan dataset mana yang sedang di-hover
                                            if (context.dataset.label === 'Pendapatan Harian (IDR)') {
                                                label += 'Rp ' + context.parsed.y.toLocaleString(
                                                    'id-ID');
                                            } else if (context.dataset.label === 'Pekerjaan Selesai') {
                                                label += context.parsed.y + ' Pekerjaan';
                                            } else {
                                                label += context.parsed.y;
                                            }
                                        }
                                        return label;
                                    }
                                }
                            }
                        }
                    }
                });
            } else {
                console.error('Elemen canvas dengan ID "earningsChart" tidak ditemukan.');
            }
        });
    </script>
    @endpush

    <div class="col-md-6 mt-3" style="padding-right: 5%">
        <div class="card-custom h-100" style="max-height: 400px; overflow-y: auto;">
            <h5 class="p-3">Ulasan</h5>
            <div class="reviews-container px-3 pb-3">
                @if ($clientReviews->isEmpty())
                <p class="text-gray-600">Belum ada ulasan bulan ini. Tetap semangat mengumpulkan bintang! ✨</p>
                @else
                @foreach ($clientReviews as $review)
                <div class="review-item d-flex align-items-start mb-3">
                    <img src="https://cdn-icons-png.freepik.com/512/9203/9203764.png" alt="Profile"
                        class="rounded-circle" style="width: 60px; height: 60px; margin-right: 10px;">
                    <div>
                        <strong>{{ $review->reviewer->first_name . ' ' . $review->reviewer->last_name ?? 'Klien Anonim' }}</strong><br>
                        <small>Klien</small><br>
                        {{ str_repeat('⭐', $review->rating) }}
                        <p class="review-text">"{{ $review->comment }}"</p>
                    </div>
                </div>
                @endforeach
                @endif
            </div>
        </div>
    </div>
</div>

<div class="card-custom" style="margin-left: 60px; margin-right: 60px">
    <h5>Laporan Bulanan</h5>

    <div class="content" style="text-align:center;">
        <p>Download Laporan Bulanan Anda!</p>
        <i class="fas fa-download download-icon"></i><br>
        <a href="{{ route('monthly.report.download.pdf', ['report_month' => app('request')->input('report_month', \Carbon\Carbon::now()->format('Y-m'))]) }}"
            class="">
            <button type="button" class="btn btn-primary btn-sm">Download</button>
        </a>
    </div>
</div>

<script>
    document.getElementById('reportMonth').addEventListener('change', function() {
        document.getElementById('hiddenReportMonth').value = this.value;
        document.getElementById('reportForm').submit();
    });
</script>



@endsection