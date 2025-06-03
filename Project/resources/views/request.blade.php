<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<body>
    <nav></nav>
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
        </div>
        <a href="{{ url()->previous() }}"><button>Kembali</button></a>
        <a href="/edit/{{ $workRequest->slug }}"><button>Sunting</button></a>
        <button>Batal Ajukan</button>
    </main>
    <footer></footer>
</body>

</html>