@extends('../nav')

@section('container')
    <div class="col-md-6 col-md-offset-3">
       <div style="text-align: center" class="alert alert-{{ array_has($custom, 'color')? $custom['color'] : info}}">
        <h1><span class="glyphicon glyphicon-warning-sign"></span></h1>
            {{ array_has($custom, 'content')? $custom['content'] : '未知错误或授权'}}
        </div>
    <div class="col-md-6 col-md-offset-3">
@endsection