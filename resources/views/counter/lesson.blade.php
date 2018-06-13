<?php

    $counter = new App\Helpers\Counter;
    //$carbon = new Carbon\Carbon;
?>

@extends('../nav')

@section('container')

<ul id="myTab" class="nav nav-tabs">
    <li class="active"><a href="#home" data-toggle="tab">考务流水</a></li>
    <li><a href="#ios" data-toggle="tab">统计</a></li>
</ul>

<div id="myTabContent" class="tab-content">
    <div class="tab-pane fade in active" id="home">
        @if(isset($records) && count($records))
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>日期</th>
                    <th>科目</th>
                    <th>驾校</th>
                    <th>结果</th>
                </tr>
            </thead>
            <tbody>
                @foreach($records as $record)
                <tr>
                    <td>{{ date('Y-m-d', $record->order_date) }}</td>
                    <td>{{ $record->lesson }}</td>
                    <td>{{ $record->branch_text }}</td>
                    <td>{!! $counter->lessonInfo($record) !!}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div style="text-align:center;">{{ $records->links() }}</div>
        @else
            <div class="alert alert-warning">无记录</div>
        @endif
    </div>
    <div class="tab-pane fade" id="ios">
        <div style="height: 40px"></div>
        <div class="col-sm-5">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        军安集团总体
                    </h3>
                </div>
                <div class="panel-body">
                    @if(isset($all) && count($all))
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>科目</th>
                                    <th>累计人次</th>
                                    <th>合格人次</th>
                                    <th>合格率</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($all as $a)
                                <tr>
                                    <td>{{ $a->lesson }}</td>
                                    <td>{!! $counter->lessonSum($a)['all'] !!}</td>
                                    <td>{!! $counter->lessonSum($a)['pass'] !!}</td>
                                    <td>{!! $counter->lessonSum($a)['percent'].'%' !!}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
        
        @if(isset($records_sum) && count($records_sum))
            <table class="table table-hover">
                <caption>
                    <a href="/counter/lesson/download/excel" class="btn btn-success btn-sm">下载Excel</a>
                </caption>
                <thead>
                    <tr>
                        <th>驾校</th>
                        <th>科目</th>
                        <th>累计人次</th>
                        <th>合格人次</th>
                        <th>合格率</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($records_sum as $sum)
                    <tr>
                        <td>{{ $sum->branch_text }}</td>
                        <td>{{ $sum->lesson }}</td>
                        <td>{!! $counter->lessonSum($sum)['all'] !!}</td>
                        <td>{!! $counter->lessonSum($sum)['pass'] !!}</td>
                        <td>{!! $counter->lessonSum($sum)['percent'].'%' !!}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        <div style="text-align:center;">{{ $records->links() }}</div>
        @else
            <div style="height: 20px"></div>
            <div class="alert alert-warning">无记录</div>
        @endif

    </div>
</div>

    
@endsection