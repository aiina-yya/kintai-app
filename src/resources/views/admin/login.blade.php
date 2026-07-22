@extends('admin.layouts.app')

@section('title','管理者ログイン')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/login.css') }}">
@endsection

@section('content')
<div class="login-form">
    <h2 class="login-form__heading content__heading">管理者ログイン</h2>
    <div class="login-form__inner">
        <form class="login-form__form"  action="{{ route('admin.login') }}" method="post" novalidate>
            @csrf
            <div class="login-form__group">
                <label class="login-form__label"  for="email">メールアドレス</label>
                <input class="login-form__input"  type="email" name="email" id="email">
                <p class="register-form__error-message">
                @error('email')
                {{ $message }}
                @enderror
                </p>
            </div>

            <div class="login-form__group">
                <label class="login-form__label"  for="password">パスワード</label>
                <input class="login-form__input"  type="password" name="password" id="password">
                <p class="register-form__error-message">
                @error('password')
                {{ $message }}
                @enderror
                </p>
            </div>

            <input class="login-form__btn btn"  type="submit" value="管理者ログインする">
        </form>
    </div>
</div>
@endsection