<?php
    $date = new App\Helpers\Date;
    $seek = new App\Helpers\Seek;
?>
@extends('../nav')

@section('container')
    <ul id="myTab" class="nav nav-tabs">
        <li class="active">
            <a href="#list" data-toggle="tab">{{ $seek->seeking('seek_array', 'key') ? '查询结果' : '全部' }}</a>
        </li>
        <li>
            <a href="#seek" data-toggle="tab">查询</a>
        </li>
    </ul>
    <div id="myTabContent" class="tab-content">
    <div class="tab-pane fade in active" id="list">
        @if(count($records))
        <table class="table table-hover">
        <caption>
            <a href="/customer/create" class="btn btn-sm btn-default">+ 新学员</a>&nbsp&nbsp
            <a href="#" class="btn btn-sm btn-success"><span class="glyphicon glyphicon-th-list"></span>&nbsp&nbsp导出excel</a>&nbsp&nbsp
            @if($seek->seeking('seek_array', 'key'))
                <a href="/customer/seek/reset" class="btn btn-sm btn-warning">重置查询条件</a>&nbsp&nbsp
            @endif
        </caption>
        <thead>
            <tr>
                <th>姓名</th>
                <th>手机</th>
                <th>出生日期</th>
                <th>身份证</th>
                <th>业务</th>
                <th>财务</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $record)
            <tr>
                <td><a class="btn btn-sm btn-{{ $record->gender == 2 ? 'danger' : 'default'}}"  href="/customer/{{ $record->id }}" >{!! $seek->seekLabel('seek_array', 'key', $record->name) !!}</a></td>
                <td>{!! $seek->seekLabel('seek_array', 'key', $record->mobile) !!}</td>
                <td>{{ $date->birthdayFromId($record->id_number)}}&nbsp&nbsp<span class="label label-{{ $record->gender == 2 ? 'danger' : 'default' }}">{{ $date->ageFromId($record->id_number) }}</span></td>
                <td>{!! $seek->seekLabel('seek_array', 'key', $record->id_number) !!}</td>
                <td>0</td>
                <td>0</td>
            </tr>
            @endforeach
        </tbody>
        </table>
            <div style="text-align:center;">{{ $records->links() }}</div>
        @else
            <!-- 顶部间距 -->
            <div style="height: 20px"></div>
            <div class="alert alert-warning"><strong>无记录:</strong> 数据库尚无记录, 或者是没有符合查询条件记录.&nbsp&nbsp
                <a href="/customer/seek/reset" class="btn btn-sm btn-warning">重置查询条件</a></div>
        @endif
    </div>
    <div class="tab-pane" id="seek">
        <!-- 顶部间距 -->
        <div style="height: 20px"></div>
        <div class="col-md-4 col-md-offset-4">
            <div class="panel panel-info">
            <div class="panel-body">
                {{-- 查询 --}}
                {!! form($form) !!}
            </div>
            </div>
        </div>
    </div>

@endsection