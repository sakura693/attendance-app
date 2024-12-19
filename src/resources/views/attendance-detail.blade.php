@extends('layouts.app')

<!--css読み込み-->
@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance-detail.css') }}">
@endsection

<!--本体-->
@section('content')

@include('components.header')
<div class="content">
    <div class="content__inner">
        <h1 class="content__title">勤怠詳細</h1>

        <table class="table">
            <tr class="table__row">
                <th class="table__label">名前</th>
                <td class="table__data">ああああ</td>
            </tr>

            <tr class="table__row">
                <th class="table__label">日付</th>
                <td class="table__data">
                    <input class="table__input input__year" type="text">
                    <input class="table__input month" type="text">
                </td>
            </tr>

            <tr class="table__row">
                <th class="table__label">出勤・退勤</th>
                <td class="table__data">
                    <input class="table__input clock_in" type="text">
                    <span class="time_mark">~</span>
                    <input class="table__input clock_out" type="text">
                </td>
            </tr>

            <tr class="table__row">
                <th class="table__label">休憩</th>
                <td class="table__data">
                    <input class="table__input break_start" type="text">
                    <span class="time_mark">~</span>
                    <input class="table__input break_end" type="text">
                </td>
            </tr>

            <tr class="table__row">
                <th class="table__label">休憩2</th>
                <td class="table__data">
                    <input class="table__input break_start" type="text">
                    <span class="time_mark">~</span>
                    <input class="table__input break_end" type="text">
                </td>
            </tr>

            <tr class="table__row">
                <th class="table__label">備考</th>
                <td class="table__data">
                    <textarea class="remarks-section" rows="4" cols="37" name="" id=""></textarea>
                </td>
            </tr>
        </table>

        <div class="btn__inner">
            <button class="correction__btn small-btn">修正</button>
        </div>
    </div>
</div>