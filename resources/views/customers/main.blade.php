<?php
    $auth = new App\Helpers\Auth;
    $show = new App\Helpers\Show;
    $pre = new App\Helpers\Pre;
    $date = new App\Helpers\Date;
?>
@extends('../nav')

@section('container')
    @if(isset($records))
        
        <table class="table table-striped table-hover">
          <caption><h4>学员 <span class="glyphicon glyphicon-leaf"></h4>
            @if(!$auth->admin())
              <a href="/customer/create" class="btn btn-sm btn-success">+ 新学员</a>&nbsp&nbsp
            @endif

            @if($auth->admin())
            <div class="btn-group">
                <a href="/customer/download/excel" class="btn btn-success btn-sm"><span class="glyphicon glyphicon-th-list"></span> 下载Excel </a>
            </div>
            @endif
        </caption>
          <caption>
              <form role="form" class="form-inline" method="post" action="/customer/seek">
                {{ csrf_field() }}
                  <div class="form-group">
                    <input type="text" class="form-control input-sm" name="key" placeholder="关键词" value="{{ Session::has('customer_key') ? Session::get('customer_key') : '' }}">
                  </div>

                  <button type="submit" class="btn btn-info btn-sm"><span class="glyphicon glyphicon-search"></span> 查询 </button>
                @if(Session::has('customer_date_start') || Session::has('customer_date_end') || Session::has('customer_key'))
                    <a href="/customer/seek/reset" class="btn btn-warning btn-sm"> 重置查询条件</a>
                @endif
                </form>
          </caption>
          @if(count($records))
          <thead>
            <tr>
                <th>姓名</th>
                <th>手机</th>
                <th>出生日期</th>
                <th>身份证</th>
                <th>身份证地址</th>
                <th>创建日期</th>
                <th>财务</th>
                <th>业务</th>
            </tr>
          </thead>
          <tbody>
            @foreach($records as $record)
            <tr class="{{ $record->finance_info > 0 ? 'warning' : 'default' }}">
                <td><a class="btn btn-xs btn-block btn-{{ $record->finance_info > 0 ? 'warning' : 'default' }}"  href="/customer/{{ $record->id }}" >{!! $show->seekString('customer_key', $record->name) !!}</a></td>
                <td>{!! $show->seekString('customer_key', $record->mobile) !!}</td>
                <td>{{ $date->birthdayFromId($record->id_number)}}&nbsp&nbsp<span class="label label-{{ $record->gender == 2 ? 'danger' : 'default' }}">{{ $date->ageFromId($record->id_number) }}</span></td>
                <td>{!! $show->seekString('customer_key', $record->id_number) !!}</td>
                <td>{!! $show->seekString('customer_key', $record->address) !!}</td>
                <td>{{ $record->created_at }}</td>
                <td>{!! '¥'.$show->seekString('customer_key', $record->finance_info) !!}</td>
                <td id="claim_msg{{ $record->id }}">{!! $pre->getBiz($record) !!}</td>
            </tr>
            @endforeach
          </tbody>
          @else
          <tr><td>无记录: 数据库为空或是没有符合查询条件的记录</td></tr>
          @endif
        </table>
        <div style="text-align:center;">{{ $records->links() }}</div>
    @else
    @endif

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
        var msg = "您确定认领这个学员吗?此操作无法自行撤销, 只能申请管理员进行重新调配!!"
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