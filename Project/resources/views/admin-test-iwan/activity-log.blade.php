@extends('master.master-job-req')
@section('content')
<h1>Log Aktivitas Terbaru</h1>

<table class="table">
    <thead>
        <tr>
            <th>Deskripsi</th>
            <th>Pelaku (Causer)</th>
            <th>Target (Subject)</th>
            <th>Waktu</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($activities as $activity)
        <tr>
            <td>{!! $activity->description !!}</td>
            <td>
                @if ($activity->causer)
                {{ $activity->causer->first_name }} (ID: {{ $activity->causer->id }})
                @else
                Sistem
                @endif
            </td>
            <td>
                @if ($activity->subject)
                {{ class_basename($activity->subject_type) }} (ID: {{ $activity->subject->id }})
                @else
                -
                @endif
            </td>
            <td>{{ $activity->created_at->format('d M Y, H:i') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection