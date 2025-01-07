@extends('layouts.app')

<!--css読み込み-->
@section('css')
<link rel="stylesheet" href="{{ asset('css/correction-request.css') }}">
@endsection

<!--本体-->
@section('content')

@include('components.header')
<div class="content">
    <div class="content__inner">
        <h1 class="content__title">申請一覧</h1>

        <div class="status__container">
            <a class="status__text pending-approval" href="/stamp_correction_request/list/?tab=pending">承認待ち</a>
            <a class="status__text approved" href="/stamp_correction_request/list/?tab=approved">承認済み</a>
        </div>

        <table class="table">
            <tr class="table__row">
                <th class="table__label">状態</th>
                <th class="table__label">名前</th>
                <th class="table__label">対象日時</th>
                <th class="table__label">申請理由</th>
                <th class="table__label">申請日時</th>
                <th class="table__label">詳細</th>
            </tr>

            <!--実際の値-->
            @foreach($corrections as $correction)
                <tr class="table__row">
                    <td class="table__data">{{ $correction->status->status }}</td>
                    <td class="table__data">{{ $correction->attendance->user->name}}</td>
                    <td class="table__data">{{ \Carbon\Carbon::parse($correction->attendance->date)->format('Y/m/d') }}</td>
                    <td class="table__data">{{ $correction->display_reason }}</td>
                    <td class="table__data">{{ \Carbon\Carbon::parse($correction->created_at)->format('Y/m/d') }}</td>
                    <td class="table__data">
                        <a class="attendance_detail" href="/attendance/{{ $correction->attendance->id }}">詳細</a>
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
</div>