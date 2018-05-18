<?php
    $date = new App\Helpers\Date;
    $seek = new App\Helpers\Seek;
    // $auth = new App\Helpers\Auth;
?>
@extends('../nav')

@section('container')
    <ul id="myTab" class="nav nav-tabs">
        <li class="active">
            <a href="#list" data-toggle="tab">{{ $seek->seeking('finance_seek_array', 'key') || $seek->seeking('finance_seek_array', 'branch') || $seek->seeking('finance_seek_array', 'date_begin') || $seek->seeking('finance_seek_array', 'date_end') ? '查询结果' : '全部' }}</a>
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
            <a href="#" class="btn btn-sm btn-success"><span class="glyphicon glyphicon-th-list"></span>&nbsp&nbsp导出excel</a>&nbsp&nbsp
            @if($seek->seeking('finance_seek_array', 'key') || $seek->seeking('finance_seek_array', 'branch') || $seek->seeking('finance_seek_array', 'date_begin') || $seek->seeking('finance_seek_array', 'date_end'))
                <a href="/finance/seek/reset" class="btn btn-sm btn-warning">重置查询条件</a>&nbsp&nbsp
            @endif
        </caption>
        <thead>
            <tr>
                <th>收/付</th>
                <th>驾校</th>
                <th>项目</th>
                <th>学员</th>
                <th>应收(付)</th>
                <th>实收(付)</th>
                <th>结果</th>
                <th>经手人</th>
                <th>推荐人</th>
                <th>时间</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $record)
 
            <tr class="{{ $record->in ? 'default' : 'danger' }}">
                <td>{{ $record->in ? '+' : '-' }}</td>
                <td>{{ $record->branch_text }}</td>
                <td>{{ $record->item_text }}</td>
                <td>{{ $record->customer_id_text }}</td>
                <td>{{ $record->price }}</td>
                <td>{{ $record->real_price }}</td>
                <td>{{ $record->price - $record->real_price }}</td>
                <td>{{ $record->created_by_text }}</td>
                <td>{{ $record->user_id_text }}</td>
                <td>{{ date('Y-m-d', $record->date) }}</td>
            </tr>
            @endforeach
        </tbody>
        </table>
            <div style="text-align:center;">{{ $records->links() }}</div>
        @else
            <!-- 顶部间距 -->
            <div style="height: 20px"></div>
            <div class="alert alert-warning"><strong>无记录:</strong> 数据库尚无记录, 或者是没有符合查询条件记录.&nbsp&nbsp
                <a href="/finance/seek/reset" class="btn btn-sm btn-warning">重置查询条件</a>
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