@extends('Master.master-job_taker')

@section('content')
    @livewire('job-taker.chat', ['selectedRoomId' => $chatRoomId ?? null])
@endsection