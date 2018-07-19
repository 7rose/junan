<?php
    $carbon = new \Carbon\Carbon;
    function setLevel($level)
    {
        switch ($level) {
            case 'info':
                return '<span style="color:#4fe078;"><span class="glyphicon glyphicon-leaf"></span></span>';
                break;

            case 'warning':
                return '<span style="color:#f7cf4f;"><span class="glyphicon glyphicon-fire"></span></span>';
                break;

            case 'danger':
                return '<span style="color:red;"><span class="glyphicon glyphicon-flash"></span></span>';
                break;
            
            default:
                return '<span class="glyphicon glyphicon-info-sign"></span>';
                break;
        }
    }
?>
@extends('../nav')

@section('container')
    @if(isset($records))
        
        <table class="table table-striped">
          <caption>系统日志 <a href="/biz_logs/download/excel" class="btn btn-success btn-sm"><span class="glyphicon glyphicon-th-list"></span> 下载Excel </a></caption>
          <caption>
              <form role="form" class="form-inline" method="post" action="/biz_logs/seek">
                {{ csrf_field() }}
                  <div class="form-group form-sm">
                    <input type="date" class="form-control input-sm" name="date_start" value="{{ Session::has('logs_date_start') ? Session::get('logs_date_start') : '' }}">
                  </div>
                  <div class="form-group">
                    <label for="name">起, 至</label>
                    <input type="date" class="form-control input-sm" name="date_end" value="{{ Session::has('logs_date_end') ? Session::get('logs_date_end') : '' }}">
                  </div>
                  <div class="form-group">
                    <label for="name"> + </label>
                    <input type="text" class="form-control input-sm" name="key" placeholder="关键词" value="{{ Session::has('logs_key') ? Session::get('logs_key') : '' }}">
                  </div>
                  <div class="form-group">
                    <label for="name"> + </label>
                    <select class="form-control input-sm" name="level">
                        <option value="all" {{ !Session::has('logs_level') ? 'selected="selected"' : '' }}>等级:全部</option>
                        <option value="info" {{ Session::has('logs_level') && Session::get('logs_level') == 'info' ? 'selected="selected"' : ''}}> 绿标 - 常规</option>
                        <option value="warning" {{ Session::has('logs_level') && Session::get('logs_level') == 'warning' ? 'selected="selected"' : ''}}> 黄标 - 重要</option>
                        <option value="danger" {{ Session::has('logs_level') && Session::get('logs_level') == 'danger' ? 'selected="selected"' : ''}}> 红标 - 紧急</option>
                    </select>
                  </div>

                  <button type="submit" class="btn btn-info btn-sm"><span class="glyphicon glyphicon-search"></span> 查询 </button>
                </form>
          </caption>
          <thead>
            <tr>
              <th>等级</th>
              <th>用户</th>
              <th>时间</th>
              <th>地点</th>
              <th>内容</th>
            </tr>
          </thead>
          <tbody>
            @foreach($records as $record)
            <tr>
              <td>{!! setLevel($record->level) !!}</td>
              <td><a href="/user/{{ $record->user_id }}"> {{ $record->user_name }}</a></td>
              <td>{{ $carbon->diffForHumans($record->created_at, true).'前:  '.$record->created_at }}</td>
              <td>{{ $record->from }}</td>
              <td>{{ $record->content }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
        <div style="text-align:center;">{{ $records->links() }}</div>
    @else
    @endif
@endsection