<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Pekerja - Detail Kontrak</title>
    {{-- @vite('resources/css/app.css') --}}
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center font-sans">
    <div class="relative w-full max-w-4xl bg-white rounded-lg shadow-xl overflow-hidden">
        <div class="absolute top-0 right-0 p-6 flex items-center text-blue-700 font-semibold text-lg">
            <span>Jadi bagian Kerja</span>
            <div class="ml-1 bg-blue-700 text-white w-8 h-8 flex items-center justify-center rounded text-xl font-bold">
                In
            </div>
        </div>
        <div class="absolute top-0 left-0 w-full h-32 bg-gradient-to-br from-green-300 via-blue-300 to-indigo-300 opacity-50 z-0"></div>

        <div class="relative z-10 p-10 mt-10">
            <div class="flex justify-center mb-10 text-gray-500">
                <div class="flex flex-col items-center mx-4">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold mb-2
                        {{ true ? 'bg-blue-600 text-white' : 'bg-gray-300 text-gray-700' }}">1</div>
                    <span class="{{ true ? 'text-blue-600 font-semibold' : '' }}">Data Pribadi</span>
                </div>
                <div class="flex flex-col items-center mx-4">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold mb-2
                        {{ true ? 'bg-blue-600 text-white' : 'bg-gray-300 text-gray-700' }}">2</div>
                    <span class="{{ true ? 'text-blue-600 font-semibold' : '' }}">Detail Kontrak</span>
                </div>
                <div class="flex flex-col items-center mx-4">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold mb-2
                        {{ false ? 'bg-blue-600 text-white' : 'bg-gray-300 text-gray-700' }}">3</div>
                    <span class="{{ false ? 'text-blue-600 font-semibold' : '' }}">Verifikasi</span>
                </div>
            </div>

            <h2 class="text-2xl font-bold text-gray-800 mb-6">Detail Kontrak</h2>

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('worker.register.store2') }}" method="POST" id="contractForm">
                @csrf

                <div class="text-gray-700 text-sm leading-relaxed space-y-4 mb-8 p-4 border border-gray-200 rounded-md bg-gray-50 max-h-96 overflow-y-auto">
                    <h3 class="font-semibold text-lg text-gray-800">1. Identitas Pihak-Pihak</h3>
                    <p>Pemberi Kerja:<br>
                        Nama: [Nama Pemberi Kerja]<br>
                        No. HP: [Nomor HP]<br>
                        Alamat: [Alamat Opsional]</p>
                    <p>Pekerja:<br>
                        Nama: [Nama Worker]<br>
                        No. KTP: [NIK Worker]<br>
                        No. HP: [Nomor HP]<br>
                        Alamat: [Alamat Domisili]</p>

                    <h3 class="font-semibold text-lg text-gray-800 mt-4">2. Deskripsi Pekerjaan</h3>
                    <p>Jenis Pekerjaan: [Misalnya: Bersih-bersih rumah, bantu angkat barang, jaga stand, dll]<br>
                        Lokasi Pekerjaan: [Alamat tempat kerja]<br>
                        Kebutuhan Khusus: [Jika ada, misal bawa alat, pakaian tertentu, dsb.]</p>

                    <h3 class="font-semibold text-lg text-gray-800 mt-4">3. Upah dan Pembayaran</h3>
                    <ul class="list-disc list-inside ml-4">
                        <li>Upah yang Disepakati: Rp [jumlah nominal]</li>
                        <li>Metode Pembayaran: [Tunai / Transfer / E-Wallet]</li>
                        <li>Waktu Pembayaran: [Setelah pekerjaan selesai / Sesuai kesepakatan]</li>
                    </ul>

                    <h3 class="font-semibold text-lg text-gray-800 mt-4">4. Hak dan Kewajiban Pekerja.</h3>
                    <ul class="list-disc list-inside ml-4">
                        <li>Melakukan pekerjaan sesuai deskripsi & waktu.</li>
                        <li>Datang tepat waktu & bertanggung jawab atas pekerjaannya.</li>
                        <li>Menjaga etika dan sopan santun selama bekerja.</li>
                    </ul>
                    <p>Pemberi Kerja:</p>
                    <ul class="list-disc list-inside ml-4">
                        <li>Memberikan informasi kerja yang jelas.</li>
                        <li>Menyediakan lingkungan kerja yang aman.</li>
                        <li>Membayar upah sesuai kesepakatan.</li>
                    </ul>

                    <h3 class="font-semibold text-lg text-gray-800 mt-4">5. Pembatalan & Sanksi</h3>
                    <ul class="list-disc list-inside ml-4">
                        <li>Jika salah satu pihak ingin membatalkan, harus memberi pemberitahuan minimal [6-12 jam] sebelumnya.</li>
                        <li>Jika Pekerja tidak datang tanpa alasan jelas, akun dapat dikenai peringatan.</li>
                        <li>Jika Pemberi kerja membatalkan sepihak tanpa pemberitahuan, bisa diberi rating buruk atau penalti sesuai kebijakan KerjaIn.</li>
                    </ul>

                    <h3 class="font-semibold text-lg text-gray-800 mt-4">6. Penutup</h3>
                    <p>Kontrak ini dibuat sebagai bentuk kesepakatan antara kedua belah pihak agar pekerjaan berjalan lancar dan adil. Dengan menekan tombol "Setuju dan Mulai Kerja", kedua pihak menyatakan telah membaca dan menyetujui isi kontrak ini.</p>
                </div>

                <div class="mt-6 space-y-3">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="agree_terms" id="agree_terms" class="form-checkbox h-5 w-5 text-blue-600 rounded" value="1" required>
                        <span class="ml-2 text-gray-700 text-base">Saya setuju dengan <a href="#" class="text-blue-600 hover:underline">Syarat dan Ketentuan KerjaIn</a></span>
                    </label>
                    <p id="error-agree_terms" class="text-red-500 text-sm ml-7 -mt-2 @unless($errors->has('agree_terms')) hidden @endunless">
                        {{ $errors->first('agree_terms') }}
                    </p>

                    <label class="inline-flex items-center">
                        <input type="checkbox" name="agree_data_usage" id="agree_data_usage" class="form-checkbox h-5 w-5 text-blue-600 rounded" value="1" required>
                        <span class="ml-2 text-gray-700 text-base">Saya bersedia data saya digunakan untuk keperluan verifikasi dan keamanan</span>
                    </label>
                    <p id="error-agree_data_usage" class="text-red-500 text-sm ml-7 -mt-2 @unless($errors->has('agree_data_usage')) hidden @endunless">
                        {{ $errors->first('agree_data_usage') }}
                    </p>
                </div>

                <div class="mt-10 flex justify-between">
                    <a href="{{ route('worker.register.step1') }}"
                       class="inline-flex justify-center py-2 px-8 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Sebelumnya
                    </a>
                    <button type="submit" id="submitButton"
                            class="inline-flex justify-center py-2 px-8 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Lanjut
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('contractForm').addEventListener('submit', function(event) {
            const agreeTerms = document.getElementById('agree_terms');
            const agreeDataUsage = document.getElementById('agree_data_usage');
            const errorAgreeTerms = document.getElementById('error-agree_terms');
            const errorAgreeDataUsage = document.getElementById('error-agree_data_usage');

            let isValid = true;

            // Reset error messages
            errorAgreeTerms.classList.add('hidden');
            errorAgreeDataUsage.classList.add('hidden');
            errorAgreeTerms.textContent = '';
            errorAgreeDataUsage.textContent = '';

            // Validate 'agree_terms' checkbox
            if (!agreeTerms.checked) {
                errorAgreeTerms.textContent = 'Anda harus menyetujui Syarat dan Ketentuan KerjaIn.';
                errorAgreeTerms.classList.remove('hidden');
                isValid = false;
            }

            // Validate 'agree_data_usage' checkbox
            if (!agreeDataUsage.checked) {
                errorAgreeDataUsage.textContent = 'Anda harus menyetujui penggunaan data.';
                errorAgreeDataUsage.classList.remove('hidden');
                isValid = false;
            }

            if (!isValid) {
                event.preventDefault(); // Stop the form from submitting
            }
        });
    </script>
</body>
</html>