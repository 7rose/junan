<?php

    $counter = new App\Helpers\Counter;
    $carbon = new Carbon\Carbon;
?>

@extends('../nav')

@section('container')
    @if(isset($records))
    <table class="table table-hover">
        <caption>
            @if(isset($records))
            <div class="alert alert-info">
                军安集团: {{ Session::has('date_range') ? Session::get('date_range')['text'] : '' }}各驾校业务情况
                @if(count($records))
                <a href="/counter/biz/download/excel" class="btn btn-success btn-sm">导出Excel</a>
                @endif
                <div class="dropdown pull-right">
                    <button type="button" class="btn btn-sm dropdown-toggle" id="dropdownMenu1" data-toggle="dropdown">{{ Session::has('date_range') ? Session::get('date_range')['text'] : '选择' }}
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
                        <li role="presentation">
                            <a role="menuitem" tabindex="-1" href="/counter/set/today">今天 - {{ $carbon->now()->day }}日</a>
                        </li>
                        <li role="presentation">
                            <a role="menuitem" tabindex="-1" href="/counter/set/week">本周</a>
                        </li>
                        <li role="presentation">
                            <a role="menuitem" tabindex="-1" href="/counter/set/month">本月 - {{ $carbon->now()->month }}月份</a>
                        </li>
                        <li role="presentation">
                            <a role="menuitem" tabindex="-1" href="/counter/set/year">本年度 - {{ $carbon->now()->year }}年</a>
                        </li>
                        <li role="presentation" class="divider"></li>
                        <li role="presentation">
                            <a role="menuitem" tabindex="-1" href="/counter/set/pre_month">上个月</a>
                        </li>

                    </ul>
                </div>

            </div>
            @endif
        </caption>
        @if(count($records))
        <thead>
            <tr>
                <th>驾校</th>
                <th>证照类型</th>
                <th>在学(现在)</th>
                <th>新招</th>
                <th>毕业</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $record)
            <tr>
                <td>{{ $record->branch_text }}</td>
                <td>{{ $record->licence_type_text }}</td>
                <td>{{ $counter->bizSum($record)['doing'] }}</td>
                <td>{{ $counter->bizSum($record)['new'] }}</td>
                <td>{{ $counter->bizSum($record)['finished'] }}</td>
            </tr>
            @endforeach
        </tbody>
        @endif
    </table>
    @endif
@endsection