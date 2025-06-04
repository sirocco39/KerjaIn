@extends('Master.master-job_taker')

@section('content')
    <div class="container-fluid pembatas-x pembatas-y">
        Ini Dummy Job Taker Pesan
    </div>

    <div class="container-fluid pembatas-x pembatas-b">
        Ini Dummy Job Taker Pesan
    </div>
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
