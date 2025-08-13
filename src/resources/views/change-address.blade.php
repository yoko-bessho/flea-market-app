@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
@endsection

@section('title', '住所変更')

@section('content')
<div class="register-form__content">
    <div class="register-form__heading">
        <h2>住所の変更</h2>
    </div>
    <form class="form" action="{{ route('address.update', $item->id) }}" method="post">
        @csrf
        <div class="form__group">
            <div class="form__group-title">郵便番号</div>
            <div class="form__group-content">
                <div class="form__input--text">
                    <input
                    type="text"
                    name="postal_code"
                    value="{{ old('postal_code', $user->postal_code) }}">
                </div>
                <div class="form__error">
                @error('postal_code')
                    <span class="error-message">{{ $message }}</span>
                @enderror
                </div>
            </div>
        </div>


        <div class="form__group">
            <div class="form__group-title">住所
            </div>
            <div class="form__group-content">
                <div class="form__input--text">
                    <input
                    type="text"
                    name="address"
                    value="{{ old('address', $user->address) }}">
                </div>
                <div class="form__error">
                @error('address')
                    <span class="error-message">{{ $message }}</span>
                @enderror
                </div>
            </div>
        </div>


        <div class="form__group">
            <div class="form__group-title">建物名
            </div>
            <div class="form__group-content">
                <div class="form__input--text">
                    <input
                    type="text"
                    name="building"
                    value="{{ old('building', $user->building) }}">
                </div>
                <div class="form__error">
                @error('building')
                    <span class="error-message">{{ $message }}</span>
                @enderror
                </div>
            </div>
        </div>


        <div class="form__button">
            <button class="update form__button-submit" type="submit">更新する</button>
        </div>
    </form>
</div>

@endsection