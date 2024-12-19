@extends('layouts.app')

<!--css読み込み-->
@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance-register.css') }}">
@endsection

<!--本体-->
@section('content')

@include('components.header')
<div class="register__content">
    <div class="register__content--inner">
        <div class="work__status">勤務外</div>
        <div class="date">2023年6月1日(木)</div>
        <div class="time">08:00</div>

        <div class="btn__inner">
            <button class="register__btn">出勤</button>
        </div>
    </div>
</div>
