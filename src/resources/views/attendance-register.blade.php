@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance-register.css') }}">
@endsection

@section('content')
@include('components.header')
<div class="register__content">
    <div class="register__content--inner">
        <div class="work__status">{{ $status }}</div>
        <div class="date">{{ $formattedData }}</div>
        <div class="time">{{ $formattedTime }}</div>

        <div class="btn__inner">
            @if ($status === '勤務外')
                <form action="attendance/start" method="post">
                    @csrf
                    <button class="register__btn">出勤</button>
                </form>
            @elseif ($status === '出勤中')
                <div class="form__container">
                    <form action="attendance/end" method="post">
                        @csrf
                        <button class="register__btn">退勤</button>
                    </form>
                    <form action="attendance/break/start" method="post">
                        @csrf
                        <button class="register__btn break__btn">休憩入</button>
                    </form>
                </div>
            @elseif ($status === '休憩中')
                <form action="attendance/break/end" method="post">
                    @csrf
                    <button class="register__btn break__btn">休憩戻</button>
                </form>
            @elseif ($status === '退勤済み')
                <p class="clock-out__message">お疲れ様でした。</p>
            @endif
        </div>
    </div>
</div>
@endsection
