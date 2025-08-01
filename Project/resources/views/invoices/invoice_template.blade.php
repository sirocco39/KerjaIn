<!DOCTYPE html>
<html>

<head>
    <title>Invoice #{{ $transaction->order_number ?? 'N/A' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Manrope:wght@400;600;700&display=swap"
        rel="stylesheet">
    <style>
        @page {
            margin: 10mm;
        }

        body {
            font-family: 'Manrope', 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            font-size: 12pt;
            -webkit-print-color-adjust: exact;
            color-adjust: exact;
            position: relative;
        }

        .header-outside-box {
            text-align: center;
            margin-bottom: 15pt;
            padding-top: 5pt;
        }

        .header-outside-box img {
            width: 100pt;
            margin-bottom: 3pt;
        }

        .header-outside-box p {
            font-size: 10pt;
            color: #555555;
            margin: 0;
        }

        .outer-invoice-border-box {
            flex-grow: 1;
            border: 2pt solid #D3FA0D;
            border-radius: 12pt;
            background-color: #FFFFFF;
            padding: 0pt;
            box-shadow: 0 0 8pt rgba(0, 0, 0, 0.05);
            margin-bottom: 20pt;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .invoice-content-box {
            background-color: #f7f7ff;
            border-radius: 8pt;
            padding: 16pt;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .row-flex {
            display: table;
            width: 100%;
            table-layout: fixed;
            border-spacing: 0;
        }

        .col-flex {
            display: table-cell;
            vertical-align: top;
            padding: 2pt 5pt;
            box-sizing: border-box;
        }

        .align-left {
            text-align: left;
        }

        .align-center {
            text-align: center;
        }

        .align-right {
            text-align: right;
        }

        .invoice-title {
            font-size: 20pt;
            font-weight: bold;
            color: #294287;
            margin-bottom: 20pt;
            text-align: center;
        }

        .details-section {
            margin-bottom: 15pt;
            margin-top: 35pt;
        }

        .details-section p {
            margin: 0;
            padding: 1pt 0;
            font-size: 10pt;
        }

        .details-section .label {
            color: #666666;
            font-weight: 600;
            white-space: nowrap;
            padding-right: 5pt;
        }

        .details-section .value {
            font-weight: bold;
            color: #333333;
        }

        .section-title {
            font-size: 14pt;
            font-weight: bold;
            color: #333333;
            margin-top: 15pt;
            margin-bottom: 5pt;
            border-bottom: 0.5pt solid #eeeeee;
            padding-bottom: 3pt;
        }

        .content-with-col-padding {
            padding: 2pt 5pt;
        }

        .description-content {
            font-size: 10pt;
            line-height: 1.5;
            color: #555555;
            margin-bottom: 15pt;
            flex-grow: 1;
            min-height: 40pt;
            max-height: 219pt;
            overflow: hidden;
        }

        .total-section {
            text-align: right;
            margin-top: 18pt;
            padding-top: 10pt;
            border-top: 0.5pt solid #eeeeee;
            display: inline-flex;
            justify-content: flex-end;
            align-items: baseline;
            width: 100%;
        }

        .total-section .label {
            font-size: 12pt;
            font-weight: bold;
            color: #333333;
            margin-right: 8pt;
        }

        .total-section .amount {
            font-size: 16pt;
            font-weight: bold;
            color: #309FFF;
        }

        .footer-message {
            text-align: center;
            margin-top: 20pt;
            margin-bottom: 20pt;
            font-size: 10pt;
            color: #777777;
        }

        .contact-info {
            margin-top: 15pt;
            padding-top: 10pt;
            border-top: 0.5pt solid #eeeeee;
            font-size: 10pt;
            color: #555555;
            padding: 0 5pt;
        }

        .contact-info p {
            margin: 1pt 0;
            white-space: nowrap;
        }

        .right-aligned-content {
            float: right;
            clear: both;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }

        .right-aligned-content p {
            text-align: left;
        }

        .midcol {
            padding-left: 3.125rem;
        }

        .endcol {
            padding-left: 5.125rem;
        }
    </style>
</head>

<body>
    <div class="header-outside-box">
        <img src="{{ $base64Logo }}" alt="Logo Kerjain">
    </div>

    <div class="outer-invoice-border-box">
        <div class="invoice-content-box">
            <h2 class="invoice-title">Invoice #{{ $transaction->order_number ?? 'N/A' }}</h2>

            <div class="details-section">

                <div class="row-flex">
                    <div class="col-flex align-left">
                        <div class="row-flex">
                            <p><span class="label">Nama Klien:</span></p>
                        </div>
                        <div class="row-flex">
                            <p><span class="value">{{ $transaction->requester->full_name ?? 'N/A' }}</span></p>
                        </div>
                    </div>
                    <div class="col-flex midcol">

                    </div>
                    <div class="col-flex align-left endcol">
                        <div class="row-flex">
                            <p><span class="label">Nama Pekerja:</span></p>
                        </div>
                        <div class="row-flex">
                            <p><span class="value">{{ $transaction->worker->full_name ?? 'N/A' }}</span></p>
                        </div>
                    </div>
                </div>


                <div class="row-flex">
                    <div class="col-flex align-left">
                        <div class="row-flex">
                            <p><span class="label">No. Pemesanan:</span></p>
                        </div>
                        <div class="row-flex">
                            <p><span class="value">{{ $transaction->order_number ?? 'N/A' }}</span></p>
                        </div>
                    </div>
                    <div class="col-flex align-left midcol">
                        <div class="row-flex">
                            <p><span class="label">Tanggal Pemesanan:</span></p>
                        </div>
                        <div class="row-flex">
                            <p><span
                                    class="value">{{ \Carbon\Carbon::parse($transaction->created_at)->format('d - m - Y') }}</span>
                            </p>
                        </div>
                    </div>
                    <div class="col-flex align-left endcol">
                        <div class="row-flex">
                            <p><span class="label">Tanggal Selesai:</span></p>
                        </div>
                        <div class="row-flex">
                            <p><span
                                    class="value">{{ \Carbon\Carbon::parse($transaction->updated_at)->format('d - m - Y') }}</span>
                            </p>
                        </div>
                    </div>
                </div>


                <div class="row-flex">
                    <div class="col-flex align-left">
                        <div class="row-flex">
                            <p><span class="label">Lokasi:</span></p>
                        </div>
                        <div class="row-flex">
                            <p><span class="value">{{ $transaction->request->location ?? 'N/A' }}</span></p>
                        </div>
                    </div>
                    <div class="col-flex align-left midcol">
                        <div class="row-flex">
                            <p><span class="label">Jam Mulai Kerja:</span></p>
                        </div>
                        <div class="row-flex">
                            <p><span
                                    class="value">{{ \Carbon\Carbon::parse($transaction->start_work)->format('H.i') ?? 'N/A' }}</span>
                            </p>
                        </div>
                    </div>
                    <div class="col-flex align-left endcol">
                        <div class="row-flex">
                            <p><span class="label">Jam Selesai Kerja:</span></p>
                        </div>
                        <div class="row-flex">
                            <p><span
                                    class="value">{{ \Carbon\Carbon::parse($transaction->finish_work)->format('H.i') ?? 'N/A' }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>


            <div class="content-with-col-padding">
                <div class="section-title">Judul Pesanan</div>
                <p class="description-content">{{ $transaction->request->title ?? 'N/A' }}</p>
            </div>

            <div class="content-with-col-padding">
                <div class="section-title">Deskripsi Pesanan</div>
                <p class="description-content">
                    {{ $transaction->request->description ?? 'N/A' }}
                </p>
            </div>


            <div class="total-section">
                <span class="label">Total :</span>
                <span class="amount">Rp {{ number_format($transaction->request->price ?? 0, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <p class="footer-message">Memberikan layanan terbaik, setiap saat.</p>

    <div class="row-flex contact-info">
        <div class="col-flex contact-col">
            <p class="p-0 m-0">PT Global Kerjaln Network</p>
            <p class="p-0 m-0">Jl. Pakuan No.3, Sumur Batu, Kec. Babakan Madang,</p>
            <p class="p-0 m-0">Kabupaten Bogor, Jawa Barat 16810</p>
        </div>
        <div class="col-flex right-aligned-content">
            <p class="p-0 m-0">kerjain@gmail.com</p>
            <p class="p-0 m-0">www.kerjain.com</p>
            <p class="p-0 m-0">+62 853-7721-4287</p>
        </div>
    </div>
</body>

</html>
