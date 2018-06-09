<?php
    $date = new App\Helpers\Date;
    $seek = new App\Helpers\Seek;
    $auth = new App\Helpers\Auth;
?>
@extends('../nav')

@section('container')
    <ul id="myTab" class="nav nav-tabs">
        <li class="active">
            @if(isset($records))
            <a href="#list" data-toggle="tab">{{ $seek->seeking('user_seek_array', 'key') || $seek->seeking('user_seek_array', 'branch') ? '查询结果 - '.count($records) : '全部 -'.count($records)}}</a>
            @endif
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
            <a href="/user/create" class="btn btn-sm btn-default">+ 新成员</a>&nbsp&nbsp
            <a href="/user/download/excel" class="btn btn-sm btn-success"><span class="glyphicon glyphicon-th-list"></span>&nbsp&nbsp导出excel</a>&nbsp&nbsp

            @if($seek->seeking('user_seek_array', 'key') || $seek->seeking('user_seek_array', 'branch'))
                <a href="/user/seek/reset" class="btn btn-sm btn-warning">重置查询条件</a>&nbsp&nbsp
            @endif
        </caption>
        <thead>
            <tr>
                <th>工号</th>
                <th>姓名</th>
                <th>手机</th>
                <th>机构</th>
                <th>类型</th>
                <th>累计业务量</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $record)
        @if($auth->self($record->id))
            <tr class="info">
        @else
            <tr class="{{ $record->locked ? 'warning' : 'default' }}">
        @endif
                <td>{!! $seek->seekLabel('user_seek_array', 'key', $record->work_id) !!}</td>
                <td>{!! '<a class="btn btn-xs btn-block btn-'.$auth->authColor($record->auth_type).'" href="/user/'.$record->id.'">'. $seek->seekLabel('user_seek_array', 'key', $record->name).'</a>' !!}</td>
                <td>{!! $seek->seekLabel('user_seek_array', 'key', $record->mobile) !!}</td>
                <td>{!! $seek->seekLabel('user_seek_array', 'key', $record->branch_text) !!}</td>
                <td>{!! $seek->seekLabel('user_seek_array', 'key', $record->user_type_text) !!}</td>
                <td>{!! $record->biz_num !!}</td>
            </tr>
            @endforeach
        </tbody>
        </table>
            <div style="text-align:center;">{{ $records->links() }}</div>
        @else
            <!-- 顶部间距 -->
            <div style="height: 20px"></div>
            <div class="alert alert-warning"><strong>无记录:</strong> 数据库尚无记录, 或者是没有符合查询条件记录.&nbsp&nbsp
                <a href="/user/seek/reset" class="btn btn-sm btn-warning">重置查询条件</a>
            </div>
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