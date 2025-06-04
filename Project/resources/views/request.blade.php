@extends('Master.master-job_req')
@section('content')
<main>
    <h1>Judul : {{ $workRequest->title }}</h1>
    <div class="work-details">
        <p>Description : {{ $workRequest->description }}</p>
        <p>Harga : {{ $workRequest->price }}</p>
        <p>Lokasi : {{ $workRequest->location }}</p>
        <p>Status : {{ $workRequest->status }}</p>
        <?php
        $startdatetime  = strtotime($workRequest->start_time);
        $enddatetime  = strtotime($workRequest->end_time);
        $startdate   = date('d M Y', $startdatetime);
        $starttime = date('H.i', $startdatetime);
        $enddate  = date('d M Y', $enddatetime);
        $endtime = date('H.i', $enddatetime);
        ?>
        <p>Mulai: {{ $workRequest->start_time }} pukul {{ $workRequest->start_time }}</p>
        <p>Selesai: {{ $workRequest->end_time }} pukul {{ $workRequest->end_time }}</p>
        <!-- other details -->
        <a href="{{ url()->previous() }}"><button>Kembali</button></a>
        <a href="{{ route('requesttt.edit',  $workRequest->slug) }}"><button>Sunting</button></a>
        <form action="{{ route('requesttt.destroy', $workRequest->slug) }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit">Batal Ajukan</button>
        </form>
    </div>

</main>
@endsection()