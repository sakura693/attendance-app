@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/authentication.css') }}">
@endsection

@section('content')

@include('components.header')
<div class="form__container">
    <form class="authenticate__form" action="/register" method="post" class="authenticate ">
        @csrf
        <h1 class="page__title">会員登録</h1>
        <div class="form__content">
            <label class="entry__name" for="name">名前</label>
            <input class="input" name="name" type="input" value="{{ old('name') }}">
            <div class="form__error">
                <p class="error-messages">
                    @error('name')
                    {{ $message }}
                    @enderror
                </p>
            </div>

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

            <label class="entry__name" for="password_confirmation">パスワード確認</label>
            <input class="input" name="password_confirmation" type="password">

            <button class="btn btn--big">登録する</button>
            <a class="link" href="/login">ログインはこちら</a>
        </div>
    </form>
</div>
@endsection