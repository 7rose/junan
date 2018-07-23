<?php
    $auth = new App\Helpers\Auth;
    $show = new App\Helpers\Show;
?>
@extends('../nav')

@section('container')
    @if(isset($records))
        
        <table class="table table-striped table-hover">
          <caption><h4>成员 <span class="glyphicon glyphicon-user"></h4>
            @if($auth->admin())
            <div class="btn-group">
                <a href="/finance/download/excel" class="btn btn-success btn-sm"><span class="glyphicon glyphicon-th-list"></span> 下载Excel </a>
            </div>
            @endif
        </caption>
          <caption>
              <form role="form" class="form-inline" method="post" action="/user/seek">
                {{ csrf_field() }}
                  <div class="form-group">
                    <input type="text" class="form-control input-sm" name="key" placeholder="关键词" value="{{ Session::has('user_key') ? Session::get('user_key') : '' }}">
                  </div>

                  <button type="submit" class="btn btn-info btn-sm"><span class="glyphicon glyphicon-search"></span> 查询 </button>
                @if(Session::has('user_date_start') || Session::has('user_date_end') || Session::has('user_key'))
                    <a href="/user/seek/reset" class="btn btn-warning btn-sm"> 重置查询条件</a>
                @endif
                </form>
          </caption>
          @if(count($records))
          <thead>
            <tr>
                <th>工号</th>
                <th>姓名</th>
                <th>手机</th>
                <th>机构</th>
                <th>类型</th>
                <th>创建日期</th>
            </tr>
          </thead>
          <tbody>
            @foreach($records as $record)
            <tr class="{{ $auth->self($record->id) ? 'info' : 'default' }}">
              <td>{!! $show->seekString('user_key', $record->work_id) !!}</td>
                <td>{!! '<a class="btn btn-xs btn-block btn-'.$auth->authColor($record->auth_type).'" href="/user/'.$record->id.'">'. $show->seekString('user_key', $record->name).'</a>' !!}</td>
                <td>{!! $show->seekString('user_key', $record->mobile) !!}</td>
                <td>{!! $show->seekString('user_key', $record->branch_text) !!}</td>
                <td>{!! $show->seekString('user_key', $record->user_type_text) !!}</td>
                <td>{{ $record->created_at ? $record->created_at : '-' }}</td>   
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

    // 登记
    function check(id) {
        var no = $("#no"+id).val();
        var trim_no = no.replace(/(^\s*)|(\s*$)/g, ""); 

        if(trim_no == '') {
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
                location.reload();
                // $("#modal-msg").html(message);
                // $("#checking"+id).html("<span class=\"label label-success\">"+message+"</span>");
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
                location.reload();
                // $("#modal-msg").html(message);
                // $("#check_2_msg"+id).html("<span class=\"label label-info\">"+message+"</span>");
           }
        );
    }

    // 撤销
    function cancel(id)
    {
        var msg = "您即将撤销序号为"+id+"的单据号及输入记录, 此操作本身无法撤销, 请谨慎操作!!"
        $("#modal-msg").html(msg);

        var close_btn = '<button type=\"button\" class=\"btn btn-sm btn-default\" data-dismiss=\"modal\">关闭</button>';
        var click_btn = '<button type=\"button\" class=\"btn btn-sm btn-danger\" onClick=\"javascript:cancel_ex('+id+')\">确定撤销!</button>';
        $("#modal-btn").html(close_btn+click_btn);

        $("#myModal").modal();
    }

    function cancel_ex(id)
    {
        var post_url = "/finance/cancel";
        var post_data = {id:id};

        $.post(
            post_url,
            post_data,
            function(message){
                location.reload();
                // $("#modal-msg").html(message);
                // $("#check_2_msg"+id).html("<span class=\"label label-info\">"+message+"</span>");
           }
        );
    }

    // 废弃
    function abandon(id)
    {
        var msg = "您即将废弃序号为"+id+"的单据, 废弃后的记录仍会显示在列表中, 以红色背景标注, 废弃后的数据不计入统计; 此操作本身无法撤销, 请谨慎操作!!"
        $("#modal-msg").html(msg);

        var close_btn = '<button type=\"button\" class=\"btn btn-sm btn-default\" data-dismiss=\"modal\">关闭</button>';
        var click_btn = '<button type=\"button\" class=\"btn btn-sm btn-danger\" onClick=\"javascript:abandon_ex('+id+')\">确定废弃!</button>';
        $("#modal-btn").html(close_btn+click_btn);

        $("#myModal").modal();
    }

    function abandon_ex(id)
    {
        var post_url = "/finance/abandon";
        var post_data = {id:id};

        $.post(
            post_url,
            post_data,
            function(message){
                location.reload();
                // $("#modal-msg").html(message);
                // $("#check_2_msg"+id).html("<span class=\"label label-info\">"+message+"</span>");
           }
        );
    }

</script>
@endsection