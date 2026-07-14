@extends('admin.layouts.app')

@section('title','修正承認画面')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance-detail.css') }}">
@endsection

@section('content')
<div class="attendance-detail">
    <div class="attendance-detail__container">

        <h1 class="attendance-detail__title">勤怠詳細</h1>
        <form action="{{ route('admin.approve', $correction->id) }}" method="post">
            @csrf
            @method('PATCH')
            <div class="attendance-detail__content">

                <div class="attendance-detail__row">
                    <label class="attendance-detail__label" for="name">名前</label>
                    <input class="attendance-detail__input"  type="text" id="name" value="{{ $correction->attendance->user->name }}" readonly >
                </div>

                <div class="attendance-detail__row">
                    <label class="attendance-detail__label" for="work_date">日付</label>
                    <input class="attendance-detail__input"  type="text" id="work_date" value="{{ $correction->attendance->work_date->format('Y年') }}" readonly>

                    <input class="attendance-detail__input"  type="text" id="work_date" value="{{ $correction->attendance->work_date->format('n月j日') }}" readonly>
                </div>

                <div class="attendance-detail__row">
                    <label class="attendance-detail__label">出勤・退勤</label>
                    <div class="attendance-detail__times">
                        <input class="attendance-detail__input" type="time" value="{{ optional($correction->requested_clock_in)->format('H:i') }}" readonly>

                        <span>～</span>

                        <input class="attendance-detail__input" type="time" value="{{ optional($correction->requested_clock_out)->format('H:i') }}"readonly>
                    </div>
                </div>
                @foreach($correction->breaks as $index => $break)
                <div class="attendance-detail__row">
                    <label class="attendance-detail__label">休憩{{ $index + 1 }}</label>

                    <input type="hidden" name="break_ids[]" value="{{ $break->id }}">

                    <div class="attendance-detail__times">
                    <input class="attendance-detail__input" type="time" name="break_start[]" value="{{$break->requested_break_start->format('H:i') }}" readonly>

                    <span>～</span>

                    <input class="attendance-detail__input" type="time" name="break_end[]" value="{{$break->requested_break_end->format('H:i') }}" readonly>
                    </div>
                </div>
                @endforeach

                <div class="attendance-detail__row">
                    <label class="attendance-detail__label" for="reason">備考</label>
                    <textarea class="attendance-detail__textarea" name="reason" id="reason" readonly>{{ old('reason', $correction->reason ?? '') }}</textarea>
                </div>
            </div>

            <div class="attendance-detail__button">
                <button class="attendance-detail__btn" type="submit">承認</button>
            </div>
        </form>
    </div>
</div>
@endsection