@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/confirm.css') }}">
@endsection

@section('title', 'メール認証')

@section('content')
<div>
登録していただいたメールアドレスに認証メールを送付しました。
メール認証を完了してください。
</div>
<div class="authentication">
    <button class="authentication-button">認証はこちらから</button>
</div>
<div class="confirm-retry">
  <button>認証メールを再送する</button>
</div>
@endsection