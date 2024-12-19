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
            <tr class="table__row">
                <td class="table__data">あ</td>
                <td class="table__data">い</td>
                <td class="table__data">う</td>
            </tr>
        </table>
    </div>
</div>