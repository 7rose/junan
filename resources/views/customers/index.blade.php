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
        @if(isset($records))
        <table class="table table-hover">
        <caption>
            <a href="/customer/create" class="btn btn-sm btn-default">+ 新学员</a>&nbsp&nbsp
            <a href="/customer/download/excel" class="btn btn-sm btn-success"><span class="glyphicon glyphicon-th-list"></span>&nbsp&nbsp导出excel</a>&nbsp&nbsp
            @if($seek->seeking('seek_array', 'key'))
                <a href="/customer/seek/reset" class="btn btn-sm btn-warning">重置查询条件</a>&nbsp&nbsp
            @endif

        </caption>
            @if(count($records))
        <thead>
            <tr>
                <th>#</th>
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
                <td>{{ $record->id }}</td>
                <td><a class="btn btn-xs btn-block btn-{{ $record->finance_info > 0 ? 'warning' : 'default' }}"  href="/customer/{{ $record->id }}" >{!! $seek->seekLabel('seek_array', 'key', $record->name) !!}</a></td>
                <td>{!! $seek->seekLabel('seek_array', 'key', $record->mobile) !!}</td>
                <td>{{ $date->birthdayFromId($record->id_number)}}&nbsp&nbsp<span class="label label-{{ $record->gender == 2 ? 'danger' : 'default' }}">{{ $date->ageFromId($record->id_number) }}</span></td>
                <td>{!! $seek->seekLabel('seek_array', 'key', $record->id_number) !!}</td>
                <td>{!! $seek->seekLabel('seek_array', 'key', $record->address) !!}</td>
                <td>{!! '¥'.$seek->seekLabel('seek_array', 'key', $record->finance_info) !!}</td>
               
                <td id="claim_msg{{ $record->id }}">{!! $pre->customerBranch($record) !!}</td>
            </tr>
            @endforeach
        </tbody>
        @else
        <!-- 顶部间距 -->
            <div style="height: 20px"></div>
            <div class="alert alert-warning"><strong>无记录:</strong> 数据库尚无记录, 或者是没有符合查询条件记录.&nbsp&nbsp
                <a href="/customer/seek/reset" class="btn btn-sm btn-warning">重置查询条件</a></div>
        @endif
        
        </table>
            <div style="text-align:center;">{{ $records->links() }}</div>
        
            
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

<script>
    function claim(id, name)
    {
        var msg = "您正在认领序列号为"+id+"的学员, 此操作无法自行撤销, 只能申请管理员进行重新调配!!"
        $("#modal-msg").html(msg);

        var close_btn = '<button type=\"button\" class=\"btn btn-sm btn-default\" data-dismiss=\"modal\">关闭</button>';
        var click_btn = '<button type=\"button\" class=\"btn btn-sm btn-danger\" onClick=\"javascript:claim_ex('+id+')\">确定认领</button>';
        $("#modal-btn").html(close_btn+click_btn);

        $("#myModal").modal();
    }

    function claim_ex(id)
    {
        var post_url = "/biz/claim";
        var post_data = {id:id};

        $.post(
            post_url,
            post_data,
            function(message){
                $("#modal-msg").html(message);
                $("#claim_msg"+id).html("<span class=\"label label-info\">"+message+"</span>");
           }
        );
    }
</script>

@endsection


















