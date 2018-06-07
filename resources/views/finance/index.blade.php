<?php
    $date = new App\Helpers\Date;
    $seek = new App\Helpers\Seek;
    $auth = new App\Helpers\Auth;
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
            <a href="/finance/download/excel" class="btn btn-sm btn-success"><span class="glyphicon glyphicon-th-list"></span>&nbsp&nbsp导出excel</a>&nbsp&nbsp
            @if($seek->seeking('finance_seek_array', 'key') || $seek->seeking('finance_seek_array', 'branch') || $seek->seeking('finance_seek_array', 'date_begin') || $seek->seeking('finance_seek_array', 'date_end'))
                <a href="/finance/seek/reset" class="btn btn-sm btn-warning">重置查询条件</a>&nbsp&nbsp
            @endif
        </caption>
        <thead>
            <tr>
                <th>#</th>
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
                <th>登记票号</th>
                <th>审核</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $record)
 
            <tr class="{{ $record->in ? 'default' : 'danger' }}">
                <td>{{ $record->id}}</td>
                <td>{{ $record->in ? '+' : '-' }}</td>
                <td>{!! $seek->seekLabel('finance_seek_array', 'key', $record->branch_text) !!}</td>
                <td>{!! $seek->seekLabel('finance_seek_array', 'key', $record->item_text) !!}</td>
                <td>{!! $seek->seekLabel('finance_seek_array', 'key', $record->customer_id_text) !!}</td>
                <td>{!! $seek->seekLabel('finance_seek_array', 'key', $record->price) !!}</td>
                <td>{{ $record->real_price }}</td>
                <td>{{ $record->price - $record->real_price }}</td>
                <td>{{ $record->created_by_text }}</td>
                <td>{!! $seek->seekLabel('finance_seek_array', 'key', $record->user_id_text) !!}</td>
                <td>{{ date('Y-m-d', $record->date) }}</td>
                <td>
                    @if($record->checked)
                        <span class="btn btn-success btn-xs">
                            {{ $record->ticket_no.' - '.$record->checked_by_text.', '.date('Y-m-d h:m:s', $record->checked_by_time) }}
                        </span>
                    @else
                        @if($auth->user() || $auth->finance())
                            <div id="checking{{ $record->id }}" class="input-group input-group-sm">
                                <input id="no{{ $record->id }}" type="text" class="form-control" placeholder="请输入票号...">
                                <span class="input-group-btn">
                                    <button class="btn btn-warning" type="button" onClick="javascript:check({{ $record->id }})">
                                        完成
                                    </button>
                                </span>
                            </div>
                        @else
                            <span class="label label-warning">未审核</span>
                        @endif
                    @endif
                </td>
                <td id="check_2_msg{{ $record->id }}">
                
                {{-- 审核必须完成登记 --}}
                @if($record->checked)
                    @if($record->checked_2)
                        <span class="label label-info">{{ $record->checked_2_by_name.','.date('Y-m-d h:m:s', $record->checked_2_by_time) }}</span>
                    @else
                        @if($auth->financeMaster())
                            <button class="btn btn-danger btn-xs" type="button" onClick="javascript:check_2({{ $record->id }})">审核</button>
                        @else
                            <span class="label label-warning">未审核</span>
                        @endif
                    @endif
                @else
                    -
                @endif
                    
                </td>
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
{{-- 弹出窗口 --}}
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" 
                        aria-hidden="true">×
                </button>

            </div>
            <div class="modal-body" id='modal-msg'>
                msg
            </div>
            <div class="modal-footer" id='modal-btn'>
                btn
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

{{-- 审核 --}}
<script>

    // 登记
    function check(id) {
        var no = $("#no"+id).val();
        if(no == '') {
            alert('票号必须输入!');
            return false;
        }

        var msg = "您正在登记序列号为"+id+"的财务单据号, 此操作无法撤销, 请谨慎操作!!"
        $("#modal-msg").html(msg);

        var close_btn = '<button type=\"button\" class=\"btn btn-sm btn-default\" data-dismiss=\"modal\">关闭</button>';
        var click_btn = '<button type=\"button\" class=\"btn btn-sm btn-danger\" onClick=\"javascript:check_ex('+id+')\">确定登记!</button>';
        $("#modal-btn").html(close_btn+click_btn);

        $("#myModal").modal();
    }

    function check_ex(id) 
    {
        var no = $("#no"+id).val();
        var post_url = "/finance/checking";
        var post_data = {no:no, id:id};

        $.post(
            post_url,
            post_data,
            function(message){
                $("#modal-msg").html(message);
                $("#checking"+id).html("<span class=\"label label-success\">"+message+"</span>");
           }
        );
    }

    // 审核
    function check_2(id)
    {
        var msg = "您正在审核序列号为"+id+"的财务记录单据, 此操作无法撤销, 请谨慎操作!!"
        $("#modal-msg").html(msg);

        var close_btn = '<button type=\"button\" class=\"btn btn-sm btn-default\" data-dismiss=\"modal\">关闭</button>';
        var click_btn = '<button type=\"button\" class=\"btn btn-sm btn-danger\" onClick=\"javascript:check_2_ex('+id+')\">审核无误</button>';
        $("#modal-btn").html(close_btn+click_btn);

        $("#myModal").modal();
    }

    function check_2_ex(id)
    {
        var post_url = "/finance/check_2";
        var post_data = {id:id};

        $.post(
            post_url,
            post_data,
            function(message){
                $("#modal-msg").html(message);
                $("#check_2_msg"+id).html("<span class=\"label label-info\">"+message+"</span>");
           }
        );
    }

</script>

@endsection


















