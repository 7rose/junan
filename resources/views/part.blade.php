@extends('../nav')

@section('container')
    <div class="col-md-6 col-md-offset-3">
       <div style="text-align: center" class="alert alert-danger">
        <h1><span class="glyphicon glyphicon-warning-sign"></span></h1>
            @if(isset($txt))
                {!! $txt !!}
            <div style="height: 20px"></div>
                <form method="POST" action="{{ $post_url }}">
                        {{ csrf_field() }}
                        @if($date_input)
                            <label class="pull-left" for="date">考试日期:</label>
                            <input class="form-control" type="date" name="date" required="required">
                        @endif

                        @if($lesson)
                            <input type="hidden" name="lesson" value="{{ $lesson }}">
                        @endif

                        @if($order_date)
                            <input type="hidden" name="order_date" value="{{ $order_date }}">
                        @endif

                        <input type="hidden" name="all_id" value="{{ $all_id }}">
                        <input type="hidden" name="spec_id" value="{{ $spec_id }}">
                        <input type="hidden" name="diff_id" value="{{ $diff_id }}">
                        <div class="form-group">
                            <label class="pull-left" for="type">批处理规则:</label>
                            <select class="form-control" name="type">
                              <option value="1">排除: 忽略标记项目, 其他全部执行</option>
                              <option value="2">标记: 仅执行标记项目, 未标记的忽略</option>
                            </select>
                        </div>
                    <button type="submit" class="btn btn-block btn-danger">{{ $btn_txt }} </button>
                </form>
           
            @endif
        </div>
    <div class="col-md-6 col-md-offset-3">
@endsection