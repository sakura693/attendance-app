@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/verify.css')  }}">
@endsection

@section('content')
@include('components.header')
<div class="mail_notice">
    <div class="mail_notice--header">
        <p class="notice_header--text">メール認証</p>
    </div>

    <div class="mail_notice--content">
        <p class="alert_resend--text">
            このページの閲覧にはメール認証が必要です。<br>
            ボタンをクリックして認証メールを受け取ってください。
        </p>
        <form class="mail_resend--form" method="POST" action="/email/verification-notification">
            @csrf
            <div class="btn_inner">
                <button type="submit" class="mail_resend--btn">認証メールを送信</button>
            </div>
        </form>
        @if (session('resent'))
        <div class="resend_inner">
            <p class="notice_resend--text" role="alert">
            認証メールを送信しました！</p>
        </div>
        @endif
    </div>
</div>
@endsection