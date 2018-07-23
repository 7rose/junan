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

@endsection