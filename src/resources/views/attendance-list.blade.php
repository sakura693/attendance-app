@extends('layouts.app')

<!--css読み込み-->
@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance-list.css') }}">
<!--font awesomをインポート-->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
@endsection

<!--本体-->
@section('content')

@include('components.header')
<div class="content">
    <div class="content__inner">
        <h1 class="content__title">勤怠一覧</h1>

        <!--カレンダー部分-->
        <div class="calendar">
            <!--矢印アイコン-->
            <i class="fa-solid fa-arrow-left arrow-left__icon" style="color: #a8a7a3;"></i>
            <div class="text__container">
                <p class="calendar__text">前月</p>
                
                <!--日付部分-->
                <div>（仮）日付</div>

                <p class="calendar__text">翌月</p>
            </div>
            <i class="fa-solid fa-arrow-right arrow-right__icon" style="color: #a8a7a3;"></i>
        </div>

        <table class="table">
            <tr class="table__row">
                <th class="table__label">日付</th>
                <th class="table__label">出勤</th>
                <th class="table__label">退勤</th>
                <th class="table__label">休憩</th>
                <th class="table__label">合計</th>
                <th class="table__label">詳細</th>
            </tr>

            <!--実際の値-->
            <tr class="table__row">
                <td class="table__data">あ</td>
                <td class="table__data">い</td>
                <td class="table__data">う</td>
                <td class="table__data">え</td>
                <td class="table__data">お</td>
                <td class="table__data">か</td>
            </tr>
        </table>
    </div>
</div>