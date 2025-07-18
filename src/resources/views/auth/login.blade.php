@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/register.css') }}">
@endsection

@section('title', 'ログイン画面')

@section('content')
<div class="login-form__content">
    <div class="login-form__heading">
      <h2>ログイン</h2>
    </div>
    <form class="form" action="/login" method="post">
        @csrf
        <div class="form__group">
            <div class="form__group-title">メールアドレス</div>
            <div class="form__group-content">
                <div class="form__input--text">
                    <input type="email" name="email" value="{{ old('email') }}">
                </div>
                <div class="form__error">
                @error('email')
                    <span class="error-message">{{ $message }}</span>
                @enderror
                </div>
            </div>
        </div>

        <div class="form__group">
            <div class="form__group-title">パスワード</div>
            <div class="form__group-content">
                <div class="form__input--text">
                    <input type="password" name="password" value="{{ old('password') }}">
                </div>
                <div class="form__error">
                @error('password')
                    <span class="error-message">{{ $message }}</span>
                @enderror
                </div>
            </div>
        </div>

        <div class="form__button">
            <button class="form__button-submit" type="submit">ログインする</button>
        </div>
    </form>

    <div class="register__link">
      <a class="register__button-submit" href="/register">会員登録はこちら</a>
    </div>

</div>
@endsection