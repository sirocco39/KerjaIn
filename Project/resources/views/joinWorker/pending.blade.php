@extends('master.master-job_req')

@section('content')
{{-- Link ke Tailwind CSS sudah dihapus, diasumsikan Bootstrap sudah ada di master layout --}}

<div class="container">
    {{-- Menggunakan grid Bootstrap untuk membuat konten responsif dan terpusat --}}
    <div class="row justify-content-center align-items-center min-vh-100 py-5">
        <div class="col-lg-7 col-md-9">

            {{-- Card utama dengan kelas-kelas Bootstrap --}}
            <div class="card shadow-lg border-0 rounded-4 p-4 p-md-5 text-center">

                <div class="mb-4">
                    {{-- Ikon checkmark menggunakan kelas warna Bootstrap 'text-success' --}}
                    {{-- Ukuran diatur manual karena Bootstrap tidak punya kelas width/height seperti Tailwind --}}
                    <svg class="mx-auto text-success" style="width: 6rem; height: 6rem;" viewBox="0 0 52 52">
                        <circle cx="26" cy="26" r="25" fill="none" stroke="currentColor" stroke-width="3" />
                        <path fill="none" stroke="currentColor" stroke-width="3" d="M14.1 27.2l7.1 7.2 16.7-16.8" />
                    </svg>
                </div>

                {{-- Judul menggunakan kelas 'display' Bootstrap agar lebih besar dan menonjol --}}
                <h1 class="display-6 fw-bold text-dark mb-3">Pengajuan Verifikasi Diterima!</h1>

                {{-- Ukuran teks paragraf menggunakan 'fs-5' (font-size 5) --}}
                <p class="fs-5 mb-4 text-body-secondary">Terima kasih telah mengajukan verifikasi identitas untuk menjadi Mitra KerjaIn.</p>

                <p class="mb-5">
                    Tim kami sedang memproses pengajuan verifikasi Anda. Proses ini biasanya memakan waktu <strong>1–3 hari kerja</strong>. Anda akan menerima notifikasi melalui WhatsApp atau email setelah proses verifikasi selesai.
                </p>

                {{-- Tombol utama menggunakan kelas 'btn', 'btn-primary', dan 'btn-lg' --}}
                <a href="{{ url('/') }}"
                    class="btn btn-primary btn-lg d-inline-flex align-items-center px-4 shadow-sm text-center">
                    
                    {{-- Ikon di dalam tombol --}}
                    <svg class="me-2" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0v-9l-2 2M9 5h6m-6 0H9"></path>
                    </svg>
                    Kembali ke Beranda
                </a>

                {{-- Teks catatan kaki di bagian bawah --}}
                <div class="mt-5 text-muted small">
                    Jika ada pertanyaan, silakan hubungi kami di <a href="mailto:support@kerjain.com">support@kerjain.com</a>.
                </div>

            </div>
        </div>
    </div>
</div>
@endsection