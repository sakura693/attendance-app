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

        <form class="approval-form" action="" method="post">
            @csrf
            <table class="table">
                <tr class="table__row">
                    <th class="table__label">名前</th>
                    <td class="table__data">{{ $attendance->user->name }}</td>
                </tr>

                <tr class="table__row">
                    <th class="table__label">日付</th>
                    <td class="table__data text__data">
                        <p class="text__year">{{$year}}</p>
                        <p>{{$monthDay}}</p>
                    </td>
                </tr>

                <tr class="table__row">
                    <th class="table__label">出勤・退勤</th>
                    <td class="table__data">
                        <p>{{ $clockInOut['clock_in'] }}<span class="time_mark">~</span>{{ $clockInOut['clock_out'] }}</p>
                    </td>
                </tr>

                <tr class="table__row">
                    <th class="table__label">休憩</th>
                    <td class="table__data">
                        @if (isset($breakTime[0]) && $breakTime[0]['break_start'] && $breakTime[0]['break_end'])
                            <p>{{ $breakTime[0]['break_start']}}<span class="time_mark">~</span>{{ $breakTime[0]['break_end'] }}</p>
                        @endif
                    </td>
                </tr>

                <tr class="table__row">
                    <th class="table__label">休憩2</th>
                    <td class="table__data">
                        @if ($attendance->breakRecords->count() > 1)
                            <p>{{ $breakTime[1]['break_start']}}<span class="time_mark">~</span>{{ $breakTime[1]['break_end'] }}</p>
                        @endif
                    </td>
                </tr>

                <tr class="table__row">
                    <th class="table__label">備考</th>
                    <td class="table__data">
                        @if ($attendance->attendanceRequest && $attendance->attendanceRequest->status_id === 1)
                            <p>{{ $attendance->attendanceRequest->pending_reason ?? ''}}</p>
                            @endif
                    </td>
                </tr>
            </table>

            <div class="btn__inner">
                @if ($attendance->attendanceRequest && $attendance->attendanceRequest->status_id === 1)
                    <button class="approval__btn small-btn">承認</button>
                @elseif ($attendance->attendanceRequest && $attendance->attendanceRequest->status_id === 2)
                    <button class="approved__btn small-btn" disabled>承認済み</button>
                @endif
            </div>
        </form>
    </div>
</div>

                            