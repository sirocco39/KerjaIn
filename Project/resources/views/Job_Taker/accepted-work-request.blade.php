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

    {{-- Tombol aksi berdasarkan status --}}
    @if($transaction->status === 'accepted')
    {{-- Tombol Mulai Kerja --}}
    <form id="start-work-form" action="{{ route('worker.startWork', $transaction->id) }}" method="POST">
        @csrf
        <button type="submit" id="start-work-button" class="btn btn-primary">
            Mulai Kerja
        </button>
    </form>
    @elseif($transaction->status === 'completed')
    {{-- Tombol Sudah Dikerjakan --}}
    <button class="btn btn-success" disabled>
        Sudah Dikerjakan
    </button>
    @elseif($transaction->status === 'in progress')
    {{-- Label pekerjaan sedang berlangsung --}}
    <form action="{{ route('worker.markComplete', $transaction->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menandai pekerjaan ini selesai?');">
        @csrf
        <button class="btn btn-warning"
            data-bs-toggle="modal"
            data-bs-target="#completionProofModal">
            Selesai Kerja
        </button>
    </form>
    <!-- Modal kirim bukti penyelesaian -->
    <div class="modal fade" id="completionProofModal" tabindex="-1" aria-labelledby="completionProofModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content p-3">
                <form action="{{ route('worker.uploadProof', $transaction->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label for="photo" class="form-label">Upload Foto Bukti Pekerjaan</label>
                        <input type="file" name="photo" id="photo" class="form-control" accept="image/*" required>
                    </div>

                    <div class="mb-3">
                        <label for="note" class="form-label">Catatan (Opsional)</label>
                        <textarea name="note" id="note" rows="3" class="form-control"></textarea>
                    </div>

                    <button type="submit" class="btn btn-success">Upload Bukti</button>
                </form>
            </div>
        </div>
    </div>

    {{-- Batalkan kerja --}}
    <form action="{{ route('transaction.cancel', $transaction->id) }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-danger">Ya, Tetap Batalin</button>
    </form>

    @endif

    @endsection