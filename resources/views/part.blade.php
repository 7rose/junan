@extends('../nav')

@section('container')
    <div class="col-md-6 col-md-offset-3">
       <div style="text-align: center" class="alert alert-danger">
        <h1><span class="glyphicon glyphicon-warning-sign"></span></h1>
            @if(isset($txt))
                {!! $txt !!}
                <form method="POST" action="{{ $post_url }}">
                        {{ csrf_field() }}
                        <input type="hidden" name="all_id" value="{{ $all_id }}">
                        <input type="hidden" name="spec_id" value="{{ $spec_id }}">
                        <input type="hidden" name="diff_id" value="{{ $diff_id }}">
                        <div class="form-group">
                            <label for="name">批处理规则:</label>
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