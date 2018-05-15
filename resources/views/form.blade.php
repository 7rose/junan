@extends('nav')

@section('container')
    <div class="col-md-6 col-md-offset-3">
        <div class="panel panel-default">
            <div class="panel-heading">
                @if(array_has($custom,'icon'))
                     <span class="glyphicon glyphicon-{{$custom['icon']}}"></span>
                @endif

                @if(array_has($custom,'title'))
                    {{$custom['title']}}
                @else
                    <span>+ 添加</span>
                @endif

            </div>
            <div class="panel-body">
                {!! form($form) !!}
            </div>
        </div>
    </div>
@endsection