@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
<link rel="stylesheet" href="{{ asset('css/item-detail.css') }}">
@endsection

@section('title', '商品詳細')

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
<div class="item-detail__content">
    <div class="item-card">
        <div class="item-card__img-wrapper">
            <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->title }}">
        </div>
        @if ($item->is_sold)
        <p class="soldout">sold out</p>
        @endif
    </div>

    <div class="item-detail">
        <h1 class="title">{{ $item->title }}</h1>
        <p class="brand">{{ $item->brand }}</p>
        <span class="price">¥ {{ number_format($item->price) }}</span><span>(税込)</span>
        <div class="reaction-count">
            <span class="like-count">
                星マークとカウンタ
            </span>
            <span class="comment-count">
                コメントマークとカウンタ
            </span>
        </div>
        <div class="form__button-submit purchase-form__button-submit">
            <a class="goto-purchase" href="#">購入手続きへ</a>
        </div>
        <h2 class="title">商品説明</h2>
        <div class="item-description">
            {{ $item->description }}
        </div>
        <h2 class="title">商品の情報</h2>
        <table class="item-infomation">
            <tr>
                <th>カテゴリー　　　　</th>
                <td class="item-category">
                    @foreach ($item->categories as $category)
                        {{ $category->name }}
                    @endforeach
                </td>
            </tr>
            <tr>
                <th class="item-condition">商品の状態　　　　</th>
                <td>{{ $conditionlabel }}</td>
            </tr>
        </table>
        <div class="cmment-area">
            <h3 class="title">コメント(数)</h3>
            @foreach ($commenters as $user)
            <div class="item-comment">
                <div class="commenter">
                    <div class="profile-image">
                        @if ($user->profile_image)
                        <img
                        src="{{ asset('storage/' . $user->profile_image) }}"
                        alt="プロフィール画像"
                        class="profile-image">
                        @else
                        <div class="profile-placeholder"></div>
                        @endif
                    </div>
                    <h4>{{ $user->name }}</h4>
                </div>
                <div class="comment-text">
                  {{ $user->pivot->text}}
                </div>
            </div>
            @endforeach
            <form class="comment-form" action="">
                <h4>商品へのコメント</h4>
                <textarea name="" id="" cols="32" rows="8"></textarea>
                <button class="form__button-submit">コメントを送信する</button>
            </form>
        </div>
    </div>
</div>
@endsection
