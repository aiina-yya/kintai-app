@extends('user.layouts.app')

@section('title','勤怠詳細画面')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance-detail.css') }}">
@endsection

@section('content')
<div class="attendance-detail">
    <div class="attendance-detail__container">

        <h1 class="attendance-detail__title">勤怠詳細</h1>
        <div class="attendance-detail__content">
            <form action="{{ route('attendance.correction', $attendance) }}" method="post">
            @csrf
            <div class="attendance-detail__row">
                <label class="attendance-detail__label" for="name">名前</label>
                <input class="attendance-detail__input"  type="text" id="name" value="{{ $attendance->user->name }}" readonly >

                <label class="attendance-detail__label" for="work_date">日付</label>
                <input class="attendance-detail__input"  type="text" id="work_date" value="{{ $attendance->work_date->format('Y年n月j日') }}" readonly>
            </div>
        </form>
    </div>

</div>
@endsection