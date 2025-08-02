@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('title', 'フリマアプリTOP')


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
<div class="item-container">
    <div class="tab-header">
        <a class="tab-link {{ $tab === 'recommended' ? 'active' : '' }}" href="{{ route('items.index', array_merge(request()->query(), ['tab' => 'recommended'])) }}">おすすめ</a>
        <a class="tab-link {{ $tab === 'mylist' ? 'active' : '' }}" href="{{ route('items.index', array_merge(request()->query(), ['tab' => 'mylist'])) }}">マイリスト</a>
    </div>
       

    <div class="tab-content">
        @if ($tab === 'recommended')
            <div class="item-list">
                @forelse ($recommendedItems as $item)
                <div class="item-card">
                    <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->title }}">
                    <h4>{{ $item->title }}</h4>
                        @if ($item->is_sold)
                        <p class="soldout">sold out</p>
                        @endif
                </div>
                @empty
                <p>出品されている商品はありません。</p>
                @endforelse
            </div>
        @elseif ($tab === 'mylist')
            <div class="item-list">
                @forelse ($mylistItems as $item)
                <div class="item-card">
                    <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->title }}">
                    <h4>{{ $item->title }}</h4>
                        @if ($item->is_sold)
                        <p class="soldout">sold out</p>
                        @endif
                </div>
                @empty
                <p></p>
                @endforelse
            </div>
        @endif
    </div>
</div>



@endsection