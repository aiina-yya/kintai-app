@extends('user.layouts.app')

@section('title', '申請一覧')

@section('css')
<link rel="stylesheet" href="{{ asset('css/correction_request_list.css') }}">
@endsection

@section('content')
<div class="request-list">
    <div class="request-list__container">
        <h1 class="request-list__title">申請一覧</h1>
        <div class="request-list__tabs">
            <a class="request-list__tab {{ $status == 'pending' ? 'is-active' : '' }}" href="{{ route('correction.list' , ['status' => 'pending']) }}">承認待ち</a>
            <a class="request-list__tab {{ $status == 'approved' ? 'is-active' : '' }}" href="{{ route('correction.list', ['status' => 'approved']) }}">承認済み</a>
        </div>

        <table class="request-list__table">
            <thead class="request-list__table-head">
                <tr>
                    <th >状態</th>
                    <th >名前</th>
                    <th >対象日時</th>
                    <th >申請理由</th>
                    <th >申請日時</th>
                    <th >詳細</th>
                </tr>
            </thead>

            <tbody class="request-list__body">
                @foreach($requests as $request)
                <tr>
                    <td>{{ $request->is_approved ? '承認済み' : '承認待ち' }}</td>
                    <td>{{ $request->attendance->user->name }}</td>
                    <td>{{ $request->attendance->work_date }}</td>
                    <td>{{ $request->reason }}</td>
                    <td>{{ $request->created_at->format('Y/m/d') }}</td>
                    <td>
                        <a href="{{ route('attendance.detail', [
                        'attendance' => $request->attendance_id,
                        'correction' => $request->id,
                        'from' => 'request'
                        ]) }}">詳細</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection