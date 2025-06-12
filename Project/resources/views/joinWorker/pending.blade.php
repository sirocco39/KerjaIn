@extends('master.master-job_req')
@section('content')
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <div class="row justify-content-center align-items-center my-5 py-5 px-5">
        <div
            class="bg-white rounded-xl shadow-2xl p-8 md:p-12 text-center max-w-lg w-full transform transition-all duration-300 hover:scale-105">

            <div class="mb-8">
                <svg class="w-24 h-24 mx-auto text-green-500 checkmark-circle" viewBox="0 0 52 52">
                    <circle cx="26" cy="26" r="25" fill="none" stroke="currentColor" stroke-width="3" />
                    <path class="checkmark-path" fill="none" stroke="currentColor" stroke-width="3"
                        d="M14.1 27.2l7.1 7.2 16.7-16.8" />
                </svg>
            </div>

            <h1 class="text-3xl font-bold text-gray-800 mb-4">Pengajuan Verifikasi Diterima!</h1>

            <p class="text-gray-700 text-lg mb-6">Terima kasih telah mengajukan verifikasi identitas untuk menjadi Mitra
                KerjaIn.</p>


            <p class="text-gray-600 mb-8">
                Tim kami sedang memproses pengajuan verifikasi Anda. Proses ini biasanya memakan waktu <strong>1–3 hari
                    kerja</strong>.
                Anda akan menerima notifikasi melalui WhatsApp atau email setelah proses verifikasi selesai.
            </p>

            <a href="{{ url('/') }}"
                class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0v-9l-2 2M9 5h6m-6 0H9">
                    </path>
                </svg>
                Kembali ke Beranda
            </a>

            <div class="mt-8 text-gray-500 text-sm">
                Jika ada pertanyaan, silakan hubungi kami di <a href="mailto:support@kerjain.com"
                    class="text-blue-500 hover:underline">support@kerjain.com</a>.
            </div>
        </div>
    </div>
@endsection
