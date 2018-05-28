<?php
    $date = new App\Helpers\Date;
    $seek = new App\Helpers\Seek;
    $auth = new App\Helpers\Auth;
    $pre = new App\Helpers\Pre;

?>
@extends('../nav')

@section('container')
    <ul id="myTab" class="nav nav-tabs">
        <li class="active">
            @if(isset($records))
            <a href="#list" data-toggle="tab">{{ $seek->seeking('seek_array', 'key') ? '查询结果 - '.count($records) : '全部 -'.count($records)}}</a>
            @endif
        </li>
        <li>
            <a href="#seek" data-toggle="tab">查询</a>
        </li>
    </ul>
    <div id="myTabContent" class="tab-content">
    <div class="tab-pane fade in active" id="list">
        @if(isset($records) && count($records))
        <table class="table table-hover">
        <caption>
            <a href="/customer/create" class="btn btn-sm btn-default">+ 新学员</a>&nbsp&nbsp
            <a href="/customer/download/excel" class="btn btn-sm btn-success"><span class="glyphicon glyphicon-th-list"></span>&nbsp&nbsp导出excel</a>&nbsp&nbsp
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
                <th>身份证地址</th>
                <th>财务</th>
                <th>业务</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $record)
            <tr class="{{ $record->finance_info > 0 ? 'warning' : 'default' }}">
                <td><a class="btn btn-sm btn-{{ $record->finance_info > 0 ? 'warning' : 'default' }}"  href="/customer/{{ $record->id }}" >{!! $seek->seekLabel('seek_array', 'key', $record->name) !!}</a></td>
                <td>{!! $seek->seekLabel('seek_array', 'key', $record->mobile) !!}</td>
                <td>{{ $date->birthdayFromId($record->id_number)}}&nbsp&nbsp<span class="label label-{{ $record->gender == 2 ? 'danger' : 'default' }}">{{ $date->ageFromId($record->id_number) }}</span></td>
                <td>{!! $seek->seekLabel('seek_array', 'key', $record->id_number) !!}</td>
                <td>{!! $seek->seekLabel('seek_array', 'key', $record->address) !!}</td>
                <td>{!! '¥'.$seek->seekLabel('seek_array', 'key', $record->finance_info) !!}</td>
               
                <td>{!! $pre->customerBranch($record) !!}</td>
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