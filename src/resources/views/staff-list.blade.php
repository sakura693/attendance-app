@extends('layouts.app')

<!--css読み込み-->
@section('css')
<link rel="stylesheet" href="{{ asset('css/staff-list.css') }}">
@endsection

<!--本体-->
@section('content')

@include('components.header')
<div class="content">
    <div class="content__inner">
        <h1 class="content__title">スタッフ一覧</h1>

        <table class="table">
            <tr class="table__row">
                <th class="table__label">名前</th>
                <th class="table__label">メールアドレス</th>
                <th class="table__label">月次勤怠</th>
            </tr>

            <!--実際の値-->
            @foreach($users as $user)
                <tr class="table__row">
                    <td class="table__data">{{ $user->name }}</td>
                    <td class="table__data">{{ $user->email }}</td>
                    <td class="table__data">
                        <a class="attendance_approval" href="/admin/attendance/staff/{{$user->id}}">詳細</a>
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
</div>