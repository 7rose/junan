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
                {{ config('ginkgo.name') }}: {{ Session::has('date_range') ? Session::get('date_range')['text'] : '' }}财务记录:{{ $all['total_num'] }}, 总营收: ¥{{ $all['total'] }}
                    @if($mode == 'normal')
                <a href="/filter/counter_finance_mode/real" class="btn btn-sm btn-info">切换为: 贡献模式</a>
                    @elseif($mode == 'real')
                <a href="/filter/counter_finance_mode/normal" class="btn btn-sm btn-warning">切换为: 对账模式</a>
                    @endif
                @if(count($records))
                    @if($auth->admin())
                <a href="/counter/finance/download/excel/all" class="btn btn-success btn-sm">导出Excel</a>
                    @endif
                @endif
                <div class="dropdown pull-right">
                    <button type="button" class="btn btn-sm dropdown-toggle" id="dropdownMenu1" data-toggle="dropdown">{{ Session::has('date_range') ? Session::get('date_range')['text'] : '选择' }}
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
                        <li role="presentation">
                            <a role="menuitem" tabindex="-1" href="/counter/set/today-finance">今天 - {{ $carbon->now()->day }}日</a>
                        </li>
                        <li role="presentation">
                            <a role="menuitem" tabindex="-1" href="/counter/set/week-finance">本周</a>
                        </li>
                        <li role="presentation">
                            <a role="menuitem" tabindex="-1" href="/counter/set/month-finance">本月 - {{ $carbon->now()->month }}月份</a>
                        </li>
                        <li role="presentation">
                            <a role="menuitem" tabindex="-1" href="/counter/set/year-finance">本年度 - {{ $carbon->now()->year }}年</a>
                        </li>
                        <li role="presentation" class="divider"></li>
                        <li role="presentation">
                            <a role="menuitem" tabindex="-1" href="/counter/set/pre_month-finance">上个月</a>
                        </li>
                        <li role="presentation" class="divider"></li>
                        <li role="presentation">
                            <a role="menuitem" tabindex="-1" href="javascript:manual()"><span class="glyphicon glyphicon-resize-horizontal"></span> 自定义时间段</a>
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
                <td><a href="/counter/finance/{{ $record->branch }}" class="btn btn-block btn-info btn-xs">{{ $record->branch_text }}</a></td>
                @else
                    @if($record->real_branch)
                <td><a href="/counter/finance/{{ $record->real_branch }}" class="btn btn-block btn-info btn-xs">{{ $record->real_branch_text }}</a></td>
                    @else
                <td>其他(无推荐人项)</td>
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

    {{-- 时间选择 --}}
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">自定时统计时间段</h4>
            </div>
            <div class="modal-body">
                <input type="date" id="date_start" required="required"> 至
                <input type="date" id="date_end" required="required">
            </div>
            <div class="modal-footer">
                <a class="btn btn-default btn-sm" data-dismiss="modal">关闭</a>
                <a class="btn btn-info btn-sm" href="javascript:post_set()">设定时间段</a>
            </div>
        </div>
    </div>

    <script>
        function manual() {
            $('#myModal').modal();
        }

        function post_set() {
            var date_start = $("#date_start").val();
            var date_end = $("#date_end").val();

            if(date_start == '' || date_end == '') {
                alert('起止时间均不得为空!');
                return false;
            }
            if(date_start >= date_end) {
                alert('截止时间不得小于或等于起始时间!');
                return false;
            }

            var post_url = "/counter/post_set";
            var post_data = {date_start:date_start, date_end:date_end};

            $.post(
                post_url,
                post_data,
                function(message){
                    location.reload();
                    // $("#modal-msg").html(message);
                    // $("#checking"+id).html("<span class=\"label label-success\">"+message+"</span>");
               }
            );
        }
    </script>

@endsection














