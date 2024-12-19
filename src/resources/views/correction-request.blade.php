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
            <a class="status__text pending-approval" href="">承認待ち</a>
            <a class="status__text approved" href="">承認済み</a>
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
            <tr class="table__row">
                <td class="table__data">あ</td>
                <td class="table__data">い</td>
                <td class="table__data">う</td>
                <td class="table__data">あ</td>
                <td class="table__data">い</td>
                <td class="table__data">う</td>
            </tr>
        </table>
    </div>
</div>