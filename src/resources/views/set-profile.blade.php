
@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
@endsection

@section('title', 'プロフィール設定')

@section('content')
<div class="register-form__content">
    <div class="register-form__heading">
      <h2>プロフィール設定</h2>
    </div>
    <form class="form" action="" method="post">
        @csrf

        <div class="form__group">
            <div class="form__group-title">ユーザー名</div>
            <div class="form__group-content">
                <div class="form__input--text">
                    <input type="text" name="username" value="oldname入れる">
                </div>
                <div class="form__error">
                  あっとエラー入れる
                </div>
            </div>
        </div>

        <div class="form__group">
            <div class="form__group-title">郵便番号</div>
            <div class="form__group-content">
                <div class="form__input--text">
                    <input type="text" name="postal" value="oldname入れる">
                </div>
                <div class="form__error">
                  あっとエラー入れる
                </div>
            </div>
        </div>

        <div class="form__group">
            <div class="form__group-title">住所
            </div>
            <div class="form__group-content">
                <div class="form__input--text">
                    <input type="text" name="address" value="oldname入れる">
                </div>
                <div class="form__error">
                  あっとエラー入れる
                </div>
            </div>
        </div>

        <div class="form__group">
            <div class="form__group-title">建物名
            </div>
            <div class="form__group-content">
                <div class="form__input--text">
                    <input type="text" name="building" value="oldname入れる">
                </div>
                <div class="form__error">
                  あっとエラー入れる
                </div>
            </div>
        </div>

        <div class="form__button">
            <button class="form__button-submit" type="submit">更新する</button>
        </div>
    </form>

</div>

@endsection