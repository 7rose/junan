@extends('../nav')

@section('container')
    @if(isset($records))
        
        <table class="table table-striped">
          <caption>车辆 <a href="/biz_logs/download/excel" class="btn btn-success btn-sm"><span class="glyphicon glyphicon-th-list"></span> 下载Excel </a></caption>
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
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
            </tr>
            @endforeach
          </tbody>
        </table>
        <div style="text-align:center;">{{ $records->links() }}</div>
    @else
    @endif
@endsection