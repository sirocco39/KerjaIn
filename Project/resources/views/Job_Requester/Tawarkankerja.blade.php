@extends('layouts.app') <!-- Jika kamu pakai layout, atau bisa hapus ini -->

@section('content')
<div class="container">
    <div class="form-container">
        <div class="form-left">
            <h1>Buat tawaran kerja</h1>
            <p>Yuk, mulai! Isi detail pekerjaan agar mitra kami bisa segera membantumu.</p>
            
            <form>
                <label for="judul">Judul Pekerjaan</label>
                <input type="text" id="judul" placeholder="Contoh: Masangin AC Ruang Tamu">

                <label for="detail">Detail Pekerjaan</label>
                <textarea id="detail" placeholder="Contoh: Pasang AC 1 PK di ruang tamu bagian atas korden"></textarea>

                <label for="alamat">Alamat</label>
                <input type="text" id="alamat" placeholder="Contoh: Jalan Pakuan No 3, Sentul">

                <label for="tanggal">Tanggal</label>
                <input type="date" id="tanggal" value="2025-05-09">

                <label for="waktu">Waktu</label>
                <div class="waktu">
                    <input type="time" id="start-time" value="09:09"> –
                    <input type="time" id="end-time" value="09:09">
                </div>

                <label for="biaya">Biaya Jasa</label>
                <input type="text" id="biaya" placeholder="Contoh: 150.000">

                <button type="submit">Buat Tawaran Kerja</button>
            </form>
        </div>

        <div class="form-right">
            <img src="{{ asset('images/pekerja-ilustrasi.png') }}" alt="Ilustrasi Pekerja">
        </div>
    </div>
</div>
@endsection


