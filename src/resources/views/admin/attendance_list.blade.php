@extends('admin.layouts.app')

@section('title','管理者用勤怠一覧')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance-list.css') }}">
@endsection

@section('content')
<div class="attendance-list">
    <div class="attendance-list__container">
        <h1 class="attendance-list__title">{{ $date->format('Y年n月j日') }}の勤怠</h1>
        <div class="attendance-list__header">
            <a class="attendance-list__nav" href="{{ route('admin.attendance.list', ['date' => $date->copy()->subDay()->toDateString()]) }}">
            ←前日
            </a>

            <div class="attendance-list__month">
                <i class="fa-regular fa-calendar"></i>
                {{ $date->format('Y/m/d') }}
            </div>

            <a class="attendance-list__nav" href="{{ route('admin.attendance.list', ['date' => $date->copy()->addDay()->toDateString()]) }}">
            →翌日
            </a>
        </div>

        <div class="attendance-list__content">
            <table class="attendance-list__table">
                <thead>
                    <tr>
                        <th>名前</th>
                        <th>出勤</th>
                        <th>退勤</th>
                        <th>休憩</th>
                        <th>合計</th>
                        <th>詳細</th>
                    </tr>
                </thead>

                <tbody> @foreach($attendances as $attendance)
                    <tr>
                        <td>{{ $attendance->user->name }}</td>
                        <td>{{ optional($attendance->clock_in)->format('H:i') }}</td>
                        <td>{{ optional($attendance->clock_out)->format('H:i') }}</td>

                        <td>
                        @php
                        $hours = floor($attendance->break_minutes / 60);
                        $minutes = $attendance->break_minutes % 60;
                        @endphp

                        {{ sprintf('%02d:%02d', $hours, $minutes)}}
                        </td>

                        <td>@php
                            $workMinutes = $attendance->work_minutes ?? 0;
                            @endphp

                            {{ floor($workMinutes / 60 )}}:{{ sprintf('%02d', $workMinutes % 60) }}
                        </td>

                        <td><a href="{{ route('admin.attendance.detail', $attendance->id) }}">詳細</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection