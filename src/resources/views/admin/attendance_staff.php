@extends('admin.layouts.app')

@section('title','スタッフ別勤怠一覧')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance-list.css') }}">
@endsection

@section('content')
<div class="attendance-list">
    <div class="attendance-list__container">
        <h1 class="attendance-list__title">{{ $user->name }}</h1>
        <div class="attendance-list__header">
            <a class="attendance-list__nav" href="?year={{ $month == 1 ? $year - 1 : $year }}&month={{ $month == 1 ? 12 : $month - 1 }}">
            ←前月
            </a>

            <div class="attendance-list__month">
                <i class="fa-regular fa-calendar"></i>
                {{ $year }}/{{ $month }}
            </div>

            <a class="attendance-list__nav" href="?year={{ $month == 12 ? $year + 1 : $year }}&month={{ $month == 12 ? 1 : $month + 1 }}">
            →翌月
            </a>
        </div>

        <div class="attendance-list__content">
            <table class="attendance-list__table">
                <thead>
                    <tr>
                        <th>日付</th>
                        <th>出勤</th>
                        <th>退勤</th>
                        <th>休憩</th>
                        <th>合計</th>
                        <th>詳細</th>
                    </tr>
                </thead>

                <tbody> @foreach($attendances as $attendance)
                    <tr>
                        <td>{{ $attendance->work_date->format('Y/m/d') }}</td>
                        <td>{{ optional($attendance->clock_in)->format('H:i') }}</td>
                        <td>{{ optional($attendance->clock_out)->format('H:i') }}</td>

                        <td>
                        @php
                        $breakMinutes = 0;
                        foreach ($attendance->breaks as $break) {
                        if ($break->break_end) {
                            $breakMinutes += $break->break_end->diffInMinutes($break->break_start);
                            }
                        }
                        @endphp

                        {{ floor($breakMinutes / 60) }}:{{ sprintf('%02d', $breakMinutes % 60) }}
                        </td>

                        <td>{{ $attendance->work_minutes }}</td>

                        <td><a href="{{ route('attendance.detail', $attendance->id) }}">詳細</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
