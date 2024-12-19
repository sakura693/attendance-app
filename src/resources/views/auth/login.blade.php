@extends('layouts.app')

<!--css読み込み-->
@section('css')
<link rel="stylesheet" href="{{ asset('css/authentication.css') }}">
@endsection

<!--本体-->
@section('content')

@include('components.header')
<div class="form__container">
    <form class="authenticate__form" action="/login" method="post" class="authenticate ">
        @csrf
        <h1 class="page__title">ログイン</h1>
        <div class="form__content">
            <label class="entry__name" for="mail">メールアドレス</label>
            <input class="input" name="email" type="email" value="{{ old('email') }}">
            <!--後で書く-->
            <div class="form__error"></div>

            <label class="entry__name" for="password">パスワード</label>
            <input class="input" name="password" type="password">
            <!--後で書く-->
            <div class="form__error"></div>

            <button class="btn btn--big">ログインする</button>
            <a class="link" href="/register">会員登録はこちら</a>
        </div>
    </form>
</div>

@endsection