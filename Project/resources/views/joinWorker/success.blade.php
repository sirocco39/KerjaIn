<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Berhasil! - KerjaIn</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        .checkmark-circle {
            animation: scaleIn 0.5s ease-out forwards;
        }
        .checkmark-path {
            animation: drawLine 0.75s ease-out forwards 0.2s;
            stroke-dasharray: 1000;
            stroke-dashoffset: 1000;
        }
        @keyframes scaleIn {
            0% { transform: scale(0); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }
        @keyframes drawLine {
            to { stroke-dashoffset: 0; }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-100 to-indigo-200 min-h-screen flex items-center justify-center p-4">

    <div class="bg-white rounded-xl shadow-2xl p-8 md:p-12 text-center max-w-lg w-full transform transition-all duration-300 hover:scale-105">

        <div class="mb-8">
            <svg class="w-24 h-24 mx-auto text-green-500 checkmark-circle" viewBox="0 0 52 52">
                <circle cx="26" cy="26" r="25" fill="none" stroke="currentColor" stroke-width="3"/>
                <path class="checkmark-path" fill="none" stroke="currentColor" stroke-width="3" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
            </svg>
        </div>

        <h1 class="text-3xl font-bold text-gray-800 mb-4">Pendaftaran Berhasil!</h1>

        @if (session('success'))
            <p class="text-green-600 text-lg mb-6">{{ session('success') }}</p>
        @else
            <p class="text-gray-700 text-lg mb-6">Terima kasih telah mendaftar. Permintaan Anda telah berhasil disubmit.</p>
        @endif

        <p class="text-gray-600 mb-8">
            Tim kami akan segera memproses verifikasi data Anda. Proses ini mungkin memakan waktu **1-3 hari kerja**.
            Anda akan menerima notifikasi melalui WhatsApp atau email setelah proses verifikasi selesai.
        </p>

        <a href="{{ url('/') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0v-9l-2 2M9 5h6m-6 0H9"></path></svg>
            Kembali ke Beranda
        </a>

        <div class="mt-8 text-gray-500 text-sm">
            Jika ada pertanyaan, hubungi <a href="mailto:support@kerjain.com" class="text-blue-500 hover:underline">support@kerjain.com</a>.
        </div>
    </div>

</body>
</html>