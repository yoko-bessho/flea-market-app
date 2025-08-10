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
        <div class="reaction">
            <span class="like-count">
                <form action="{{ route('item.like', $item->id)}}" method="post">
                    @csrf
                    <button class="button-like {{ $item->likes->contains(auth()->user()) ? 'button-unlike' : ''}}" type="submit">
                      {{ $item->likes->contains(auth()->user()) ? '★' : '★' }}
                    </button>
                </form>
                <p class="reaction-count--like">{{ $item->likes->count() }}</p>
            </span>

            <span class="comment-count">
                <div class="comment-icon"></div>
                <p class="reaction-count--comment">{{ $item->commentingUsers->count() }}</p>
            </span>
        </div>
        <div class="form__button-submit purchase-form__button-submit">
            <a class="goto-purchase" href="/purchase/{{ $item->id }}">購入手続きへ</a>
        </div>
        <h2 class="title">商品説明</h2>
        <div class="item-description">
            {{ $item->description }}
        </div>
        <h2 class="title">商品の情報</h2>
        <table class="item-infomation">
            <tr>
                <th class="title-header">カテゴリー</th>
                <td class="item-category">
                    @foreach ($item->categories as $category)
                        <span>{{ $category->name }}</span>
                    @endforeach
                </td>
            </tr>
            <tr>
                <th class="item-condition">商品の状態</th>
                <td>{{ $conditionlabels }}</td>
            </tr>
        </table>
        <div class="cmment-area">
            <h3 class="title">コメント（{{ $item->commentingUsers->count() }}）</h3>
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
                    <h4 class="commenter-name">{{ $user->name }}</h4>
                </div>
                <div class="commenter-text">
                  {{ $user->pivot->text}}
                </div>
            </div>
            @endforeach
            <form class="comment-form" action="{{ route('item.comment', $item->id) }}" method="post">
                @csrf
                <h4 class="comment-header">商品へのコメント</h4>
                <textarea name="text" id="field"></textarea>
                <div id="charCount"></div>
                <script>
                    const textarea = document.getElementById('field');
                    textarea.addEventListener('input', function() {
                      const thisVal = this.value;
                      const charCount = thisVal.length;
                      document.getElementById('charCount').innerText = charCount+'/最大255文字';
                    });
                </script>
                <button class="form__button-submit" type="submit">コメントを送信する</button>
            </form>
        </div>
    </div>
</div>
@endsection
