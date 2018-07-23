<?php
    $auth = new App\Helpers\Auth;
?>
@extends('../nav')

@section('container')
    @if(isset($records))
        
        <table class="table table-striped table-hover">
          <caption><h4>车辆: 加班车 <span class="glyphicon glyphicon-tag"></h4>
            <div class="btn-group">
                <a href="/cars/income/create" class="btn btn-success btn-sm"> + 新业务</a>
            </div>
            @if($auth->admin())
            <div class="btn-group">
                <a href="/cars/incomes/excel" class="btn btn-success btn-sm"><span class="glyphicon glyphicon-th-list"></span> 下载Excel </a>
            </div>
            @endif
        </caption>
          <caption>
              <form role="form" class="form-inline" method="post" action="/cars/seek">
                {{ csrf_field() }}
                <input type="hidden" name="to_path" value="{{ Request::path() }}">
                  <div class="form-group form-sm">
                    <input type="date" class="form-control input-sm" name="date_start" value="{{ Session::has('cars_date_start') ? Session::get('cars_date_start') : '' }}">
                  </div>
                  <div class="form-group">
                    <label for="name">起, 至</label>
                    <input type="date" class="form-control input-sm" name="date_end" value="{{ Session::has('cars_date_end') ? Session::get('cars_date_end') : '' }}">
                  </div>
                  <div class="form-group">
                    <label for="name"> + </label>
                    <input type="text" class="form-control input-sm" name="key" placeholder="关键词" value="{{ Session::has('cars_key') ? Session::get('cars_key') : '' }}">
                  </div>

                  <button type="submit" class="btn btn-info btn-sm"><span class="glyphicon glyphicon-search"></span> 查询 </button>
                @if(Session::has('cars_date_start') || Session::has('cars_date_end') || Session::has('cars_key'))
                    <a href="/cars/seek/reset/incomes" class="btn btn-warning btn-sm"> 重置查询条件</a>
                @endif
                </form>
          </caption>
          @if(count($records))
          <thead>
            <tr>
              <th>#</th>
              <th>牌号</th>
              <th>类型</th>
              <th>驾校</th>
              <th>教练员</th>
              <th>开始时间</th>
              <th>时长</th>
              <th>价格</th>
              <th>票号</th>
              <th>操作人</th>
              <th>时间</th>
              <th>备注</th>
            </tr>
          </thead>
          <tbody>
            @foreach($records as $record)
            <tr class="{{ $record->abandon ? 'danger' : 'default' }}">
              <td>{{ $record->id }}</td>
              <td>{{ $record->car_no }}</td>
              <td>{{ $record->type_text }}</td>
              <td>{{ $record->branch_text }}</td>
              <td>{{ $record->user_name }}</td>
              <td>{{ date('Y-m-d H:i:s', $record->start) }}</td>
              <td>{{ $record->hours }}</td>
              <td>{{ $record->real_price }}</td>
              <td>{{ $record->ticket_no }}</td>
              <td>{{ $record->created_by_name }}</td>
              <td>{{ $record->created_at }}</td>
              <td>{{ $record->content }}</td>
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
@endsection