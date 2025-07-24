@extends('master.master-job-taker')

@section('content')
    @livewire('job-taker.chat', ['selectedRoomId' => $chatRoomId ?? null])
@endsection