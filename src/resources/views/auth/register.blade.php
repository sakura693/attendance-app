@extends('layouts.app')

<!--css読み込み-->
@section('css')
<link rel="stylesheet" href="{{ asset('css/authentication.css') }}">
@endsection

<!--本体-->
@section('content')

@include('components.header')
<div class="form__container">
    <form class="authenticate__form" action="/register" method="post" class="authenticate ">
        @csrf
        <h1 class="page__title">会員登録</h1>
        <div class="form__content">
            <label class="entry__name" for="name">名前</label>
            <input class="input" name="name" type="input" value="{{ old('name') }}">
            <!--後で書く-->
            <div class="form__error"></div>

            <label class="entry__name" for="mail">メールアドレス</label>
            <input class="input" name="email" type="email" value="{{ old('email') }}">
            <!--後で書く-->
            <div class="form__error"></div>

            <label class="entry__name" for="password">パスワード</label>
            <input class="input" name="password" type="password">
            <!--後で書く-->
            <div class="form__error"></div>

            <label class="entry__name" for="password">パスワード確認</label>
            <input class="input" name="password_confirm" type="password">

            <button class="btn btn--big">登録する</button>
            <a class="link" href="/register">ログインはこちら</a>
        </div>
    </form>
</div>
@endsection