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

        <form class="correction-form" action="/attendance/list" method="post">
            @csrf
            <input type="hidden" name="attendance_id" value="{{$attendance->id}}">

            <!--承認待ちの場合-->
            @if ($attendance->attendanceRequest && $attendance->attendanceRequest->status_id === 1)
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
                            <p>{{ $breakTime[0]['break_start']}}<span class="time_mark">~</span>{{ $breakTime[0]['break_end'] }}</p>
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
                <p class="text__remark">*承認待ちのため修正はできません</p>
            
            <!--修正前-->
            @else
                <table class="table">
                    <tr class="table__row">
                        <th class="table__label">名前</th>
                        <td class="table__data">{{ $attendance->user->name }}</td>
                    </tr>

                    <tr class="table__row">
                        <th class="table__label">日付</th>
                        <td class="table__data">
                            <input class="table__input input__year" type="text" name="year" value="{{ old('date', $year) }}">
                            <input class="table__input month" type="text" name="monthDay" value="{{ old('date', $monthDay) }}">
                        </td>
                    </tr>

                    <tr class="table__row">
                        <th class="table__label">出勤・退勤</th>
                        <td class="table__data">
                            <input class="table__input clock_in" type="text" name="clock_in_time" value="{{ old('clock_in_time', $clockInOut['clock_in']) }}">
                            <span class="time_mark">~</span>
                            <input class="table__input clock_out" type="text" name="clock_out_time" value="{{ old('clock_out_time', $clockInOut['clock_out']) }}">
                        </td>
                    </tr>

                    <tr class="table__row">
                        <th class="table__label">休憩</th>
                        <td class="table__data">    
                            <input class="table__input break_start" type="text" name="break_start[0]" value="{{ old('break_start.0', $breakTime[0]['break_start'])}}">
                            <span class="time_mark">~</span>
                            <input class="table__input break_end" type="text" name="break_end[0]" value="{{ old('break_end.0', $breakTime[0]['break_end']) }}">
                        </td>
                    </tr>

                    <tr class="table__row">
                        <th class="table__label">休憩2</th>
                        <td class="table__data">
                            @if (isset($breakTime[1]))
                                <input class="table__input break_start" type="text" name="break_start[1]" value="{{ old('break_start.1', $breakTime[1]['break_start']) }}">
                                <span class="time_mark">~</span>
                                <input class="table__input break_end" type="text" name="break_end[1]" value="{{ old('break_end.1', $breakTime[1]['break_end']) }}">
                            @endif
                        </td>
                    </tr>

                    <tr class="table__row">
                        <th class="table__label">備考</th>
                        <td class="table__data">
                            @if ($attendance->attendanceRequest && $attendance->attendanceRequest->status_id === 1 &&
                            ($attendance->attendanceRequest->reason || (!$attendance->attendanceRequest->reason && !$attendance->attendanceRequest->pending_reason)))
                                <textarea class="remarks-section" rows="4" cols="37" name="reason" id="">{{ $attendance->attendanceRequest->reason ?? ''}}</textarea>
                            @endif
                        </td>
                    </tr>
                </table>
                <div class="form__error">
                    <p class="error-messages">
                        @error('reason')
                        {{ $message }}
                        @enderror
                    </p>
                </div>
                <div class="form__error">
                    @if ($errors->has('validation_error'))
                        <p class="error-messages">
                            {{ $errors->first('validation_error')}}
                        </p>
                    @endif
                </div>
                <div class="btn__inner">
                    <button class="correction__btn small-btn">修正</button>
                </div>
            @endif
        </form>
    </div>
</div>