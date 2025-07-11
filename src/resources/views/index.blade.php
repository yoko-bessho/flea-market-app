@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('title', 'COACHTECH')


@section('header')
<nav>
  <ul class="header-nav">
    <li class="header-nav__item">
        <a class="header-nav__link" href="/login">ログイン</a>
    </li>
    @if (Auth::check())
    <li class="header-nav__item">
      <form action="/logout" method="post">
        @csrf
        <button class="header-nav__button" type="submit">ログアウト</button>
      </form>
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
<H2>一覧画面 表示 index だよー</H2>
@endsection