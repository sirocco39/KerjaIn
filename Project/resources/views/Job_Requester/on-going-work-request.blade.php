@extends('Master.master-job_req')

@section('content')

    <div class="d-flex flex-column align-items-start">

        {{-- Badge status dinamis label --}}
        @if($transaction->status == 'submitted')
            <span class="badge rounded-pill bg-primary mb-2">Ditinjau</span>
        @elseif($transaction->status == 'cancelled')
            <span class="badge rounded-pill bg-warning text-dark mb-2">Dibatalin</span>
        @elseif($transaction->status == 'accepted')
            <span class="badge rounded-pill bg-warning text-dark mb-2">Diterima</span>
        @elseif($transaction->status == 'in progress')
            <span class="badge rounded-pill bg-info text-dark mb-2">Dikerjain</span>
        @elseif($transaction->status == 'completed')
            <span class="badge rounded-pill bg-success mb-2">Selesai</span>
        @endif

        {{-- Batalkan --}}
        <form action="{{ route('transaction.cancel', $transaction->id) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-danger">Ya, Tetap Batalin</button>
        </form>

        <!-- Tombol Tandai Selesai -->
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#completionModal">
            Ya, Selesaikan Pekerjaan
        </button>

        <!-- Modal Tandai selesai-->
        <div class="modal fade" id="completionModal" tabindex="-1" aria-labelledby="completionModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="completionModalLabel">Detail Penyelesaian</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p><strong>Judul Pesanan:</strong> {{ $request->title }}</p>
                        <p><strong>Nomor Pesanan:</strong> {{ $orderNumber }}</p>
                        <p><strong>Nama Pekerja:</strong> {{ $worker->first_name }} {{ $worker->last_name }}</p>
                        <p><strong>Lokasi:</strong> {{ $request->location }} </p>
                        <p><strong>Tanggal Pemesanan:</strong> {{ $transaction->created_at->format('d M Y') }} </p>
                        <p><strong>Tanggal Selesai:</strong> {{ $transaction->updated_at->format('d M Y') }} </p>
                        <p><strong>Total:</strong> Rp {{ number_format($request->price, 2, ',', '.') }} </p>
                    </div>
                    <h5 class="text-center">Kasih penilaian, yuk!</h5>
                        <div class="text-center mb-3">
                            @for ($i = 1; $i <= 5; $i++)
                                <i class="bi bi-star-fill text-secondary fs-2 star-rating" data-value="{{ $i }}"></i>
                            @endfor
                        </div>

                        <input type="hidden" name="rating" id="ratingInput">
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="button" class="btn btn-primary">Kirim Penilaian</button>
                    </div>

                </div>
            </div>
        </div>


        {{-- Lihat Bukti Penyelesaian --}}
        <a href="#" data-bs-toggle="modal" data-bs-target="#completionProofModal" style="color:#a7a7a7; text-decoration: none">
            Lihat Bukti Penyelesaian
        </a>

        <!-- Modal lihat bukti penyelesaian -->
        <div class="modal fade" id="completionProofModal" tabindex="-1" aria-labelledby="completionProofModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content p-3">
                    <div class="modal-header">
                        <h5 class="modal-title" id="completionProofModalLabel">Bukti Penyelesaian</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body text-center">
                        {{-- Tempat Foto Bukti --}}
                        <img src="{{ $completionProof->photo_url ?? 'tidak ada bukti foto dari pekerja.' }}" 
                            alt="Bukti Foto" 
                            class="img-fluid mb-3 rounded">

                        {{-- Note dari pekerja --}}
                        <p>{{ $completionProof->note ?? 'Tidak ada catatan dari pekerja.' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
