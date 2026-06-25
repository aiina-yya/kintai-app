@extends('user.layouts.app')

@section('title','勤怠詳細画面')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance-detail.css') }}">
@endsection

@section('content')
<div class="attendance-detail">
    <div class="attendance-detail__container">

        <h1 class="attendance-detail__title">勤怠詳細</h1>
        <form action="{{ route('attendance.correction', $attendance) }}" method="post">
            @csrf
            <div class="attendance-detail__content">

                <div class="attendance-detail__row">
                    <label class="attendance-detail__label" for="name">名前</label>
                    <input class="attendance-detail__input"  type="text" id="name" value="{{ $attendance->user->name }}" readonly >
                </div>

                <div class="attendance-detail__row">
                    <label class="attendance-detail__label" for="work_date">日付</label>
                    <input class="attendance-detail__input"  type="text" id="work_date" value="{{ $attendance->work_date->format('Y年') }}" readonly>

                    <input class="attendance-detail__input"  type="text" id="work_date" value="{{ $attendance->work_date->format('n月j日') }}" readonly>
                </div>

                <div class="attendance-detail__row">
                    <label class="attendance-detail__label">出勤・退勤</label>
                    <div class="attendance-detail__times">
                        <input class="attendance-detail__input" type="text" name="clock_in" value="{{ optional($attendance->clock_in)->format('H:i') }}">

                        <span>～</span>

                        <input class="attendance-detail__input" type="text" name="clock_out" value="{{ optional($attendance->clock_out)->format('H:i') }}">
                    </div>
                </div>
                @foreach($attendance->breaks as $index => $break)
                <div class="attendance-detail__row">
                    <label class="attendance-detail__label">休憩{{ $index + 1 }}</label>

                    <input type="hidden" name="break_ids[]" value="{{ $break->id }}">

                    <div class="attendance-detail__times">
                    <input class="attendance-detail__input" type="text" name="break_start[]" value="{{ optional($break->break_start)->format('H:i') }}">

                    <span>～</span>

                    <input class="attendance-detail__input" type="text" name="break_end[]" value="{{ optional($break->break_end)->format('H:i') }}">
                    </div>
                </div>
                @endforeach

                <div class="attendance-detail__row">
                    <label class="attendance-detail__label">休憩{{ $attendance->breaks->count() + 1 }}</label>
                    <div class="attendance-detail__times">
                        <input class="attendance-detail__input" type="text">

                        <span>～</span>

                        <input class="attendance-detail__input" type="text">
                    </div>
                </div>

                <div class="attendance-detail__row">
                    <label class="attendance-detail__label" for="reason">備考</label>
                    <textarea class="attendance-detail__textarea" name="reason" id="reason">{{ old('reason', $attendance->reason ?? '') }}</textarea>
                </div>
            </div>

            <div class="attendance-detail__button">
                @if($attendance->hasPendingCorrection())
                <p class="attendance-detail__pending-message">
                    ＊承認待ちのため修正はできません。
                </p>
                @else
                <button class="attendance-detail__btn" type="submit">修正</button>
                @endif
            </div>
        </form>
    </div>
</div>
@endsection