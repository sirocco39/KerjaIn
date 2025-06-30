@extends('Master.master-job_req')

@section('content')
@foreach ($fiveLatestRequests as $r)
<div class="container-fluid pembatas-x pembatas-y">
    <div class="work-request">
        <h2>{{ $r->title }}</h2>
        <p>{{ $r->description }}</p>
        <p>Harga: {{ $r->price }}</p>
        <p>Lokasi: {{ $r->location }}</p>
        <p>Status: {{ $r->status }}</p>
        <?php
        $startdatetime  = strtotime($r->start_time);
        $enddatetime  = strtotime($r->end_time);
        $startdate   = date('d M Y', $startdatetime);
        $starttime = date('H.i', $startdatetime);
        $enddate  = date('d M Y', $enddatetime);
        $endtime = date('H.i', $enddatetime);
        ?>
        <p>Mulai: {{ $startdate }} pukul {{ $starttime }}</p>
        <p>Selesai: {{ $enddate }} pukul {{ $endtime }}</p>
        <a href="{{ route('requesttt.show', $r->slug) }}"><button>Lihat Detail</button></a>
    </div>
    <div class="dropdown text-end">
        <button class="btn btn-light border-0" type="button" id="dropdownMenuButton{{ $loop->index }}" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-three-dots-vertical"></i>
        </button>
        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $loop->index }}">
            <li><a class="dropdown-item" href="{{ route('requesttt.edit', $r->slug) }}">Edit</a></li>
            <li>
                <form action="{{ route('requesttt.destroy', $r->slug) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                    @csrf
                    @method('DELETE')
                    <button class="dropdown-item text-danger" type="submit">Delete</button>
                </form>
            </li>
        </ul>
    </div>

</div>
@endforeach
@endsection