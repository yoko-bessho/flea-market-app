@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
<link rel="stylesheet" href="{{ asset('css/item-create.css') }}">
@endsection

@section('title', '商品出品')

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
<div class="sell-container">
    <h2 class="sell-title">商品の出品</h2>

    <form action="" method="POST" enctype="multipart/form-data" class="sell-form">
        @csrf

        <div class="form-group">
            <label for="image">商品画像</label>
            <div class="image-upload-box">
                <input type="file" name="image" id="image" class="image-input">
                <p class="image-placeholder">画像を選択する</p>
            </div>
            @error('image')
                <p class="error">{{ $message }}</p>
            @enderror
        </div>

        <h3 class="section-title">商品の詳細</h3>

        <div class="form-group">
            <label>カテゴリー</label>
            <div class="category-list">
                @foreach ($categories as $category)
                    <label class="category-tag">
                        <input type="checkbox" name="category_id[]" value="{{ old($category->id) == $category->id ? 'checked' : '' }}">
                        {{ $category->name }}
                    </label>
                @endforeach
            </div>
            @error('category')
                <p class="error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="condition">商品の状態</label>
            <select name="condition" id="condition">
                <option value="">選択してください</option>
                @foreach($conditionlabels as $value => $label)
                <option value="$label">{{ $label }}</option>
                @endforeach
            </select>
            @error('condition')
                <p class="error">{{ $message }}</p>
            @enderror
        </div>

        <h3 class="section-title">商品名と説明</h3>

        <div class="form-group">
            <label for="title">商品名</label>
            <input type="text" name="title" id="title" value="{{ old('title') }}">
            @error('title')
                <p class="error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="brand">ブランド名</label>
            <input type="text" name="brand" id="brand" value="{{ old('brand') }}">
        </div>

        <div class="form-group">
            <label for="description">商品の説明</label>
            <textarea name="description" id="description" rows="5">{{ old('description') }}</textarea>
        </div>

        <div class="form-group">
            <label for="price">販売価格</label>
            <div class="price-box">
                <span>¥</span>
                <input type="number" name="price" id="price" value="{{ old('price') }}">
            </div>
            @error('price')
                <p class="error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-submit">
            <button type="submit" class="submit-button">出品する</button>
        </div>
    </form>
</div>
@endsection
