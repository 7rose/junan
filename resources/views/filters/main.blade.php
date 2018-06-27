<?php

$part = new App\Helpers\Part;
$seek = new App\Helpers\Seek;
$key = Session::has('filter_key') ? Session::get('filter_key') : null;

$lesson_info = Session::has('score_date') ? date('Y-m-d', Session::get('score_date')) : '';
$lesson_info .= Session::has('score_lesson') ? ',  科目: '.Session::get('score_lesson') : '';

?>
@extends('../nav')

@section('container')

<div class="row">
    {{-- 主表 --}}
    <div class="col-sm-9">
        {{-- 查询 --}}
        <div class="alert alert-info">
            <div class="col-sm-3">
                @if($part->actionFromUrl())
                    <strong>{{ $part->actionText() ? $part->actionText() : $lesson_info }} : {{ count($records) }}人</strong>
                @endif
            </div>

            <div class="col-sm-3">
                @if(Session::has('filter_key'))
                    <form method="POST" action="/filter/seek/reset">
                        {{ csrf_field() }}
                        <input type="hidden"  name="path" value="{{ Request::path() }}">
                        <button type="submit" class="btn btn-sm btn-warning">重置查询条件</button>
                    </form>
                @endif
            </div>

            <form method="POST" action="/filter/seek/set">
                {{ csrf_field() }}
                <input type="hidden" name="path" value="{{ Request::path() }}">
                <div class="input-group input-group-sm col-sm-4">
                        <input  type="text" class="form-control" name="key" placeholder="请输入关键词...">
                        <span class="input-group-btn">
                            <button type="submit" class="btn btn-sm btn-info"><span class="glyphicon glyphicon-search"></span> 查询</button>
                        </span>
                </div>
            </form>
        </div>

        <div class="alert alert-default">
            <div class="btn-group">
                <a  class="btn  btn-sm btn-default dropdown-toggle" id="selector" 
                        data-toggle="dropdown">
                    科目1 <span class="caret"></span>
                </a>
                <ul class="dropdown-menu" role="menu" aria-labelledby="selector">
                    <li role="presentation"><a role="menuitem" tabindex="-1" href="/filter/ready_for_1">具备预约条件的</a></li>
                    <li role="presentation"><a role="menuitem" tabindex="-1" href="/filter/date_for_1">提交预约申请的</a></li>
                    <li role="presentation"><a role="menuitem" tabindex="-1" href="/filter/fail_for_1">不合格的</a></li>
                </ul>
            </div>

            <div class="btn-group">
                <a  class="btn  btn-sm btn-success dropdown-toggle" id="selector" 
                        data-toggle="dropdown">
                    科目 2 <span class="caret"></span>
                </a>
                <ul class="dropdown-menu" role="menu" aria-labelledby="selector">
                    <li role="presentation"><a role="menuitem" tabindex="-1" href="/filter/ready_for_2">具备预约条件的</a></li>
                    <li role="presentation"><a role="menuitem" tabindex="-1" href="/filter/date_for_2">提交预约申请的</a></li>
                    <li role="presentation"><a role="menuitem" tabindex="-1" href="/filter/fail_for_2">不合格的</a></li>
                </ul>
            </div>

            <div class="btn-group">
                <a  class="btn  btn-sm btn-warning dropdown-toggle" id="selector" 
                        data-toggle="dropdown">
                    科目 3 <span class="caret"></span>
                </a>
                <ul class="dropdown-menu" role="menu" aria-labelledby="selector">
                    <li role="presentation"><a role="menuitem" tabindex="-1" href="/filter/ready_for_3">具备预约条件的</a></li>
                    <li role="presentation"><a role="menuitem" tabindex="-1" href="/filter/date_for_3">提交预约申请的</a></li>
                    <li role="presentation"><a role="menuitem" tabindex="-1" href="/filter/fail_for_3">不合格的</a></li>
                </ul>
            </div>

            <div class="btn-group">
                <a  class="btn  btn-sm btn-danger dropdown-toggle" id="selector" 
                        data-toggle="dropdown">
                    科目 4 <span class="caret"></span>
                </a>
                <ul class="dropdown-menu" role="menu" aria-labelledby="selector">
                    <li role="presentation"><a role="menuitem" tabindex="-1" href="/filter/ready_for_4">具备预约条件的</a></li>
                    <li role="presentation"><a role="menuitem" tabindex="-1" href="/filter/date_for_4">提交预约申请的</a></li>
                    <li role="presentation"><a role="menuitem" tabindex="-1" href="/filter/fail_for_4">不合格的</a></li>
                </ul>
            </div>

            <div class="btn-group left-cell">
                <a  class="btn  btn-sm btn-info" href="/filter/no_class"> <span class="glyphicon glyphicon-ban-circle"></span> 未开班的</a>
            </div>
            <div class="btn-group">
                <a  class="btn  btn-sm btn-primary" href="/counter/lesson"><span class="glyphicon glyphicon-edit"></span> 成绩和考务流水</a>
            </div>
            @if(count($records))
            <div class="btn-group left-cell">
                <a href="/filter/download/excel" class="btn btn-sm btn-success"><span class="glyphicon glyphicon-save"></span> 导出Excel</a>
            </div>
            @endif
        </div>
        
    @if(count($records))
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>姓名</th>
                    <th>电话</th>
                    <th>身份证</th>
                    <th>驾校</th>
                    <th>教练</th>
                    <th>开班信息</th>
                    <th>证照类型</th>
                    <th>批处理标记</th>
                </tr>
            </thead>
            <tbody>
                @foreach($records as $record)
                    @if(isset($record->selected))
                    <tr class="{{ $record->selected ? 'danger' : 'default' }}">
                    @else
                    <tr>
                    @endif
                        <td>{!! $seek->seekKey($record->customer_name, $key) !!}</td>
                        <td>{!! $seek->seekKey($record->customer_mobile, $key) !!}</td>
                        <td>{!! $seek->seekKey($record->customer_id_number, $key) !!}</td>
                        <td>{!! $seek->seekKey($record->branch_text, $key) !!}</td>
                        <td>{!! $seek->seekKey($record->user_name, $key) !!}</td>
                        <td>{!! explode('(', $record->class_branch_text)[0].$seek->seekKey($record->class_no, $key) !!}</td>
                        <td>{{ $record->licence_type_text }}</td>
                        <td>
                    @if($part->actionFromUrl() != 'no_class')
                        @if(isset($record->selected) && $record->selected)
                            <form method="POST" action="/filter/cancel/{{ $record->id }}">
                                {{ csrf_field() }}
                                <input type="hidden"  name="url" value="{{ Request::fullUrl() }}">
                                <button class="btn btn-xs btn-block btn-default">取消标记<span class="glyphicon glyphicon-remove"></span></button>
                            </form>
                        @else
                            <form method="POST" action="/filter/select/{{ $record->id }}">
                                {{ csrf_field() }}
                                <input type="hidden"  name="url" value="{{ Request::fullUrl() }}">
                                <button class="btn btn-xs btn-block btn-danger">标记 <span class="glyphicon glyphicon-circle-arrow-right"></span></button>
                            </form>
                        @endif
                    @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div style="text-align:center;">{{ $records->links() }}</div>
    @else
        <!-- 顶部间距 -->
        <div style="height: 40px"></div>
        <div class="alert alert-warning"><strong>无记录:</strong> 数据库尚无记录, 或者是没有符合查询条件记录.&nbsp&nbsp
        </div>
    @endif 

    </div>

    {{-- 标记列表 --}}
    <div class="alert alert-danger col-sm-3">
        @if(count($records))
            @if($part->actionFromUrl() != 'no_class')
            <button  class="btn btn-sm btn-block btn-danger" data-toggle="modal" data-target="#myModal">批处理</button>
            @else
            <a href="/import/class" class="btn btn-sm btn-block btn-danger">导入开班花名册</a>
            @endif
        @else
        <button disabled="disabled" class="btn btn-sm btn-block btn-danger" data-toggle="modal" data-target="#myModal">批处理</button>
        @endif
        @if(isset($selected_records) && count($selected_records))
            <ol>
                @foreach($selected_records as $key)
                    <li class="li-pad" >
                            <form method="POST" action="/filter/cancel/{{ $key->id }}">
                                {{ csrf_field() }}
                                <input type="hidden" id="hi{{ $key->id }}" name="url" value="{{ Request::fullUrl() }}">
                                <button class="btn btn-xs btn-block btn-default">{{ $key->customer_name }} &nbsp&nbsp<span class="glyphicon glyphicon-remove"></span></button>
                            </form>
                    </li>
                @endforeach
            </ol>
        @else
            没有标记记录
        @endif
    </div>
</div>

{{-- 弹出窗口 --}}
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
                <h4 class="modal-title" id="myModalLabel">
                   批处理: 共{{ count($records) }}条记录, 其中标记{{ count($selected_records) }}条.
                </h4>
            </div>
            <div class="modal-body">
                <ol>
                    <li>注意: 批处理操作无法被批量撤销, 只能由管理员手工逐条处理, 务必谨慎操作!!</li>
                </ol>
                
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-default" data-dismiss="modal">关闭</button>
                <a href="/filter/ex1/{{ $part->actionFromUrl() }}" class="btn btn-sm btn-danger">未标记的处理, 标记的忽略</a>
                <a href="/filter/ex2/{{ $part->actionFromUrl() }}" class="btn btn-sm btn-warning">标记的处理, 未标记的忽略</a>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal -->

@endsection







