@extends('../nav')

@section('container')
    <div class="col-md-6 col-md-offset-3">
       <div style="text-align: center" class="alert alert-danger">
              批处理确认: 批处理无法撤销, 您确认继续操作吗? <a href="/filter" class="btn btn-xs btn-success">放弃</a>
              <div style="height: 20px"></div>
            @if(isset($post_url) && isset($btn_txt))
                <form method="POST" action="{{ $post_url }}">
                    {{ csrf_field() }}
                    <input type="hidden"  name="lesson" value="{{ $lesson }}">
                    @if(isset($date_input) && $date_input)
                    <label>若继续, 请输入日期</label>
                    <input class="form-control" type="date" name="date" required="required">
                    @endif
                    <div style="height: 20px"></div>
                    <button class="btn btn-sm btn-block btn-danger">{{ $btn_txt }} <span class="glyphicon glyphicon-ok"></span></button>
                </form>
    
            @endif
        </div>
    </div>
@endsection