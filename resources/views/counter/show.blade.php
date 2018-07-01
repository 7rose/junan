<?php

    $counter = new App\Helpers\Counter;
    $auth = new App\Helpers\Auth;
    $carbon = new Carbon\Carbon;
    $mode = !Session::has('counter_finance_mode') || Session::get('counter_finance_mode') != 'real' ? 'normal' : 'real';
?>

@extends('../nav')

@section('container')
    @if(isset($records))
    <table class="table table-hover">
        <caption>
            @if(isset($all))
            <div class="alert alert-info">{{ $mode == 'normal' ? '对账 - ' : '贡献 - ' }}
                {{ Session::has('export') ? Session::get('export')['branch'] : '' }}: {{ Session::has('date_range') ? Session::get('date_range')['text'] : '' }}财务记录:{{ $all['total_num'] }}, 总营收: ¥{{ $all['total'] }}
                    @if($mode == 'normal')
                <a href="/filter/counter_finance_mode/real" class="btn btn-sm btn-info">切换为: 贡献模式</a>
                    @elseif($mode == 'real')
                <a href="/filter/counter_finance_mode/normal" class="btn btn-sm btn-warning">切换为: 对账模式</a>
                    @endif

                @if(count($records))
                    @if($auth->admin())
                <a href="/counter/finance/download/excel/branch" class="btn btn-success btn-sm">导出Excel</a>
                    @endif
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
            @if($mode == 'normal')
                    @if($record->user_id)
                @if($auth->sameBranch($record->user_id))
                <td><a href="/user/{{ $record->user_id }}" class="btn btn-block btn-info btn-xs">{{ $record->user_id_text }}</a></td>
                @else
                <td>{{ $record->user_id_text }}</td>
                @endif
                    @else
                        <td>其他(无推荐人的)</td>
                    @endif
            @else
                @if($record->user_id)
                <td><a href="/user/{{ $record->user_id }}" class="btn btn-block btn-info btn-xs">{{ $record->user_id_text }}</a></td>
                @else
                <td>其他(无推荐人的)</td>
                @endif
            @endif
                
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
        @endif
    </table>
    @endif
@endsection