@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
@endsection

@section('title', '新規登録')

@section('content')
<div class="register-form__content">
    <div class="register-form__heading">
      <h2>会員登録</h2>
    </div>
    <form class="form" action="/register" method="post">
        @csrf

        <div class="form__group">
            <div class="form__group-title">ユーザー名</div>
            <div class="form__group-content">
                <div class="form__input--text">
                    <input type="text" name="name" value="{{ old('name')}}">
                </div>
                <div class="form__error">
                @error('name')
                    <span class="error-message">{{ $message }}</span>
                @enderror
                </div>
            </div>
        </div>

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

        <div class="form__group">
            <div class="form__group-title">確認用パスワード</div>
            <div class="form__group-content">
                <div class="form__input--text">
                    <input type="password" name="password_confirmation" value="{{ old('password_confirmation') }}">
                </div>
                <div class="form__error">
                @error('password')
                    <span class="error-message">{{ $message }}</span>
                @enderror
                </div>
            </div>
        </div>

        <div class="form__button">
            <button class="form__button-submit" type="submit">登録する</button>
        </div>
    </form>

    <div class="login__link">
      <a class="login__button-submit" href="/login">ログインはこちら</a>
    </div>
</div>
@endsection