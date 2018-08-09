@extends('../nav')

@section('container')
    @if(isset($all))
    <div class="col-md-8 col-md-offset-2">
       <div class="alert alert-success">
           <ol>
               <li>进度导入包含认领学员功能</li>
               <li>进度批量导入内容不能撤销,只能由技术客服恢复或者逐条修正!</li>
               <li>数据有效性已经严密校验</li>
           </ol>
           <h4>共{{ $all }}条数据通过校验,可以写入开班数据库</h4>


           @if($all == 0)
              <div class="alert alert-danger"> 
                没有通过校验的内容可以导入!
              </div>
           @else
              <a href="/import/step/save" class="btn btn-sm btn-success"><span class="glyphicon glyphicon-ok"></span>&nbsp写入数据库</a>
           @endif
              <a href="/import/step" class="btn btn-sm btn-default"><span class="glyphicon glyphicon-remove"></span>&nbsp放弃并返回</a>&nbsp&nbsp

       </div>
    <div class="col-md-6 col-md-offset-3">
    @else
    <div class="alert alert-danger">
        缺少必要参数
    </div>
    @endif
@endsection