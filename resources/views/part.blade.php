@extends('../nav')

@section('container')
    <div class="col-md-6 col-md-offset-3">
       <div style="text-align: center" class="alert alert-danger">
        <h1><span class="glyphicon glyphicon-warning-sign"></span></h1>
            @if(isset($txt))
                {!! $txt !!}
                <div><a class="btn btn-danger btn-block">同批提交至: 科目1预约</a></div>
            @endif
        </div>
    <div class="col-md-6 col-md-offset-3">
@endsection