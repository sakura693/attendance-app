@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/authentication.css') }}">
@endsection

@section('content')

@include('components.header')
<div class="form__container">
    <form class="authenticate__form" action="/login" method="post" class="authenticate ">
        @csrf
        <h1 class="page__title">ログイン</h1>
        <div class="form__content">
            <label class="entry__name" for="mail">メールアドレス</label>
            <input class="input" name="email" type="email" value="{{ old('email') }}">
            <div class="form__error">
                <p class="error-messages">
                    @error('email')
                    {{ $message }}
                    @enderror
                </p>
            </div>

            <label class="entry__name" for="password">パスワード</label>
            <input class="input" name="password" type="password">
            <div class="form__error">
                <p class="error-messages">
                    @error('password')
                    {{ $message }}
                    @enderror
                </p>
            </div>

            <button class="btn btn--big">ログインする</button>
            <a class="link" href="/register">会員登録はこちら</a>
        </div>
    </form>
</div>

@endsection