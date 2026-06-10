@extends('user.layout.app')

@('title','勤怠登録画面')

@section('css')
<link rel="stylesheet" href="css/attendance.css">
@endsection

@section('content')
<div class="attendance">
    <div class="attendance__status">勤務外</div>
    <div class="attendance__datetime">
        @php
        $weekdays = ['日', '月', '火', '水', '木', '金', '土'];
        <p class="attendance__date">
            {{ now()->isoFormat('Y年n月j日') }}({{ $weekdays[now()->dayOfWeek] }})
        </p>
        <p class="attendance__time">
            {{ now()->format('H:i') }}
        </p>
    </div>

    <div class="attendance__actions">
        @if('$status === '勤務外')
        <form action="{{ route('attendance.clockIn) }}" method="post">
            @csrf
            <button class="attendance__btn" type="submit">
            出勤
            </button>
        </form>
        
        @elseif($status === '出勤中')
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

        @elseif($status === '休憩中')
        <form action="{{ route('attendance.breakEnd') }}" method="post">
            @csrf
            <button class="attendance__btn-break">
            休憩戻
            </button>
        </form>

        @elseif($status === '退勤済')
        <p class="attendance__message">
            お疲れ様でした。
        </p>
        @endif
    </div>    
</div>
@endsection