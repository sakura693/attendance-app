@extends('layouts.app')

<!--css読み込み-->
@section('css')
<link rel="stylesheet" href="{{ asset('css/authentication.css') }}">
@endsection

<!--本体-->
@section('content')

@include('components.header')
<div class="form__container">
    <form class="authenticate__form" action="/admin/login" method="post" class="authenticate ">
        @csrf
        <h1 class="page__title">管理者ログイン</h1>
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

            <button class="btn btn--big">管理者ログインする</button>
        </div>
    </form>
</div>

@endsection