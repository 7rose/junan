<?php

    $counter = new App\Helpers\Counter;
    $carbon = new Carbon\Carbon;
?>

@extends('../nav')

@section('container')
    @if(isset($records) && count($records))
    <table class="table table-hover">
        <caption>
            @if(isset($all))
            <div class="alert alert-info">
                {{ Session::has('export') ? Session::get('export')['branch'] : '' }}: {{ Session::has('date_range') ? Session::get('date_range')['text'] : '' }}财务记录:{{ $all['total_num'] }}, 总营收: ¥{{ $all['total'] }}
                <a href="/counter/finance/download/excel/branch" class="btn btn-success btn-sm">导出Excel</a>

                <div class="dropdown pull-right">
                    <button type="button" class="btn btn-sm dropdown-toggle" id="dropdownMenu1" data-toggle="dropdown">{{ Session::has('date_range') ? Session::get('date_range')['text'] : '选择' }}
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
                        <li role="presentation">
                            <a role="menuitem" tabindex="-1" href="/counter/finance/set/month">当月 - {{ $carbon->now()->month }}月份</a>
                        </li>
                        <li role="presentation">
                            <a role="menuitem" tabindex="-1" href="/counter/finance/set/year">当年 - {{ $carbon->now()->year }}年</a>
                        </li>

                    </ul>
                </div>

            </div>
            @endif
        </caption>
        <thead>
            <tr>
                <th>员工</th>
                <th>贡献</th>
                <th>财务记录</th>
                <th>实际盈收</th>
                <th>招生</th>
                <th>招生营收比例</th>
                <th>高级班</th>
                <th>高级班营收比例</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $record)
            <tr>
                <td><a href="/user/{{ $record->user_id }}" class="btn btn-block btn-info btn-xs">{{ $record->user_id_text }}</a></td>
                <td>{{ $counter->percent($counter->fllow($record)['all'][2], $all['total']).'%' }}</td>
                <td>{{ $counter->fllow($record)['total'] }}</td>
                <td>{{ '¥'.$counter->fllow($record)['all'][2] }}</td>
                <td>{{ $counter->fllow($record)['recruit'][0].'人次 - ¥'.$counter->fllow($record)['recruit'][2] }}</td>
                <td>{{ $counter->percent($counter->fllow($record)['recruit'][2], $counter->fllow($record)['all'][2]).'%' }}</td>
                <td>{{ $counter->fllow($record)['change_class'][0].'人次 - ¥'.$counter->fllow($record)['change_class'][2] }}</td>
                <td>{{ $counter->percent($counter->fllow($record)['change_class'][2], $counter->fllow($record)['all'][2]).'%' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
        <div class="alert alert-warning">无记录</div>
    @endif
@endsection