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
                        <input class="attendance-detail__input" type="time" name="clock_in" value="{{ old('clock_in', optional($correction?->requested_clock_in ?? $attendance->clock_in)->format('H:i')) }}" {{ $readonly ? 'readonly' : '' }}>

                        <span>～</span>

                        <input class="attendance-detail__input" type="time" name="clock_out" value="{{ old('clock_out',optional($correction?->requested_clock_out ??  $attendance->clock_out)->format('H:i')) }}" {{ $readonly ? 'readonly' : '' }}>
                        <p class="register-form__error-message">
                            @error('clock_in')
                            {{ $message }}
                            @enderror
                        </p>
                    </div>
                </div>

                @foreach($attendance->breaks as $index => $break)
                <div class="attendance-detail__row">
                    <label class="attendance-detail__label">休憩{{ $index + 1 }}</label>

                    <input type="hidden" name="break_ids[]" value="{{ $break->id }}">

                    <div class="attendance-detail__times">
                    <input class="attendance-detail__input" type="time" name="break_start[]" value="{{ old('break_start.'.$index, optional($readonly ? $correction?->breaks->get($index)?->requested_break_start : $break->break_start)->format('H:i')) }}" {{ $readonly ? 'readonly' : '' }}>
                    <p class="register-form__error-message">
                        @error("break_start.$index")
                        {{ $message }}
                        @enderror
                    </p>

                    <span>～</span>

                    <input class="attendance-detail__input" type="time" name="break_end[]" value="{{ old('break_end.'.$index , optional($readonly ? $correction?->breaks->get($index)?->requested_break_end : $break->break_end)->format('H:i')) }}" {{ $readonly ? 'readonly' : '' }}>
                    <p class="register-form__error-message">
                        @error("break_end.$index")
                        {{ $message }}
                        @enderror
                    </p>
                    </div>
                </div>
                @endforeach

                @php
                $newIndex = $attendance->breaks->count();
                @endphp

                <div class="attendance-detail__row">
                    <label class="attendance-detail__label">休憩{{ $attendance->breaks->count() + 1 }}</label>

                    <input type="hidden" name="break_ids[]" value="">

                    <div class="attendance-detail__times">
                        @php
                        $correctionBreak = $readonly ? $correction->breaks[$newIndex] ?? null : null;
                        @endphp

                        <input class="attendance-detail__input" type="time" name="break_start[]" value="{{ old('break_start.'.$newIndex, optional($readonly ? $correction?->breaks->get($newIndex)?->requested_break_start : null)->format('H:i')) }}"{{ $readonly ? 'readonly' : '' }}>
                        <p class="register-form__error-message">
                            @error("break_start.$newIndex")
                            {{ $message }}
                            @enderror
                        </p>

                        <span>～</span>

                        <input class="attendance-detail__input" type="time" name="break_end[]" value="{{ old('break_end.'.$newIndex, optional($readonly ? $correction?->breaks->get($newIndex)?->requested_break_start : null)->format('H:i')) }}" {{ $readonly ? 'readonly' : '' }}>
                        <p class="register-form__error-message">
                            @error("break_end.$newIndex")
                            {{ $message }}
                            @enderror
                        </p>
                    </div>
                </div>

                <div class="attendance-detail__row">
    <label class="attendance-detail__label" for="reason">備考</label>

    @if($readonly)
        <p class="attendance-detail__reason">
            {{ old('reason', $correction?->reason) }}
        </p>
    @else
        <textarea
            class="attendance-detail__textarea"
            name="reason"
            id="reason"
        >{{ old('reason', $attendance->reason ?? '') }}</textarea>
    @endif

    <p class="register-form__error-message">
        @error('reason')
        {{ $message }}
        @enderror
    </p>
</div>
            </div>

                <div class="attendance-detail__button">
                    @if($readonly)

                    @if(!$correction->is_approved)
                    <p class="attendance-detail__pending-message">
                        ＊承認待ちのため修正はできません。
                    </p>
                    @endif

                @else

                    <button class="attendance-detail__btn" type="submit">修正</button>
                @endif
            </div>
        </form>
    </div>
</div>
@endsection