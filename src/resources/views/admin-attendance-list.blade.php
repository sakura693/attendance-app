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
        <h1 class="content__title">{{ \Carbon\Carbon::createFromFormat('Y/m/d', $currentDay)->format('Y年n月j日') }}の勤怠 </h1>

        <!--カレンダー部分-->
        <div class="calendar">
            <form class="calendar__conteinar" class="get__month" method="get" action="/admin/attendance/list">
                @csrf
                <div class="calendar__inner">
                    <!--矢印アイコン-->
                    <i class="fa-solid fa-arrow-left arrow-left__icon" style="color: #a8a7a3;"></i>
                    <button class="calendar__text" type='submit' name="action" value="prev_day">前日</button>
                </div>
                
                <!--日付部分-->
                <div class="date__container">
                    <i class="fa-regular fa-calendar-days fa-lg" style="color: #a8a7a3;"></i>
                    <div class="date__text">{{ $currentDay }}</div>
                </div>

                <div class="calendar__inner">
                    <button class="calendar__text" type='submit' name="action" value="next_day">翌日</button>
                    <i class="fa-solid fa-arrow-right arrow-right__icon" style="color: #a8a7a3;"></i>
                </div>
            </form>
        </div>

        <table class="table">
            <tr class="table__row">
                <th class="table__label">名前</th>
                <th class="table__label">出勤</th>
                <th class="table__label">退勤</th>
                <th class="table__label">休憩</th>
                <th class="table__label">合計</th>
                <th class="table__label">詳細</th>
            </tr>

            <!--実際の値-->
            @foreach($attendances as $attendance)
                <tr class="table__row">
                    <td class="table__data">{{ $attendance->user->name }}</td>
                    <td class="table__data">{{ $attendance->formatted_clock_in_time}}</td>
                    <td class="table__data">{{ $attendance->formatted_clock_out_time}}</td>
                    <td class="table__data">{{ $attendance->break_hours}}</td>
                    <td class="table__data">{{ $attendance->total_hours}}</td>
                    <td class="table__data">
                        <a class="attendance_detail" href="">詳細</a>
                    </td>
                </tr>
            @endforeach
        </table> 
    </div>
</div>