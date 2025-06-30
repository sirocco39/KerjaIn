@extends('Master.master-job_req')

@section('content')
<pre>
{{ var_dump($fiveLatestRequests) }}
</pre>
<!-- @foreach ($fiveLatestRequests as $key => $r)
<div class="container-fluid pembatas-x pembatas-y">
    <div class="work-request">
        <h2>{{ $r->title }}</h2>
        <p>{{ $r->description }}</p>
        <p>Harga: {{ $r->price }}</p>
        <p>Lokasi: {{ $r->location }}</p>
        <p>Status: {{ $r->status }}</p>
        <?php
        // $startdatetime  = strtotime($r->start_time);
        // $enddatetime  = strtotime($r->end_time);
        // $startdate   = date('d M Y', $startdatetime);
        // $starttime = date('H.i', $startdatetime);
        // $enddate  = date('d M Y', $enddatetime);
        // $endtime = date('H.i', $enddatetime);
        ?>
        <p>Mulai: {{ $startdate }} pukul {{ $starttime }}</p>
        <p>Selesai: {{ $enddate }} pukul {{ $endtime }}</p>
        <a href="{{ route('requesttt.show', $r->slug) }}"><button>Lihat Detail</button></a>
    </div>
</div>
@endforeach -->

@endsection

{{--
    Pake template itu untuk div paling luarnya. Kalo yang paling general harusnya gini

    Bagian paling atas
    <div class="container-fluid pembatas-x pembatas-y">
        {Codingan Kalian}
    </div>

    Bagian bagian selanjutnya
    <div class="container-fluid pembatas-x pembatas-b">
        {Codingan Kalian}
    </div>

    Yang div paling luar paling aman pake template yang ku bilang diawal, misalkan dibagian dalem dalemnya
    mau pake padding atas atau bawah atau kanan kiri yang sama kek yang div luarnya, bisa pake

    pembatas-x itu biar konsisten padding kanan kirinya
    pembatas-y itu biar konsisten padding atas bawahnya
    pembatas-a itu biar konsisten padding atasnya
    pembatas-b itu biar konsisten padding bawahnya
--}}