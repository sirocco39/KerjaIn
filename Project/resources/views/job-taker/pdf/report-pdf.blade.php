<!DOCTYPE html>
<html>

<head>
    <title>Laporan Bulanan Pekerja - {{ $reportPeriod }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 10pt;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        h1,
        h2 {
            text-align: center;
        }

        .summary-box {
            border: 1px solid #eee;
            padding: 10px;
            margin-bottom: 20px;
            background-color: #f9f9f9;
        }

        .summary-box div {
            margin-bottom: 5px;
        }
    </style>
</head>

<body>
    <h1>Laporan Bulanan Pekerja</h1>
    <h2>Periode: {{ $reportPeriod }}</h2>

    <div class="summary-box">
        <div><strong>Nama Pekerja:</strong> {{ $worker->first_name . ' ' . $worker->last_name}}</div>
        <div><strong>Total Pekerjaan Selesai:</strong> {{ $totalJobsCompleted }}</div>
        <div><strong>Total Pendapatan:</strong> Rp {{ number_format($totalEarnings, 0, ',', '.') }}</div>
        <div><strong>Rating Rata-rata:</strong> {{ $averageRating }} / 5.0</div>
    </div>

    <h3>Riwayat Transaksi</h3>
    @if($jobHistory->isEmpty())
    <p>Tidak ada transaksi yang diselesaikan pada periode ini.</p>
    @else
    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Nama Klien</th>
                <th>Layanan</th>
                <th>Tanggal Selesai</th>
                <th>Estimasi Waktu</th>
                <th>Harga</th>
            </tr>
        </thead>
        <tbody>
            @foreach($jobHistory as $index => $transaction)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $transaction->request->requester->first_name . ' ' .  $transaction->request->requester->last_name ?? 'N/A' }}</td>
                <td>{{ $transaction->request->title ?? 'N/A' }}</td>
                <td>{{ \Carbon\Carbon::parse($transaction->finish_work)->translatedFormat('d F Y H:i') }}</td>
                <td>
                    @if($transaction->start_work && $transaction->finish_work)
                    {{ number_format($transaction->start_work->diffInMinutes($transaction->finish_work) / 60, 1) }} jam
                    @else
                    N/A
                    @endif
                </td>
                <td>Rp {{ number_format($transaction->request->price ?? 0, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    
    @if(!$clientReviews->isEmpty())
    <h3>Ulasan Klien</h3>
    <table>
        <thead>
            <tr>
                <th>Klien</th>
                <th>Rating</th>
                <th>Komentar</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($clientReviews as $review)
            <tr>
                <td>{{ $review->reviewer->first_name . ' ' . $review->reviewer->last_name ?? 'Anonim' }}</td>
                <td>{{ $review->rating }}</td>
                <td>{{ $review->comment }}</td>
                <td>{{ $review->created_at->translatedFormat('d F Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

</body>

</html>