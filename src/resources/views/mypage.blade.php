@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('title', 'マイページ')


@section('header')
<form action="" class="search-form" method="get">
    <span class="search-form__item">
        <input class="search-form__item-input" type="text" value="何をお探しですか？"/>
    </span>
</form>
<nav>
  <ul class="header-nav">
    @if (Auth::check())
    <li class="header-nav__item">
      <form action="/logout" method="post">
        @csrf
        <button class="header-nav__button" type="submit">ログアウト</button>
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
<div class="mypage-container">
    <div class="profile-container">
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
        <span class="profile-name">
        {{ $user->name }}
        </span>

        <div class="profile__update">
            <a class="profile__update-button" href="/setProfile">プロフィールを編集</a>
        </div>
    </div>

    <div class="tab-header">
        <a class="tab-link {{ $page === 'sell' ? 'active' : '' }}" href="{{ route('mypage', ['page' => 'sell']) }}">出品した商品</a>
        <a class="tab-link {{ $page === 'buy' ? 'active' : '' }}" href="{{ route('mypage', ['page' => 'buy']) }}">購入した商品</a>
    </div>

    <div class="tab-content">
        @if ($page === 'sell')
            <div class="item-list">
                @forelse ($sellItems as $item)
                <div class="item-card">
                    <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->title }}">
                    <h4>{{ $item->title }}</h4>
                </div>
                @empty
                <div class="item-card__placeholder">出品した商品はありません</div>
                @endforelse
            </div>

        @elseif ($page === 'buy')
            <div class="item-list">
                @forelse ($buyItems as $item)
                <div class="item-card">
                    <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}">
                    <h4>{{ $item->name }}</h4>
                </div>
                @empty
                <div class="item-card__placeholder">購入した商品はありません</div>
                @endforelse
            </div>
        @endif
    </div>
</div>
@endsection