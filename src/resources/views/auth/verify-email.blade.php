@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
@endsection

@section('title', 'メール認証')

@section('content')
<div class="authentication-guidance">
  <h3>登録していただいたメールアドレスに認証メールを送付しました。</h3>
  <h3>メール認証を完了してください。
  </h3>
  <form action="{{ route('verification.send') }}" method="post" >
    @csrf
    <div class="authentication">
        <button class="authentication-button" type="submit">認証はこちらから</button>
    </div>
    <div class="confirm-retry">
      <button>認証メールを再送する</button>
    </div>
    
</form>

</div>
@endsection