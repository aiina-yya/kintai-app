@extends('user.layouts.app')

@section('title', '勤怠レポート')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance-report.css') }}">
@endsection


@section('content')
<div class="attendance-report">
    <div class="attendance-report__container">
        <h1 class="attendance-report__title">マイ勤怠レポート</h1>
        <p class="attendance-report__description">過去6か月の勤怠データから集計しています。</p>

        <div class="attendance-report__section">
            <h2 class="attendance-report__heading">基本サマリー</h2>
            <div class="attendance-report__summary">
                <div class="attendance-report__summary-card">
                    <p class="attendance-report__card-label">
                        総労働時間
                    </p>
                    <p class="attendance-report__card-value">750h 0m</p>
                </div>

                <div class="attendance-report__summary-card">
                    <p class="attendance-report__card-label">総残業時間</p>
                    <p class="attendance-report__card-value">
                        10h 0m
                    </p>
                </div>

                <div class="attendance-report__summary-card">
                    <p class="attendance-report__card-label">
                        平均労働時間 / 日
                    </p>
                    <p class="attendance-report__card-value">
                        8h 5m
                    </p>
                </div>
            </div>
        </div>

        <div class="attendance-report__section">
            <h2 class="attendance-report__heading">
                月次推移（過去6ヶ月）
            </h2>
            <table class="attendance-report__table">
                <thead>
                    <tr>
                        <th>月</th>
                        <th>労働時間</th>
                        <th>残業時間</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        <div class="attendance-report__section">
            <h2 class="attendance-report__heading">
                今月の異常検知
            </h2>
            <p class="attendance-report__detect-description">
                基準：始業 09:00 / 終業 18:00 / 長時間労働は1日10時間超
            </p>
            <div class="attendance-report__detect">
                <div class="attendance-report__detect-card">
                    <p class="attendance-report__detect-label">遅刻回数</p>
                    <p class="attendance-report__detect-value">２回</p>
                </div>
                <div class="attendance-report__detect-card">
                    <p class="attendance-report__detect-label">早退回数</p>
                    <p class="attendance-report__detect-value">１回</p>
                </div>
                <div class="attendance-report__detect-card">
                    <p class="attendance-report__detect-label">長時間労働日数</p>
                    <p class="attendance-report__detect-value">２日</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection