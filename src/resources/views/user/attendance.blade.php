@extends('user.layouts.app')

@section('title','勤怠登録画面')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
@endsection

@section('content')
<div class="attendance">
    <p class="attendance__status">{{ $status }}</p>
    <div class="attendance__datetime">
        @php
        $weekdays = ['日', '月', '火', '水', '木', '金', '土'];
        @endphp
        <p class="attendance__date">
            {{ now()->isoFormat('Y年M月D日') }}({{ $weekdays[now()->dayOfWeek] }})
        </p>
        <p class="attendance__time">
            {{ now()->format('H:i') }}
        </p>
    </div>

    <div class="attendance__actions">

        @if(!$attendance)
        <form action="{{ route('attendance.clockIn') }}" method="post">
            @csrf
            <button class="attendance__btn" type="submit">
            出勤
            </button>
        </form>

        @elseif($attendance->clock_out)
        <p class="attendance__message">
            お疲れ様でした。
        </p>

        @elseif($attendanceBreak && is_null($attendanceBreak->break_end))
        <form action="{{ route('attendance.breakEnd') }}" method="post">
            @csrf
            <button class="attendance__btn-break" type="submit">
            休憩戻
            </button>
        </form>

        @else
        <form action="{{ route('attendance.clockOut') }}" method="post">
            @csrf
            <button class="attendance__btn" type="submit">
            退勤
            </button>
        </form>
        <form action="{{ route('attendance.breakStart') }}" method="post">
            @csrf
            <button class="attendance__btn-break" type="submit">
            休憩入
            </button>
        </form>
        @endif
    </div>
</div>
@endsection