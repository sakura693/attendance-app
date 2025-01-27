@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance-list.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
@endsection

@section('content')
@include('components.header')
<div class="content">
    <div class="content__inner">
        <h1 class="content__title">勤怠一覧</h1>
        <div class="calendar">
            <form class="calendar__conteinar" class="get__month" method="get" action="/attendance/list">
                <div class="calendar__inner">
                    <i class="fa-solid fa-arrow-left arrow-left__icon" style="color: #a8a7a3;"></i>
                    <button class="calendar__text" type='submit' name="action" value="prev">前月</button>
                </div>
                <div class="date__container">
                    <i class="fa-regular fa-calendar-days fa-lg" style="color: #a8a7a3;"></i>
                    <div class="date__text">{{ $currentMonth}}</div>
                </div>
                <div class="calendar__inner">
                    <button class="calendar__text" type='submit' name="action" value="next">翌月</button>
                    <i class="fa-solid fa-arrow-right arrow-right__icon" style="color: #a8a7a3;"></i>
                </div>
            </form>
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
            @foreach ($attendances as $attendance)
                <tr class="table__row">
                    <td class="table__data">{{ $attendance->formatted_date }}</td>
                    <td class="table__data">{{ $attendance->formatted_clock_in_time }}</td>
                    <td class="table__data">
                        @if ($attendance->clock_out_time)
                            {{ $attendance->formatted_clock_out_time }}
                        @endif
                        </td>
                    <td class="table__data">{{ $attendance->break_hours }}</td>
                    <td class="table__data">{{ $attendance->total_hours }}</td>
                    <td class="table__data">
                        <a class="attendance_detail" href="/attendance/{{ $attendance->id }}">詳細</a>
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
</div>
@endsection