@extends('user.layouts.app')

@section('title','メール認証')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/verify-email.css') }}">
@endsection

@section('content')
<div class="verify-email">
    <p class="verify-email__message">登録していただいたメールアドレスに認証メールを送付しました。<br>
    メール認証を完了してください。
    </p>

    <a class="verify-email__link" href="http://localhost:8025">
        認証はこちらから
    </a>

    <form class="verify-email__form" method="post"  action="{{ route('verification.send') }}">
        @csrf

        <button class="verify-email__button" type="submit">
            認証メールを再送する
        </button>
    </form>
</div>
@endsection