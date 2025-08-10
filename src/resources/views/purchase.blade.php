@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('title', '購入画面')

@section('header')
<form class="search-form" action="{{ route('items.index') }}" method="get">
    <span class="search-form__item">
        <input class="search-form__item-input" type="text"
        name="keyword"
        value="{{ old('keyword', $filters['keyword'] ?? ''
        ) }}"
        placeholder="何をお探しですか？" />
    </span>
</form>
<nav>
  <ul class="header-nav">
    @if (Auth::check())
    <li class="header-nav__item">
      <form action="/logout" method="post">
        @csrf
        <button class="header-nav__logout-button" type="submit">ログアウト</button>
      </form>
    </li>
    @else
    <li class="header-nav__item">
      <a class="header-nav__link" href="/login">ログイン</a
    </li>
    @endif
    <li class="header-nav__item">
      <a class="header-nav__link" href="/mypage">マイページ</a>
    </li>
    <li class="header-nav__item">
      <a class="header-nav__sell-link" href="/sell">出品</a>
    </li>
  </ul>
</nav>
@endsection

@section('content')
<div class="purchase-content">
    <form class="purchase-content__form" action="" method="post">
        <div class="purchase-detail">
            <div class="item">
                <div class="item-card">
                    <div class="item-card__img-wrapper">
                        <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->title }}">
                    </div>
                </div>
                    <div>
                    <h2>{{ $item->title }}</h2>
                    <p class="item-price">¥ {{ number_format($item->price) }}</p>
                </div>
            </div>
            <div>
                <h3 class="payment-method">支払い方法</h3>
                <select class="payment-method__select" name="payment_method" id="">
                    @foreach ($paymentlabels as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
                <div class="delivery-address">
                    <h3>配送先</h3>
                    <a class="change-address" href="">変更する</a>
                </div>
                <p class="postal-code">{{ $user->postal_code}}</p>
                <p class="address">{{ $user->address }}</p>
            </div>
        </div>

        <div class="confirmation">
            <table>
                <tr>
                    <td>商品代金</td>
                    <td>¥ {{ $item->price }}</td>
                </tr>
                <tr>
                    <td>支払い方法</td>
                    <td>選択した支払い方法表示</td>
                </tr>
            </table>
            <div class="form__button-submit">購入する</div>
        </div>
    </form>
</div>
@endsection