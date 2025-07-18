@extends('Master.master-job_taker')

@section('content')
@php
@endphp
<div class="container mx-auto p-6 bg-white shadow-lg rounded-lg">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Hai {{ $worker->first_name . ' ' . $worker->last_name}}, Ini Rekap Hebatmu Bulan Ini!</h1>

    {{-- 1. Header Ringkas Bulanan --}}
    <div class="mb-8">
        <p class="text-lg text-gray-600">Periode Laporan:
            <span class="font-semibold text-gray-800" id="selectedPeriodText">{{ $reportPeriod }}</span>
        </p>

        <div class="mt-2">
            <label for="reportMonth" class="block text-sm font-medium text-gray-700">Pilih Bulan:</label>
            <select id="reportMonth" name="reportMonth"
                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                onchange="this.form.submit()">
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


        <p class="text-lg text-gray-600 mt-4">Status Verifikasi:
            <span class="font-semibold {{ $verificationStatus == 'Terverifikasi' ? 'text-green-600' : 'text-yellow-600' }}">
                {{ $verificationStatus }}
            </span>
        </p>
    </div>

    <hr class="my-6 border-gray-300">

    {{-- 2. Ringkasan Statistik Kerja --}}
    <h2 class="text-2xl font-semibold text-gray-700 mb-4">📊 Ringkasan Statistik Kerja</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-blue-50 p-4 rounded-lg shadow-sm">
            <p class="text-sm text-gray-500">Total Job Diselesaikan</p>
            <p class="text-2xl font-bold text-blue-700">{{ $totalJobsCompleted }} pekerjaan</p>
        </div>
        <div class="bg-green-50 p-4 rounded-lg shadow-sm">
            <p class="text-sm text-gray-500">Total Jam Kerja</p>
            <p class="text-2xl font-bold text-green-700">{{ number_format($totalHoursWorked, 1) }} jam</p>
        </div>
        <div class="bg-purple-50 p-4 rounded-lg shadow-sm">
            <p class="text-sm text-gray-500">Total Pendapatan Bulan Ini</p>
            <p class="text-2xl font-bold text-purple-700">Rp {{ number_format($totalEarnings, 0, ',', '.') }}</p>
        </div>
        <div class="bg-yellow-50 p-4 rounded-lg shadow-sm">
            <p class="text-sm text-gray-500">Rating Rata-rata</p>
            <p class="text-2xl font-bold text-yellow-700">⭐ {{ $averageRating }} / 5</p>
        </div>
        <div class="bg-red-50 p-4 rounded-lg shadow-sm">
            <p class="text-sm text-gray-500">Jumlah Klien Berbeda</p>
            <p class="text-2xl font-bold text-red-700">{{ $distinctClients }}</p>
        </div>
    </div>

    <hr class="my-6 border-gray-300">

    {{-- 3. Riwayat Pekerjaan Bulan Ini --}}
    <h2 class="text-2xl font-semibold text-gray-700 mb-4">📅 Riwayat Pekerjaan Bulan Ini</h2>
    @if ($jobHistory->isEmpty())
    <p class="text-gray-600">Belum ada pekerjaan yang diselesaikan bulan ini. Mari mulai bekerja! 💪</p>
    @else
    <div class="overflow-x-auto bg-gray-50 rounded-lg shadow-sm p-4">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Job</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Klien</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durasi</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Upah</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($jobHistory as $transaction)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $transaction->updated_at->format('d M Y') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $transaction->request->title ?? 'N/A' }}</td> {{-- Assuming 'job_title' on Request --}}
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $transaction->request->location ?? 'N/A' }}</td> {{-- Assuming 'location' on Request --}}
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $transaction->requester->first_name . ' ' . $transaction->requester->last_name ?? 'N/A' }}</td> {{-- Assuming 'name' on Requester (User) --}}
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        @php
                        $durationMinutes = 0;
                        if ($transaction->start_work && $transaction->finish_work) {
                        $durationMinutes = $transaction->start_work->diffInMinutes($transaction->finish_work);
                        }
                        @endphp
                        {{ $durationMinutes > 0 ? number_format($durationMinutes / 60, 1) . ' jam' : 'N/A' }}

                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Rp {{ number_format($transaction->request->price ?? 0, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            {{ ucfirst($transaction->status) }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <hr class="my-6 border-gray-300">

    {{-- 4. Grafik Aktivitas / Pendapatan --}}
    <div class="bg-gray-50 p-4 rounded-lg shadow-sm text-gray-600">
        <div class="relative h-96">
            <canvas id="earningsChart"
                data-chart-labels='{!! $chartLabels !!}'
                data-chart-earnings='{!! $chartEarningsData !!}'
                data-chart-jobs='{!! $chartJobsCompletedData ?? ' []' !!}'>
            </canvas>
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
                                max: Math.max(...jobsCompletedData) + 1 // Sesuaikan maks sedikit di atas nilai tertinggi
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
                                                label += 'Rp ' + context.parsed.y.toLocaleString('id-ID');
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

    <hr class="my-6 border-gray-300">

    {{-- 5. Ulasan dari Pemberi Kerja --}}
    <h2 class="text-2xl font-semibold text-gray-700 mb-4">🗨️ Ulasan dari Pemberi Kerja</h2>
    @if ($clientReviews->isEmpty())
    <p class="text-gray-600">Belum ada ulasan bulan ini. Tetap semangat mengumpulkan bintang! ✨</p>
    @else
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach ($clientReviews as $review)
        <div class="bg-blue-50 p-4 rounded-lg shadow-sm border border-blue-200">
            <p class="text-gray-800 italic mb-2">"{{ $review->comment }}"</p>
            <p class="text-yellow-500 font-bold">Rating: {{ str_repeat('⭐', $review->rating) }}</p>
            <p class="text-gray-600 text-sm mt-1">
                Dari: {{ $review->reviewer->first_name . ' '. $review->reviewer->last_name ?? 'Klien Anonim' }}
            </p>
        </div>
        @endforeach
    </div>
    @endif

    <hr class="my-6 border-gray-300">


    <hr class="my-6 border-gray-300">

    {{-- 7. Download Report --}}
    {{-- Tempatkan tombol ini di mana Anda inginkan, misalnya di dekat judul laporan --}}
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-semibold text-gray-800">Laporan Bulanan</h2>
        <div>
            <a href="{{ route('monthly.report.download.pdf', ['report_month' => app('request')->input('report_month', \Carbon\Carbon::now()->format('Y-m'))]) }}"
                class="">
                Unduh Laporan (PDF)
            </a>
        </div>
    </div>

    <hr class="my-6 border-gray-300">

    {{-- 8. Saran / Feedback dari Admin (Opsional) --}}
    @if (!empty($adminFeedback))
    <h2 class="text-2xl font-semibold text-gray-700 mb-4">💬 Saran / Feedback dari Admin</h2>
    <div class="bg-indigo-50 border-l-4 border-indigo-400 text-indigo-800 p-4 rounded-md shadow-sm mb-8" role="alert">
        <p class="font-semibold">{{ $adminFeedback }}</p>
    </div>
    @endif


</div>
<script>
    document.getElementById('reportMonth').addEventListener('change', function() {
        document.getElementById('hiddenReportMonth').value = this.value;
        document.getElementById('reportForm').submit();
    });
</script>
@endsection